<?php

declare(strict_types=1);

namespace Classroom\Controller\Teacher;

use Classroom\Module\Config\Config;
use Classroom\Module\Factory\Factory;

class ControllerTeacherActiveLesson extends ControllerTeacherBase {
    
    /**
     * @var array $_conditionsList Default conditions list.
     */
    protected $_conditionsList = array('deleted' => false, 'disabled' => false,);
    
    /**
     * @var array $_studentSortingList Default sorting list for students.
     */
    protected $_studentSortingList = array('disabled' => 'asc', 'name' => 'asc');
    
    /**
     * @var array $_lessonSortingList Default sorting list for lessons.
     */
    protected $_lessonSortingList = array('disabled' => 'asc', 'id' => 'desc');
    
    /**
     * @var array $_itemsPerPage Items per page.
     */
    protected $_itemsPerPage = null;
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '/teacher/activeLesson';
    
    /**
     * @var array $_templateNames View templates' names.
     */
    protected $_templateNames = array(
        'list' => 'Teacher/ActiveLesson/list',
        'student' => 'Teacher/ActiveLesson/student',
        'lesson' => 'Teacher/ActiveLesson/lesson',
    );
    
    /**
     * @var string $_action Action paramether.
     */
    protected $_action = null;
    
    /**
     * @var string $_studentId Student id paramether.
     */
    protected $_studentId = null;
    
    /**
     * @var string $_lessonId Lesson id paramether.
     */
    protected $_lessonId = null;
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
        
        $this->_itemsPerPage = Config::instance()->get('teacher', 'itemsPerPage');
    }
    
    protected function addJsAndCssFiles() {
        parent::addJsAndCssFiles();
        
        $this->addCssFile('/css/ActiveLesson/ActiveLesson.css');
    }
    
    protected function actionIndex() {
        
        $get = $this->getRequest()->get;
        if (array_key_exists('action', $get)) {
            $this->_action = $get['action'];
        }
        if (array_key_exists('studentId', $get)) {
            $this->_studentId = $get['studentId'];
        }
        if (array_key_exists('lessonId', $get)) {
            $this->_lessonId = $get['lessonId'];
        }
        
        if ($this->_action === 'command' && $this->_studentId && $this->_lessonId) {
            $content = $this->innerActionCommand();
        } else if ($this->_action === 'lesson' && $this->_studentId && $this->_lessonId) {
            $content = $this->innerActionLesson();
        } else if ($this->_action === 'student' && $this->_studentId) {
            $content = $this->innerActionStudent();
        } else if ($this->_action === 'list') {
            $content = $this->innerActionList();
        } else if (empty($this->_action)) {
            $content = $this->innerActionList();
        } else {
            $content = '';
            $this->send404();
        }
        
        return $content;
    }
    
    protected function innerActionCommand() {
        $this->setAjaxMode(true);
        
        $result = array('error' => true);
        
        $user = $this->getAuth()->getUser();
        $activeLesson = Factory::instance()->createModel('ActiveLesson')
            ->getActiveLesson($user->id, $this->_studentId, $this->_lessonId);
        
        if ($activeLesson) {
            $changed = false;
            if (array_key_exists('commands', $this->getRequest()->post)) {
                $command = $this->getRequest()->post['commands'];
                 // Set the new commands.
                $time = (string) time();
                $activeLesson->teacherCommand = $command ? json_encode($command) : '';
                $activeLesson->teacherUpdated = $time;
                $activeLesson->updated = $time;
                $changed = true;
            } else {
                $command = '';
            }
            
            if ($activeLesson->studentCommand) {
                $studentCommand = json_decode($activeLesson->studentCommand);
                $activeLesson->studentCommand = ''; // The commands will be shown, clear it.
                $changed = true;
            } else {
                $studentCommand = '';
            }
            
            if (! $changed || $activeLesson->save()) {
                $result = array(
                    'commands' => $studentCommand,
                    'updated' => $activeLesson->studentUpdated,
                    'error' => false,
                );
            }
            
        }
        
        $content = json_encode($result);
        
        return $content;
    }
    
    protected function innerActionLesson() {
        if (array_key_exists('lesson', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['lesson']);
        }
        
        $this->addJsFile('/js/ActiveLesson/lesson.js');
        
        $user = $this->getAuth()->getUser();
        $activeLesson = Factory::instance()->createModel('ActiveLesson')
            ->getActiveLesson($user->id, $this->_studentId, $this->_lessonId);
        
        if ($activeLesson) {
            $activeLesson->updated = time();
            if ($activeLesson->save()) {
                $this->getView()->set('error', false);
                
                $this->getView()->set('teacher', $user);
                
                $student = Factory::instance()->createModel('User');
                $student->loadByPk($this->_studentId);
                $this->getView()->set('student', $student);
                
                $lesson = Factory::instance()->createModel('Lesson');
                $lesson->loadByPk($this->_lessonId);
                $this->getView()->set('lesson', $lesson);
            } else {
                $this->getView()->set('error', true);
            }
        } else {
            $this->getView()->set('error', true);
        }
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
    protected function innerActionList() {
        if (array_key_exists('list', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['list']);
        }
        
        if ($this->_studentId) {
            $currentPage = (int) $this->_studentId;
            if ($currentPage <= 0) {
                $currentPage = 1;
            }
        } else {
            $currentPage = 1;
        }
        
        $user = $this->getAuth()->getUser();
        $sortingList = array('disabled' => 'asc', 'name' => 'asc');
        $studentsList = $user->getStudentsList(
            $this->_itemsPerPage,
            ((int) $currentPage - 1) * (int) $this->_itemsPerPage,
            $this->_studentSortingList,
            array('disabled' => false)
            
        );
        
        $itemsCount = $user->getStudentsCount(array('disabled' => false));
        
        $pagesList = array();
        if ($itemsCount > 1) {
            $pagesCount = floor(($itemsCount - 1) / $this->_itemsPerPage);
            for ($i = 1; $i <= $pagesCount; $i++) {
                $pagesList[] = (string) $i;
            }
        } else {
            $pagesCount = 1;
        }
        
        $this->getView()->set('studentsList', $studentsList);
        $this->getView()->set('currentPage', $currentPage);
        $this->getView()->set('pagesCount', $pagesCount);
        $this->getView()->set('pagesList', $pagesList);
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
    protected function innerActionStudent() {
        if (array_key_exists('student', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['student']);
        }
        
        if ($this->_lessonId) {
            $currentPage = (int) $this->_lessonId;
            if ($currentPage <= 0) {
                $currentPage = 1;
            }
        } else {
            $currentPage = 1;
        }
        
        $studentConditionsList = $this->_conditionsList;
        $studentConditionsList['id'] = $this->_studentId;
        $studentConditionsList['isStudent'] = true;
        $modelStudent = Factory::instance()->createModel('User');
        $student = $modelStudent->getOneModel($studentConditionsList);
        
        $user = $this->getAuth()->getUser();
        $lessonConditionsList = $this->_conditionsList;
        $lessonConditionsList['studentId'] = $this->_studentId;
        $lessonConditionsList['teacherId'] = $user->id;
        $modelLesson = Factory::instance()->createModel('Lesson');
        $lessonsList = $modelLesson->getModelsList(
            $lessonConditionsList, 0, 0, $this->_lessonSortingList
        );
        
        $lessonsCount = $modelLesson->getCount(
            $lessonConditionsList
        );
        
        $pagesList = array();
        if ($lessonsCount > 1) {
            $pagesCount = floor(($lessonsCount - 1) / $this->_itemsPerPage);
            for ($i = 1; $i <= $pagesCount; $i++) {
                $pagesList[] = (string) $i;
            }
        } else {
            $pagesCount = 1;
        }
        
        $this->getView()->set('student', $student);
        $this->getView()->set('lessonsList', $lessonsList);
        $this->getView()->set('currentPage', $currentPage);
        $this->getView()->set('pagesCount', $pagesCount);
        $this->getView()->set('pagesList', $pagesList);
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
}

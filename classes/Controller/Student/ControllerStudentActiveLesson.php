<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerStudentBase.php');

class ControllerStudentActiveLesson extends ControllerStudentBase {
    
    /**
     * @var array $_templateNames View templates' names.
     */
    protected $_templateNames = array(
        'waiting' => 'Student/ActiveLesson/waiting',
        'lesson' => 'Student/ActiveLesson/lesson',
    );
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '/student/activeLesson';
    
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
     * @var int $_waitingTime Active lesson record shouldn't be older that. In seconds.
     */
    protected $_waitingTime = 60;
    
    protected function addJsAndCssFiles() {
        parent::addJsAndCssFiles();
        
        $this->addCssFile('/css/ActiveLesson/ActiveLesson.css');
    }
    
    protected function actionIndex() {
        
        $get = $this->getRequest()->get;
        if (array_key_exists('action', $get)) {
            $this->_action = $get['action'];
        }
        if (array_key_exists('teacherId', $get)) {
            $this->_teacherId = $get['teacherId'];
        }
        if (array_key_exists('lessonId', $get)) {
            $this->_lessonId = $get['lessonId'];
        }
        
        if ($this->_action === 'command' && $this->_teacherId && $this->_lessonId) {
            $content = $this->innerActionCommand();
        } else if ($this->_action === 'lesson' && $this->_teacherId && $this->_lessonId) {
            $content = $this->innerActionLesson();
        } else if ($this->_action === 'waiting') {
            $content = $this->innerActionWaitingAjax();
        } else if (empty($this->_action)) {
            $content = $this->innerActionWaitingPage();
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
            ->getActiveLesson($this->_teacherId, $user->id, $this->_lessonId);
        
        if ($activeLesson) {
            $changed = false;
            if (array_key_exists('commands', $this->getRequest()->post)) {
                $command = $this->getRequest()->post['commands'];
                 // Set the new commands.
                $time = (string) time();
                $activeLesson->studentCommand = $command ? json_encode($command) : '';
                $activeLesson->studentUpdated = $time;
                $activeLesson->updated = $time;
                $changed = true;
            } else {
                $command = '';
            }
            
            if ($activeLesson->teacherCommand) {
                $teacherCommand = json_decode($activeLesson->teacherCommand);
                $activeLesson->teacherCommand = ''; // The commands will be sent, clear it.
                $changed = true;
            } else {
                $teacherCommand = '';
            }
            
            if (! $changed || $activeLesson->save()) {
                $result = array(
                    'commands' => $teacherCommand,
                    'updated' => $activeLesson->teacherUpdated,
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
            ->getActiveLesson($this->_teacherId, $user->id, $this->_lessonId);
        
        if ($activeLesson) {
            $this->getView()->set('error', false);
            
            $user = $this->getAuth()->getUser();
            $this->getView()->set('student', $user);
            
            $teacher = Factory::instance()->createModel('User');
            $teacher->loadByPk($this->_teacherId);
            $this->getView()->set('teacher', $teacher);
            
            $lesson = Factory::instance()->createModel('Lesson');
            $lesson->loadByPk($this->_lessonId);
            $this->getView()->set('lesson', $lesson);
            
        } else {
            $this->getView()->set('error', true);
        }
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
    protected function innerActionWaitingAjax() {
        $this->setAjaxMode(true);
        
        $user = $this->getAuth()->getUser();
        $activeLesson = Factory::instance()->createModel('ActiveLesson')
            ->findForStudent($user->id, $this->_waitingTime);
        
        if ($activeLesson) {
            $result = array(
                'lessonId' => $activeLesson->lessonId,
                'teacherId' => $activeLesson->teacherId,
            );
        } else {
            $result = array();
        }
        
        $content = json_encode($result);
        
        return $content;
    }
    
    protected function innerActionWaitingPage() {
        if (array_key_exists('waiting', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['waiting']);
        }
        
        $this->addJsFile('/js/ActiveLesson/waiting.js');
        
        $user = $this->getAuth()->getUser();
        
        $this->getView()->set('student', $user);
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
}

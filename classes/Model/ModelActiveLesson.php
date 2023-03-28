<?php

declare(strict_types=1);

namespace Classroom\Model;

use Classroom\Module\Factory\Factory;

/**
 * Model lesson.
 * 
 * @property string|null $id Log's id.
 * @property string|null $lessonId Lesson's id. 
 * @property string|null $teacherId Teacher's id.
 * @property string|null $studentId Student's id.
 * @property string|null $teacherCommand Last teacher's command.
 * @property string|null $studentCommand Last student's command.
 * @property string|null $teacherUpdated Last teacher's command time (unix timestamp).
 * @property string|null $studentUpdated Last student's command time (unix timestamp).
 * @property string|null $updated Updated time (unix timestamp).
 */
class ModelActiveLesson extends ModelDatabase {
    
    /**
     * @var string $_table Name of database table.
     */
    protected $_table = 'active_lesson';
    
    /**
     * @var array $_propertiesList List of properties.
     */
    protected $_propertiesList = array(
        array('name' => 'id'),
        array('name' => 'lessonId', 'type' => self::TYPE_FK, 'fkModelName' => 'Lesson'),
        array('name' => 'teacherId', 'type' => self::TYPE_FK, 'fkModelName' => 'User'),
        array('name' => 'studentId', 'type' => self::TYPE_FK, 'fkModelName' => 'User'),
        array('name' => 'teacherCommand'),
        array('name' => 'studentCommand'),
        array('name' => 'teacherUpdated'),
        array('name' => 'studentUpdated'),
        array('name' => 'updated'),
    );
    
    /**
     * Gets existed or created new active lesson.
     */
    public function getActiveLesson(string $teacherId, string $studentId, string $lessonId): ?Model {
        if (! $teacherId || ! $studentId || ! $lessonId) {
            return null;
        }
        
        $conditionsList = array('teacherId' => $teacherId, 'studentId' => $studentId);
        $activeLesson = $this->getOneModel($conditionsList);
        if (! $activeLesson) {
            $activeLesson = Factory::instance()->createModel($this->getModelName());
            $activeLesson->teacherId = $teacherId;
            $activeLesson->studentId = $studentId;
            $activeLesson->lessonId = $lessonId;
            $activeLesson->updated = (string) time();
            if (! $activeLesson->save()) {
                $activeLesson = null;
            }
        } else {
            if ($activeLesson->lessonId !== $lessonId) {
                $activeLesson->lessonId = $lessonId;
                $activeLesson->updated = (string) time();
                if (! $activeLesson->save()) {
                    $activeLesson = null;
                }
            }
        }
        
        return $activeLesson;
    }
    
    /**
     * Gets existed active lesson for student.
     */
    public function findForStudent(string $studentId, int $waitingTime): ?Model {
        if (! $studentId || ! $waitingTime) {
            return null;
        }
        
        $conditionsList = array(
            'studentId' => $studentId,
            'updated' => array(
                'value' => time() - $waitingTime,
                'condition' => '>=',
            ),
        );
        $activeLesson = $this->getOneModel($conditionsList);
        
        return $activeLesson;
    }
    
    public function setTeacherCommand($command): bool {
        $result = false;
        
        $time = (string) time();
        $this->setRawProperty('teacherCommand', $command);
        $this->setRawProperty('teacherUpdated', $time);
        $this->setRawProperty('updated', $time);
        if ($this->save()) {
            $result = true;
        }
        
        return $result;
    }
    
    public function setStudentCommand($command): bool {
        $result = false;
        
        $time = (string) time();
        $this->setRawProperty('studentCommand', $command);
        $this->setRawProperty('teacherUpdated', $time);
        $this->setRawProperty('updated', $time);
        if ($this->save()) {
            $result = true;
        }
        
        return $result;
    }
}

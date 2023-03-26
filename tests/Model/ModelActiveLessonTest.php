<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

use Classroom\Module\Factory\Factory;
use ClassroomTest\UsesModelUserTrait;
use Classroom\Model\ModelActiveLesson;
use Classroom\Model\ModelUser;

include_once(dirname(__FILE__) . '/../init.php');

final class ModelActiveLessonTest extends ModelDatabase {
    use UsesModelUserTrait;

    protected string $_modelName = 'ActiveLesson';

    protected string $_password = 'password';

    protected string $_activeLessonId = '10';

    protected ?ModelUser $_teacher;

    protected ?ModelUser $_student;

    protected ?ModelActiveLesson $_activeLesson;

    public function setUp(): void {
        $this->_student = $this->createUser(
            $this->getUniqueLogin(),
            $this->_password
        );
        $this->_student->isStudent = true;
        $this->_student->save();

        $this->_teacher = $this->createUser(
            $this->getUniqueLogin(),
            $this->_password
        );
        $this->_student->isTeacher = true;
        $this->_student->save();

        $this->_activeLesson = Factory::instance()->createModel($this->_modelName);
    }

    public function testGetActiveLesson(): void {
        // Create new active lesson.
        $createdActiveLesson = $this->_activeLesson->getActiveLesson(
            $this->_teacher->getPk(),
            $this->_student->getPk(),
            $this->_activeLessonId
        );
        $this->assertTrue($createdActiveLesson instanceof \Classroom\Model\Model);

        // Find existing active lesson.
        $createdActiveLesson = $this->_activeLesson->getActiveLesson(
            $this->_teacher->getPk(),
            $this->_student->getPk(),
            $this->_activeLessonId
        );
        $this->assertTrue($createdActiveLesson instanceof \Classroom\Model\Model);
    }

    public function testFindForStudent(): void {
        $createdActiveLesson = $this->_activeLesson->getActiveLesson(
            $this->_teacher->getPk(),
            $this->_student->getPk(),
            $this->_activeLessonId
        );
        $foundActiveLesson = $this->_activeLesson->findForStudent(
            $this->_student->getPk(),
            100
        );
        $this->assertTrue($foundActiveLesson instanceof \Classroom\Model\Model);

        $foundActiveLesson = $this->_activeLesson->findForStudent(
            $this->_teacher->getPk(),
            100
        );
        $this->assertNull($foundActiveLesson);
    }

    public function testSetTeacherAndStudentCommand(): void {
        $createdActiveLesson = $this->_activeLesson->getActiveLesson(
            $this->_teacher->getPk(),
            $this->_student->getPk(),
            $this->_activeLessonId
        );

        $teacherCommand = 'Teacher command.';
        $studentCommand = 'Student command.';
        $createdActiveLesson->setTeacherCommand($teacherCommand);
        $createdActiveLesson->setStudentCommand($studentCommand);

        $this->assertEquals($createdActiveLesson->teacherCommand, $teacherCommand);
        $this->assertEquals($createdActiveLesson->studentCommand, $studentCommand);
    }

    protected function getTestData(): array {
        return [
            [
                'lessonId' => '11',
                'teacherId' => '12',
                'studentId' => '13',
                'teacherCommand' => 'Command 11',
                'studentCommand' => 'Command 12',
                'teacherUpdated' => '1679835056530',
                'studentUpdated' => '1679835056531',
                'updated' => '1679835056532',
            ],
            [
                'lessonId' => '21',
                'teacherId' => '22',
                'studentId' => '23',
                'teacherCommand' => 'Command 21',
                'studentCommand' => 'Command 22',
                'teacherUpdated' => '1679835056540',
                'studentUpdated' => '1679835056541',
                'updated' => '1679835056542',
            ],
        ];
    }
}
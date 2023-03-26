<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

final class ModelLessonTest extends ModelDatabase {
    protected string $_modelName = 'Lesson';

    protected function getTestData(): array {
        return [
            [
                'lessonTemplateId' => '11',
                'teacherId' => '12',
                'studentId' => '13',
                'caption' => 'Caption 1',
                'subject' => 'Subject 1',
                'keywords' => 'Keywords 1',
                'content' => 'Content 1',
                'disabled' => '0',
                'deleted' => '0',
            ],
            [
                'lessonTemplateId' => '21',
                'teacherId' => '22',
                'studentId' => '23',
                'caption' => 'Caption 2',
                'subject' => 'Subject 2',
                'keywords' => 'Keywords 2',
                'content' => 'Content 2',
                'disabled' => '1',
                'deleted' => '1',
            ],
        ];
    }
}

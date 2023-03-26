<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

final class ModelLessonTemplateTest extends ModelDatabase {
    protected string $_modelName = 'LessonTemplate';

    protected function getTestData(): array {
        return [
            [
                'teacherId' => '12',
                'caption' => 'Caption 1',
                'subject' => 'Subject 1',
                'keywords' => 'Keywords 1',
                'content' => 'Content 1',
                'disabled' => '0',
                'deleted' => '0',
            ],
            [
                'teacherId' => '22',
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

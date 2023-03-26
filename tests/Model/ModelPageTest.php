<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

include_once(dirname(__FILE__) . '/../init.php');

final class ModelPageTest extends ModelAccessRights {
    protected string $_modelName = 'Page';

    protected function getTestData(): array {
        return [
            [
                'caption' => 'Caption 1',
                'comment' => 'Comment 1',
                'url' => '/test1',
                'title' => 'Title 1',
                'keywords' => 'Keywords 1',
                'description' => 'Description 1',
                'content' => 'Content 1',
                'accessAdmin' => true,
                'accessTeacher' => true,
                'accessStudent' => true,
                'accessGuest' => true,
                'disabled' => 0,
                'deleted' => 0,
            ],
            [
                'caption' => 'Caption 2',
                'comment' => 'Comment 2',
                'url' => '/test2',
                'title' => 'Title 2',
                'keywords' => 'Keywords 2',
                'description' => 'Description 2',
                'content' => 'Content 2',
                'accessAdmin' => false,
                'accessTeacher' => false,
                'accessStudent' => false,
                'accessGuest' => false,
                'disabled' => 1,
                'deleted' => 1,
            ],
        ];
    }
}
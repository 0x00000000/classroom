<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

include_once(dirname(__FILE__) . '/../init.php');

final class ModelMenuItemTest extends ModelAccessRights {
    protected string $_modelName = 'MenuItem';

    protected function getTestData(): array {
        return [
            [
                'menuId' => '1',
                'caption' => 'Caption 1',
                'link' => '/test1',
                'accessAdmin' => true,
                'accessTeacher' => true,
                'accessStudent' => true,
                'accessGuest' => true,
                'position' => 1,
                'disabled' => 0,
                'deleted' => 0,
            ],
            [
                'menuId' => '1',
                'caption' => 'Caption 2',
                'link' => '/test2',
                'accessAdmin' => false,
                'accessTeacher' => false,
                'accessStudent' => false,
                'accessGuest' => false,
                'position' => 2,
                'disabled' => 1,
                'deleted' => 1,
            ],
        ];
    }
}
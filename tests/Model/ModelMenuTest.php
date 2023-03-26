<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

include_once(dirname(__FILE__) . '/../init.php');

final class ModelMenuTest extends ModelAccessRights {
    protected string $_modelName = 'Menu';

    protected function getTestData(): array {
        return [
            [
                'caption' => 'Caption 1',
                'variable' => 'Value 1',
                'accessAdmin' => true,
                'accessTeacher' => true,
                'accessStudent' => true,
                'accessGuest' => true,
                'disabled' => 0,
                'deleted' => 0,
            ],
            [
                'caption' => 'Caption 2',
                'variable' => 'Value 2',
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
<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

use PHPUnit\Framework\TestCase;

use Classroom\Module\Factory\Factory;

abstract class ModelDatabase extends TestCase {
    protected string $_modelName;

    public function testModelData(): void {
        $dataList = $this->getTestData();
        $pkList = [];
        foreach ($dataList as $dataKey => $data) {
            $model = Factory::instance()->createModel($this->_modelName);
            foreach ($data as $key => $value) {
                $model->$key = $value;
            }
            $pk = $model->save();
            $this->assertTrue(! empty($pk));
            $pkList[$dataKey] = $pk;
        }

        foreach ($pkList as $dataKey => $pk) {
            $model = Factory::instance()->createModel($this->_modelName);
            $model->loadByPk($pk);
            foreach ($dataList[$dataKey] as $key => $value) {
                $this->assertEquals($model->$key, $value);
            }
        }
    }

    abstract protected function getTestData(): array;
}

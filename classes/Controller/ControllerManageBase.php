<?php

declare(strict_types=1);

namespace Classroom\Controller;

use Classroom\Module\Factory\Factory;

use Classroom\Model\Model;
use Classroom\Model\ModelDatabase;

/**
 * Renders pages for managing database model items.
 */
abstract class ControllerManageBase extends ControllerBase {
    
    /**
     * @var string $_modelName Name of managed model class.
     */
    protected $_modelName = '';
    
    /**
     * @var array $_conditionsList Default conditions list.
     */
    protected $_conditionsList = array('deleted' => false,);
    
    /**
     * @var array $_sortingList Default sorting list.
     */
    protected $_sortingList = array('id' => 'desc',);
    
    /**
     * @var array $_itemsPerPage Items per page.
     */
    protected $_itemsPerPage = 20;
    
    /**
     * @var array $_templateNames View templates' names.
     */
    protected $_templateNames = array(
        'add' => 'Common/ManageBase/add',
        'list' => 'Common/ManageBase/list',
        'view' => 'Common/ManageBase/view',
        'edit' => 'Common/ManageBase/edit',
    );
    
    /**
     * @var array $_modelControlsList Defines controls for model properties.
     * 
     * propertyName => controlType
     * f. e.
     * 'deleted' => self::CONTROL_NONE,
     */
    protected $_modelControlsList = array();
    
    /**
     * @var string $_action Action paramether.
     */
    protected $_action = null;
    
    /**
     * @var string $_id Id paramether.
     */
    protected $_id = null;
    
    /**
     * Allow user execute actions.
     */
    protected $_canDo = array(
        'add' => true,
        'view' => true,
        'edit' => true,
        'delete' => true,
        'disable' => true,
        'list' => true,
    );
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
    }
    
    protected function actionIndex() {
        
        $get = $this->getRequest()->get;
        if (array_key_exists('action', $get)) {
            $this->_action = $get['action'];
        }
        if (array_key_exists('id', $get)) {
            $this->_id = $get['id'];
        }
        
        if ($this->_action === 'add' && $this->_canDo['add']) {
            $content = $this->innerActionAdd();
        } else if ($this->_action === 'view' && $this->_id && $this->_canDo['view']) {
            $content = $this->innerActionView();
        } else if ($this->_action === 'edit' && $this->_id && $this->_canDo['edit']) {
            $content = $this->innerActionEdit();
        } else if ($this->_action === 'delete' && $this->_id && $this->_canDo['delete']) {
            $content = $this->innerActionDelete();
        } else if ($this->_action === 'disable' && $this->_id && $this->_canDo['disable']) {
            $content = $this->innerActionDisable(); // Disables or enables item.
        } else if ($this->_action === 'list' && $this->_canDo['list']) {
            $content = $this->innerActionList();
        } else if (empty($this->_action)) {
            $content = $this->innerActionList();
        } else {
            $content = '';
            $this->send404();
        }
        
        return $content;
    }
    
    protected function setContentViewVariables() {
        parent::setContentViewVariables();
        
        $model = Factory::instance()->createModel($this->_modelName);
        $propertiesList = $model->getPropertiesList();
        $this->getView()->set('propertiesList', $propertiesList);
        $controlsList = $this->getModelControlsList($model);
        $this->getView()->set('controlsList', $controlsList);
        
        foreach ($propertiesList as $propertyName => $propertyData) {
            if (
                $propertyData['type'] === Model::TYPE_ENUM
                && array_key_exists('getValuesMethodName', $propertyData)
                && method_exists($model, $propertyData['getValuesMethodName'])
            ) {
                $methodName = $propertyData['getValuesMethodName'];
                $values = $model->$methodName();
                $this->getView()->set($propertyData['name'] . 'Values', $values);
            }
        }
        
        foreach ($this->_canDo as $name => $value) {
            $this->getView()->set('can' . ucfirst($name), $value);
        }
    }
    
    /**
     * Gets values list for foreign key field.
     * 
     * @param  string $fkPropertyName Foreign key property name.
     * @param  string $condition Condition for selecting values for field.
     * @param  string $fkModelCaptionField Name of linked model field.
     * @return array
     */
    protected function getFkValues(string $fkPropertyName, array $condition, string $fkModelCaptionField = 'name', $addEmptyValue = false): array {
        if ($addEmptyValue) {
            $itemsList = array('' => ' ');
        } else {
            $itemsList = array();
        }
        
        $model = Factory::instance()->createModel($this->_modelName);
        $propertiesList = $model->getPropertiesList();
        
        $defaultCondition = array('deleted' => false, 'disabled' => false);
        $fkModelName = $propertiesList[$fkPropertyName]['fkModelName'];
        $fkModel = Factory::instance()->createModel($fkModelName);
        $fkModelsList = $fkModel->getModelsList(
            array_merge($condition, $defaultCondition)
        );
        if ($fkModelsList) {
            foreach ($fkModelsList as $fkModelData) {
                $itemsList[$fkModelData->id] = $fkModelData->$fkModelCaptionField;
            }
        }
        
        return $itemsList;
    }
    
    protected function innerActionAdd() {
        if ($this->getFromPost('submit')) {
            $this->innerActionDoAdd();
        }
        
        if (array_key_exists('add', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['add']);
        }
        
        $model = Factory::instance()->createModel($this->_modelName);
        $this->getView()->set('model', $model);
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
    protected function innerActionDoAdd() {
        $model = Factory::instance()->createModel($this->_modelName);
        $canSave = $this->setPropertiesFromPost($model);
        if ($canSave) {
            if ($model->save()) {
                $this->setStashData('messageType', 'addedSuccessfully');
            } else {
                $this->setStashData('messageType', 'addingFailed');
            }
        } else {
            $this->setStashData('messageType', 'addingFailed');
        }
        $this->redirect($this->getBaseUrl());
        
    }
    
    protected function innerActionView() {
        if (array_key_exists('view', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['view']);
        }
        
        $conditionsList = $this->_conditionsList;
        $conditionsList['id'] = $this->_id;
        $item = null;
        $model = Factory::instance()->createModel($this->_modelName);
        $item = $model->getOneModel($conditionsList);
        
        if ($item) {
            $this->getView()->set('item', $item);
            $content = $this->getView()->render();
        } else {
            $this->setStashData('messageType', 'itemNotFound');
            $this->redirect($this->getBaseUrl());
        }
        
        return $content;
    }
    
    protected function innerActionEdit() {
        if ($this->getFromPost('submit')) {
            $this->innerActionDoEdit();
        }
        if (array_key_exists('edit', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['edit']);
        }
        
        $conditionsList = $this->_conditionsList;
        $conditionsList['id'] = $this->_id;
        $model = Factory::instance()->createModel($this->_modelName);
        $item = $model->getOneModel($conditionsList);
        
        if ($item) {
            $this->getView()->set('item', $item);
            $content = $this->getView()->render();
        } else {
            $this->setStashData('messageType', 'itemNotFound');
            $this->redirect($this->getBaseUrl());
        }
        
        return $content;
    }
    
    protected function innerActionDoEdit() {
        $conditionsList = $this->_conditionsList;
        $conditionsList['id'] = $this->_id;
        $model = Factory::instance()->createModel($this->_modelName);
        $item = $model->getOneModel($conditionsList);
        
        if ($item) {
            $canSave = $this->setPropertiesFromPost($item);
            
            if ($canSave) {
                if ($item->save()) {
                    $this->setStashData('messageType', 'editedSuccessfully');
                } else {
                    $this->setStashData('messageType', 'editingFailed');
                }
            } else {
                $this->setStashData('messageType', 'editingFailed');
            }
            
        } else {
            $this->setStashData('messageType', 'itemNotFound');
        }
        $this->redirect($this->getBaseUrl());
        
    }
    
    protected function innerActionDelete() {
        $this->innerActionDoDelete();
        
        return '';
    }
    
    protected function innerActionDoDelete() {
        if ($this->_id) {
            $conditionsList = $this->_conditionsList;
            $conditionsList['id'] = $this->_id;
            $model = Factory::instance()->createModel($this->_modelName);
            $item = $model->getOneModel($conditionsList);
            
            if ($item) {
                $item->deleted = true;
                if ($item->save()) {
                    $this->setStashData('messageType', 'deletedSuccessfully');
                } else {
                    $this->setStashData('messageType', 'deletingFailed');
                }
            } else {
                $this->setStashData('messageType', 'itemNotFound');
            }
        } else {
            $this->setStashData('messageType', 'wrongParamethers');
        }
        
        $this->redirect($this->getBaseUrl());
    }
    
    protected function innerActionDisable() {
        $this->innerActionDoDisable();
        
        return '';
    }
    
    protected function innerActionDoDisable() {
        if ($this->_id) {
            $conditionsList = $this->_conditionsList;
            $conditionsList['id'] = $this->_id;
            $model = Factory::instance()->createModel($this->_modelName);
            $item = $model->getOneModel($conditionsList);
            
            if ($item) {
                if ($item->disabled) {
                    $item->disabled = false;
                } else {
                    $item->disabled = true;
                }
                if ($item->save()) {
                    if ($item->disabled) {
                        $this->setStashData('messageType', 'disabledSuccessfully');
                    } else {
                        $this->setStashData('messageType', 'enabledSuccessfully');
                    }
                } else {
                    if ($item->disabled) {
                        $this->setStashData('messageType', 'disablingFailed');
                    } else {
                        $this->setStashData('messageType', 'enabledFailed');
                    }
                }
            } else {
                $this->setStashData('messageType', 'itemNotFound');
            }
        } else {
            $this->setStashData('messageType', 'wrongParamethers');
        }
        
        $this->redirect($this->getBaseUrl());
    }
    
    protected function innerActionList() {
        if (array_key_exists('list', $this->_templateNames)) {
            $this->getView()->setTemplate($this->_templateNames['list']);
        }
        
        if ($this->_id) {
            $currentPage = (int) $this->_id;
            if ($currentPage <= 0) {
                $currentPage = 1;
            }
        } else {
            $currentPage = 1;
        }
        $currentPage = (string) $currentPage;
        
        $itemsList = array();
        $model = Factory::instance()->createModel($this->_modelName);
        $itemsList = $model->getModelsList(
            $this->_conditionsList,
            $this->_itemsPerPage,
            ((int) $currentPage - 1) * (int) $this->_itemsPerPage,
            $this->_sortingList
            
        );
        
        $modelsCount = $model->getCount(
            $this->_conditionsList
        );
        
        $pagesList = array();
        if ($modelsCount > 1) {
            $pagesCount = ceil($modelsCount / $this->_itemsPerPage);
            for ($i = 1; $i <= $pagesCount; $i++) {
                $pagesList[] = (string) $i;
            }
        } else {
            $pagesCount = 1;
        }
        
        $this->getView()->set('itemsList', $itemsList);
        $this->getView()->set('currentPage', $currentPage);
        $this->getView()->set('pagesCount', $pagesCount);
        $this->getView()->set('pagesList', $pagesList);
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
    /**
     * @var array $_modelControlsList Defines controls for model properties.
     * 
     * propertyName => controlType
     * f. e.
     * 'deleted' => self::CONTROL_NONE,
     */
    protected function getControlsList(ModelDatabase $model) {
        return $this->_modelControlsList;
    }
    
}

<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerBase.php');

/**
 * Renders pages for managing database model items.
 */
abstract class ControllerManageBase extends ControllerBase {
    
    /**
     * @var string $_modelName Name of manaaged model class.
     */
    protected $_modelName = '';
    
    protected $_action = null;
    
    protected $_id = null;
    
    protected function actionIndex() {
        
        $get = $this->getRequest()->get;
        if (array_key_exists('action', $get)) {
            $this->_action = $get['action'];
        }
        if (array_key_exists('id', $get)) {
            $this->_id = $get['id'];
        }
        
        if ($this->_action === 'view' && $this->_id) {
            $content = $this->innerActionView();
        } else if ($this->_action === 'edit' && $this->_id) {
            $content = $this->innerActionEdit();
        } else if ($this->_action === 'delete' && $this->_id) {
            $content = $this->innerActionDelete();
        } else if (empty($this->_action)) {
            $content = $this->innerActionList();
        } else {
            $content = '';
            $this->send404();
        }
        
        return $content;
    }
    
    protected function innerActionView() {
        return 'view';
    }
    
    protected function innerActionEdit() {
        return 'edit';
    }
    
    protected function innerActionDelete() {
        return 'delete';
    }
    
    protected function innerActionList() {
        if ($this->_id) {
            $page = $this->_id;
        }
        
        return 'list';
    }
    
    protected function innerActionLogin() {
        
        if (array_key_exists('login', $this->getRequest()->post)) {
            $this->postActionLogin();
        }
        
        $view = Factory::instance()->createView();
        $view->setTemplate('Guest/login');
        
        $view->set('url', $this->getUrl());
        
        $messageType = $this->popStashData('messageType');
        
        $view->set('messageType', $messageType);
        
        $content = $view->render();
        
        return $content;
    }
    
    protected function postActionLogin() {
        if ($this->getPost('login') && $this->getPost('password')) {
            sleep(1);
            if ($this->getAuth()->login($this->getPost('login'), $this->getPost('password'))) {
                $this->setStashData('messageType', '');
                $this->redirect();
            } else {
                $this->setStashData('messageType', 'loginFailed');
                $this->redirect();
            }
        }
    }
    
    protected function innerActionLogout() {
        
        if (array_key_exists('logout', $this->getRequest()->post)) {
            $this->postActionLogout();
        }
        
        $view = Factory::instance()->createView();
        $view->setTemplate('Guest/logout');
        
        $view->set('user', $this->getAuth()->getUser());
        $view->set('url', $this->getUrl());
        
        $content = $view->render();
        
        return $content;
    }
    
    protected function postActionLogout() {
        if ($this->getPost('logout')) {
            if ($this->getAuth()->logout()) {
                $this->redirect();
            }
        }
    }
    
}

<?php

declare(strict_types=1);

namespace classroom;

Factory::instance()->loadController('ControllerBase');

class ControllerUserLogin extends ControllerBase {
    
    protected function actionIndex() {
        
        if ($this->getAuth()->isGuest()) {
            $content = $this->innerActionLogin();
        } else {
            $content = $this->innerActionLogout();
        }
        
        return $content;
    }
    
    protected function innerActionLogin() {
        
        if (array_key_exists('login', $this->getRequest()->post)) {
            $this->innerActionDoLogin();
        }
        
        $this->getView()->setTemplate('User/login');
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
    protected function innerActionDoLogin() {
        if ($this->getPost('login') && $this->getPost('password')) {
            sleep(1);
            if ($this->getAuth()->login($this->getPost('login'), $this->getPost('password'))) {
                $this->setStashData('messageType', '');
                $this->redirect($this->getRootUrl());
            } else {
                $this->setStashData('messageType', 'loginFailed');
                $this->redirect();
            }
        }
    }
    
    protected function innerActionLogout() {
        
        if (array_key_exists('logout', $this->getRequest()->post)) {
            $this->innerActionDoLogout();
        }
        
        $this->getView()->setTemplate('User/logout');
        
        $this->getView()->set('user', $this->getAuth()->getUser());
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
    protected function innerActionDoLogout() {
        if ($this->getPost('logout')) {
            if ($this->getAuth()->logout()) {
                $this->redirect($this->getRootUrl());
            }
        }
    }
    
}

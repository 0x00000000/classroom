<?php

declare(strict_types=1);

namespace Classroom\Controller\Guest;

use Classroom\Module\Factory\Factory;

class ControllerGuestLogin extends ControllerGuestBase {
    
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
        if ($this->getFromPost('login') && $this->getFromPost('password')) {
            sleep(1);
            if ($this->getAuth()->login($this->getFromPost('login'), $this->getFromPost('password'))) {
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
        if ($this->getFromPost('logout')) {
            if ($this->getAuth()->logout()) {
                $this->redirect();
            }
        }
    }
    
}

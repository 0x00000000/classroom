<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerTeacherBase.php');

class ControllerTeacherLesson extends ControllerTeacherBase {
    
    protected $_message = '';
    
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
        $view->setTemplate('Guest/Login/Login');
        
        $content = $view->render();
        
        return $content;
    }
    
    protected function postActionLogin() {
        if ($this->getPost('login') && $this->getPost('password')) {
            sleep(1);
            if ($this->getAuth()->login($this->getPost('login'), $this->getPost('password'))) {
                $this->redirect();
            }
        }
    }
    
    protected function innerActionLogout() {
        
        if (array_key_exists('logout', $this->getRequest()->post)) {
            $this->postActionLogout();
        }
        
        $view = Factory::instance()->createView();
        $view->setTemplate('Guest/Login/Logout');
        
        $view->set('user', $this->getAuth()->getUser());
        
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

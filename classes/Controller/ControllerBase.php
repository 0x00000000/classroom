<?php

declare(strict_types=1);

namespace classroom;

include_once('Controller.php');

/**
 * Executes actions and renders page.
 */
abstract class ControllerBase extends Controller {
    
    /**
     * Request object.
     */
    protected $_request = null;
    
    /**
     * Response object.
     */
    protected $_response = null;
    
    /**
     * Auth object.
     */
    protected $_auth = null;
    
    /**
     * View object.
     */
    protected $_view = null;
    
     /**
     * Class constructor.
     */
    public function __construct() {
        $this->_view = Factory::instance()->createView();
        $this->_view->setTemplate('page');
    }
    
    /**
     * Initializes controller.
     */
    public function init(Request $request, Response $response): void {
        $this->setRequest($request);
        $this->setResponse($response);
        $this->setAuth(Factory::instance()->createAuth($this->getRequest()));
    }
    
    /**
     * Execute controller action.
     */
    public function execute(string $action): void {
        $methodName = 'action' . ucfirst($action);
        if (strlen($action) && method_exists($this, $methodName)) {
            $this->setVariables();
            
            $this->before();
            $content = $this->$methodName();
            
            $this->setVariable('content', $content);
            $this->after();
            $this->setVariable('minMenuItems', $this->getMenuItems());
            $this->printPage();
        } else {
            $this->send404();
        }
    }
    
    /**
     * Redirects UA to url and stops script execution.
     */
    protected function redirect(string $url = null): void {
        if (! $url) {
            if ($this->getRequest()) {
                $url = $this->getRequest()->url;
            }
        }
        if ($url) {
            $this->getResponse()->setHeader('Location', $url);
            $this->getResponse()->setBody('');
            $this->getResponse()->send();
        }
        
        exit;
    }
    
    /**
     * Sends 404 response to UA.
     */
    protected function send404(): void {
        $this->getResponse()->send404();
    }
    
    /**
     * Executes before controller action.
     */
    protected function before(): void {
        
    }
    
    /**
     * Executes after controller action.
     */
    protected function after(): void {
        
    }
    
    /**
     * Renders and prints page.
     */
    protected function printPage(): void {
        $html = $this->_view->render();
        
        $this->getResponse()->setBody($html);
        $this->getResponse()->send();
    }
    
    /**
     * Gets request.
     */
    protected function getRequest(): ?Request {
        return $this->_request;
    }
    
    /**
     * Sets request.
     */
    protected function setRequest(Request $request) {
        $this->_request = $request;
    }
    
    /**
     * Gets response.
     */
    protected function getResponse(): ?Response {
        return $this->_response;
    }
    
    /**
     * Sets response.
     */
    protected function setResponse(Response $response) {
        $this->_response = $response;
    }
    
    /**
     * Gets auth.
     */
    protected function getAuth(): ?Auth {
        return $this->_auth;
    }
    
    /**
     * Sets auth.
     */
    protected function setAuth(Auth $auth) {
        $this->_auth = $auth;
    }
    
    /**
     * Gets view.
     */
    protected function getView(): ?View {
        return $this->_view;
    }
    
    /**
     * Sets menu items.
     */
    protected function getMenuItems(): array {
        $menuItems = array(
            array('link' => '/', 'title' => 'Главная страница'),
        );
        
        if ($this->getAuth()->isAdmin()) {
            $menuItems[] = array('link' => '/admin', 'title' => 'Админка');
            $menuItems[] = array('link' => '/admin/vocabulary/images/fill', 'title' => 'Заполнить видео');
        }
        
        if ($this->getAuth()->isTeacher()) {
            // $menuItems[] = array('link' => '/teacher', 'title' => 'Учительская');
            $menuItems[] = array('link' => '/teacher/student', 'title' => 'Ученики');
            $menuItems[] = array('link' => '/teacher/lesson', 'title' => 'Материалы');
            $menuItems[] = array('link' => '/teacher/lesson/create', 'title' => 'Начать урок');
        }
        
        if ($this->getAuth()->isStudent()) {
            $menuItems[] = array('link' => '/student/lesson/active', 'title' => 'Начать урок');
        }
        
        if ($this->getAuth()->isGuest()) {
            $menuItems[] = array('link' => '/login', 'title' => 'Войти');
        } else {
            $menuItems[] = array('link' => '/login', 'title' => 'Выйти');
        }
        
        return $menuItems;
    }
    
    /**
     * Adds varible to main view.
     */
    protected function setVariable(string $key, $value): bool {
        return $this->_view->set($key, $value);
    }
    
    /**
     * Adds common varibles to main view.
     */
    protected function setVariables() {
        $this->setVariable('user', $this->getAuth()->getUser());
        $this->setVariable('url', $this->getUrl());
        $this->setVariable('baseUrl', $this->getBaseUrl());
    }
    
    /**
     * Sets data for key. Can be gotten later.
     */
    protected function setStashData(string $key, $data): bool {
        $result = false;
        
        if ($this->getRequest()) {
            if (array_key_exists('stash', $this->getRequest()->session)) {
                $stash = $this->getRequest()->session['stash'];
            } else {
                $stash = array();
            }
            $stash[$key] = $data;
            $this->getRequest()->setSessionVariable('stash', $stash);
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Gets data for key and removes it from stash.
     */
    protected function popStashData(string $key) {
        $result = null;
        
        if ($this->getRequest()) {
            if (
                array_key_exists('stash', $this->getRequest()->session)
                && is_array($this->getRequest()->session['stash'])
                && array_key_exists($key, $this->getRequest()->session['stash'])
            ) {
                $stash = $this->getRequest()->session['stash'];
                $result = $stash[$key];
                unset($stash[$key]);
                $this->getRequest()->setSessionVariable('stash', $stash);
            }
        }
        
        return $result;
    }
    
    /**
     * Gets $_POST variable by key.
     */
    protected function getPost(string $key) {
        $result = null;
        
        if ($this->getRequest()) {
            if (
                is_array($this->getRequest()->post)
                && array_key_exists($key, $this->getRequest()->post)
            ) {
                $result = $this->getRequest()->post[$key];
            }
        }
        
        return $result;
    }
    
    /**
     * Gets $_GET variable by key.
     */
    protected function getGet(string $key) {
        $result = null;
        
        if ($this->getRequest()) {
            if (
                is_array($this->getRequest()->get)
                && array_key_exists($key, $this->getRequest()->get)
            ) {
                $result = $this->getRequest()->get[$key];
            }
        }
        
        return $result;
    }
    
    /**
     * Gets page url.
     */
    protected function getUrl(): string {
        $url = '';
        
        if ($this->getRequest()) {
            $url = $this->getRequest()->getUrl();
        }
        
        return $url;
    }
    
    /**
     * Gets site's base url.
     */
    protected function getBaseUrl(): string {
        $url = '';
        
        if ($this->getRequest()) {
            $url = $this->getRequest()->getBaseUrl();
        }
        
        return $url;
    }
    
    protected function getAuthUrl(): string {
        $url = '';
        
        if ($this->getRequest()) {
            $url = $this->getRequest()->getBaseUrl() . '/login';
        }
        
        return $url;
    }
    
}

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
    private $_request = null;
    
    /**
     * Response object.
     */
    private $_response = null;
    
    /**
     * Auth object.
     */
    private $_auth = null;
    
    /**
     * @var $_isAjaxMode Determines if html or ajax mode is active.
     */
    private $_isAjaxMode = false;
    
    /**
     * View object for whole page.
     */
    private $_pageView = null;
    
    /**
     * View object for content area.
     */
    private $_view = null;
    
    /**
     * @var $_pageTemplate string Page template.
     */
    protected $_pageTemplate = 'page';
    
    /**
     * @var $_ajaxTemplate string Page template for ajax.
     */
    protected $_ajaxTemplate = 'ajax';
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = null;
    
    /**
     * Class constructor.
     */
    public function __construct() {
        $this->_view = Factory::instance()->createView();
        $this->_pageView = Factory::instance()->createView();
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
            $this->before();
            $this->setContentViewVariables();
            $content = $this->$methodName();
            
            $this->setPageViewVariables();
            $this->getPageView()->set('content', $content);
            $this->after();
            $this->getPageView()->set('minMenuItems', $this->getMenuItems());
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
        if ($this->getAjaxMode()) {
            $this->getPageView()->setTemplate($this->_ajaxTemplate);
        } else {
            $this->getPageView()->setTemplate($this->_pageTemplate);
        }
        $html = $this->getPageView()->render();
        
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
     * Gets whole page's view.
     */
    protected function getPageView(): ?View {
        return $this->_pageView;
    }
    
    /**
     * Gets content's view.
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
            $menuItems[] = array('link' => '/teacher/lesson', 'title' => 'Уроки');
            $menuItems[] = array('link' => '/teacher/lessonTemplate', 'title' => 'Шаблоны');
            $menuItems[] = array('link' => '/teacher/activeLesson', 'title' => 'Начать урок');
        }
        
        if ($this->getAuth()->isStudent()) {
            $menuItems[] = array('link' => '/student/lesson', 'title' => 'Уроки');
            $menuItems[] = array('link' => '/student/activeLesson', 'title' => 'Начать урок');
        }
        
        if ($this->getAuth()->isGuest()) {
            $menuItems[] = array('link' => '/login', 'title' => 'Войти');
        } else {
            $menuItems[] = array('link' => '/login', 'title' => 'Выйти');
        }
        
        return $menuItems;
    }
    
    /**
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables() {
        $this->getPageView()->set('user', $this->getAuth()->getUser());
        $this->getPageView()->set('url', $this->getUrl());
        $this->getPageView()->set('rootUrl', $this->getRootUrl());
        $this->getPageView()->set('baseTemplatePath', $this->getBaseTemplatePath());
    }
    
    protected function setContentViewVariables() {
        $this->getView()->set('currentUrl', $this->getUrl());
        $this->getView()->set('rootUrl', $this->getRootUrl());
        $this->getView()->set('baseUrl', $this->getBaseUrl());
        
        $this->getView()->set('baseTemplatePath', $this->getBaseTemplatePath());
        
        $messageType = $this->popStashData('messageType');
        $this->getView()->set('messageType', $messageType);
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
    protected function getRootUrl(): string {
        $url = '';
        
        if ($this->getRequest()) {
            $url = $this->getRequest()->getRootUrl();
        }
        
        return $url;
    }
    
    /**
     * Gets url to controller's root page.
     */
    public function getBaseUrl(): string {
        if ($this->_innerUrl) {
            $baseUrl = $this->getRootUrl() . $this->_innerUrl;
        } else {
            $baseUrl = '';
        }
        
        return $baseUrl;
    }
    
    /**
     * Gets template's path for including from templates.
     */
    protected function getBaseTemplatePath(): string {
        $ds = FileSystem::getDS();
        $baseTemplatePath = FileSystem::getRoot() . $ds . 'template' . $ds . 'Standart';
        return $baseTemplatePath;
    }
    
    protected function getAuthUrl(): string {
        $url = '';
        
        if ($this->getRequest()) {
            $url = $this->getRequest()->getRootUrl() . '/login';
        }
        
        return $url;
    }
    
    protected function getFromPost(string $name, string $defaultValue = null): ?string {
        if (array_key_exists($name, $this->getRequest()->post)) {
            $value = (string) $this->getRequest()->post[$name];
        } else {
            $value = $defaultValue;
        }
        
        return $value;
    }
    
    protected function setAjaxMode(bool $value): void {
        $this->_isAjaxMode = $value;
    }
    
    protected function getAjaxMode(): bool {
        return $this->_isAjaxMode;
    }
    
}

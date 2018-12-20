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
        $this->_request = $request;
        $this->_response = $response;
    }
    
    /**
     * Execute controller action.
     */
    public function execute(string $action): void {
        $methodName = 'action' . ucfirst($action);
        if (strlen($action) && method_exists($this, $methodName)) {
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
    protected function redirect(string $url): void {
        if (strlen($url)) {
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
     * Gets response.
     */
    protected function getResponse(): ?Response {
        return $this->_response;
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
        return array(
            array('link' => '/', 'title' => 'Главная страница',),
            array('link' => '/admin', 'title' => 'Админка',),
            array('link' => '/admin/vocabulary/images/fill', 'title' => 'Заполнить видео',),
        );
    }
    
    /**
     * Adds varible rule.
     */
    protected function setVariable(string $key, $value): bool {
        return $this->_view->set($key, $value);
    }
    
}

<?php

declare(strict_types=1);

namespace Classroom\Controller;

use Classroom\System\FileSystem;

use Classroom\Module\Auth\Auth;
use Classroom\Module\Config\Config;
use Classroom\Module\Factory\Factory;
use Classroom\Module\Request\Request;
use Classroom\Module\Response\Response;
use Classroom\Module\View\View;

use Classroom\Model\Model;
use Classroom\Model\ModelDatabase;

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
     * List of css files.
     */
    private $_cssFiles = array();
    
    /**
     * List of javascript files.
     */
    private $_jsFiles = array();
    
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
            
            // Should be called before rendering content.
            // In that case controller will be able reset page view variables.
            $this->setPageViewVariables();
            
            $this->setContentViewVariables();
            $content = $this->$methodName();
            
            $this->addJsAndCssFiles();
            $this->setPageViewMenuVariables();
            $this->getPageView()->set('content', $content);
            $this->after();
            $this->getResponse()->setHeader('Content-type: text/html; charset=utf-8');
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
        $this->getPageView()->set('cssFiles', $this->_cssFiles);
        $this->getPageView()->set('jsFiles', $this->_jsFiles);
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
     * Adds menu varibles to page's view.
     */
    protected function setPageViewMenuVariables(): void {
        $conditionsList = array('disabled' => false, 'delete' => false);
        $sortingList = array('variable' => 'asc');
        $menusList = Factory::instance()->createModel('Menu')
            ->getModelsList($conditionsList, 0, 0, $sortingList);
        foreach ($menusList as $menu) {
            $menuItems = array();
            
            if ($menu->userHasAccess($this->getAuth())) {
                foreach ($menu->getItems() as $item) {
                    if ($item->userHasAccess($this->getAuth())) {
                        $item->setRootUrl($this->getRootUrl());
                        $menuItems[] = $item;
                    }
                }
            }
            
            // If there is no access set empty array.
            $this->getPageView()->set($menu->variable, $menuItems);
        }
    }
    
    /**
     * Adds common varibles to page's view.
     */
    protected function setPageViewVariables(): void {
        $config = Config::instance();
        
        $this->getPageView()->set('user', $this->getAuth()->getUser());
        $this->getPageView()->set('currentUrl', $this->getUrl());
        $this->getPageView()->set('rootUrl', $this->getRootUrl());
        $this->getPageView()->set('baseTemplatePath', $this->getBaseTemplatePath());
        $this->getPageView()->set('bodyClass', '');
        
        $this->getPageView()->set('pageCaption', $config->get('site', 'caption'));
        $this->getPageView()->set('pageTitle', $config->get('site', 'title'));
        $this->getPageView()->set('pageKeywords', $config->get('site', 'keywords'));
        $this->getPageView()->set('pageDescription', $config->get('site', 'description'));
        
        $this->getPageView()->set('nicEditConfig', $config->get('nicEdit'));
    }
    
    protected function setContentViewVariables() {
        $config = Config::instance();

        $this->setControlTypes();

        $this->getView()->set('currentUrl', $this->getUrl());
        $this->getView()->set('rootUrl', $this->getRootUrl());
        $this->getView()->set('baseUrl', $this->getBaseUrl());
        
        $this->getView()->set('baseTemplatePath', $this->getBaseTemplatePath());
        
        $messageType = $this->popStashData('messageType');
        $this->getView()->set('messageType', $messageType);
        
        $this->getView()->set('nicEditConfig', $config->get('nicEdit'));
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
            $baseUrl = $this->getRootUrl();
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
    
    protected function getProfileUrl(): string {
        $url = '';
        
        if ($this->getRequest()) {
            $url = $this->getRequest()->getRootUrl() . '/profile';
        }
        
        return $url;
    }
    
    /**
     * Gets variable value from $_GET.
     */
    protected function getFromGet(string $name, string $defaultValue = null): ?string {
        if (array_key_exists($name, $this->getRequest()->get)) {
            $value = (string) $this->getRequest()->get[$name];
        } else {
            $value = $defaultValue;
        }
        
        return $value;
    }
    
    /**
     * Gets variable value from $_POST.
     */
    protected function getFromPost(string $name, string $defaultValue = null): ?string {
        if (array_key_exists($name, $this->getRequest()->post)) {
            $value = (string) $this->getRequest()->post[$name];
        } else {
            $value = $defaultValue;
        }
        
        return $value;
    }
    
    /**
     * Gets variable value from $_SESSION.
     */
    protected function getFromSession(string $name, string $defaultValue = null): ?string {
        if (array_key_exists($name, $this->getRequest()->session)) {
            $value = (string) $this->getRequest()->session[$name];
        } else {
            $value = $defaultValue;
        }
        
        return $value;
    }
    
    /**
     * Sets variable value to $_SESSION.
     */
    protected function setToSession(string $name, string $value): bool {
        return $this->getRequest()->setSessionVariable($name, $value);
    }
    
    /**
     * Unsets variable value from $_SESSION.
     */
    protected function unsetFromSession(string $name): bool {
        return $this->getRequest()->unsetSessionVariable($name);
    }
    
    protected function setAjaxMode(bool $value): void {
        $this->_isAjaxMode = $value;
    }
    
    protected function getAjaxMode(): bool {
        return $this->_isAjaxMode;
    }
    
    protected function addCssFile($filePath) {
        if ($filePath && ! in_array($filePath, $this->_cssFiles)) {
            $this->_cssFiles[] = $filePath;
        }
    }
    
    protected function addJsFile($filePath) {
        if ($filePath && ! in_array($filePath, $this->_jsFiles)) {
            $this->_jsFiles[] = $filePath;
        }
    }
    
    protected function addJsAndCssFiles() {
        $this->addCssFile('/css/common.css');
        $this->addJsFile('/vendor/jquery/jquery-3.3.1.js');
        $this->addJsFile('/vendor/nicEdit/nicEdit.js');
        $this->addJsFile('/js/nicEdit/initNicEdit.js');
        $this->addJsFile('/js/nicEdit/nicUpload.js');
        $this->addJsFile('/js/nicEdit/nicYoutube.js');
        $this->addJsFile('/js/nicEdit/nicHtmlFragment.js');
        $this->addJsFile('/js/nicEdit/nicHtmlCode.js');
        $this->addJsFile('/js/common.js');
    }
    
    protected function setPropertiesFromPost(ModelDatabase $model): bool {
        $canSave = false;
        $propertiesList = $model->getPropertiesList();
        $controlsList = $this->getModelControlsList($model);
        
        foreach ($propertiesList as $propertyName => $propertyData) {
            if (! $model->isPk($propertyName)) {
                if ($controlsList[$propertyName] !== self::CONTROL_NONE) {
                    $value = $this->getFromPost($propertyName);
                    if (! is_null($value) && ($controlsList[$propertyName] !== self::CONTROL_PASSWORD || $value !== '')) {
                        $value = $this->convertFromPost($value, $propertyData);
                        $model->$propertyName = $value;
                        $canSave = true;
                    }
                }
            }
        }
        return $canSave;
    }
    
    protected function convertFromPost(?string $value, array $propertyData = array()) {
        if (array_key_exists('type', $propertyData) && $propertyData['type'] === ModelDatabase::TYPE_BOOL) {
            if ($value === '') {
                $preparedValue = null;
            } else if ($value === '1') {
                $preparedValue = true;
            } else {
                $preparedValue = false;
            }
        } else {
            $preparedValue = $value;
        }
        
        return $preparedValue;
    }
    
    protected function getModelControlsList(ModelDatabase $model) {
        $propertiesList = $model->getPropertiesList();
        
        $controlsList = $this->getControlsList($model);
        
        foreach ($propertiesList as $propertyName => $property) {
            if (! array_key_exists($propertyName, $controlsList)) {
                if (! empty($property['skipControl'])) {
                    $controlType = self::CONTROL_NONE;
                } else {
                    switch ($property['type']) {
                        case Model::TYPE_TEXT:
                        case Model::TYPE_INT:
                            $controlType = self::CONTROL_INPUT;
                            break;
                        case Model::TYPE_BOOL:
                            $controlType = self::CONTROL_SELECT_BOOL;
                            break;
                        case Model::TYPE_FK:
                            $controlType = self::CONTROL_SELECT;
                            break;
                        case Model::TYPE_ENUM:
                            $controlType = self::CONTROL_SELECT;
                            break;
                        default:
                            $controlType = self::CONTROL_INPUT;
                            break;
                    }
                }
                $controlsList[$propertyName] = $controlType;
            }
        }
        return $controlsList;
    }
    
    /**
     * @var array $_modelControlsList Defines controls for model properties.
     * 
     * propertyName => controlType
     * f. e.
     * 'deleted' => self::CONTROL_NONE,
     */
    protected function getControlsList(ModelDatabase $model) {
        return array();
    }

    private function setControlTypes() {
        $this->getView()->set('CONTROL_INPUT', self::CONTROL_INPUT);
        $this->getView()->set('CONTROL_PASSWORD', self::CONTROL_PASSWORD);
        $this->getView()->set('CONTROL_TEXTAREA', self::CONTROL_TEXTAREA);
        $this->getView()->set('CONTROL_HTML', self::CONTROL_HTML);
        $this->getView()->set('CONTROL_HTML_SIMPLE_PANEL', self::CONTROL_HTML_SIMPLE_PANEL);
        $this->getView()->set('CONTROL_HTML_EMPTY_PANEL', self::CONTROL_HTML_EMPTY_PANEL);
        $this->getView()->set('CONTROL_SELECT_BOOL', self::CONTROL_SELECT_BOOL);
        $this->getView()->set('CONTROL_SELECT', self::CONTROL_SELECT);
        $this->getView()->set('CONTROL_FILE', self::CONTROL_FILE);
        $this->getView()->set('CONTROL_LABEL', self::CONTROL_LABEL);
        $this->getView()->set('CONTROL_NONE', self::CONTROL_NONE);
    }
}

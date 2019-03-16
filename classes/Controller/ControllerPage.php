<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerBase.php');

class ControllerPage extends ControllerBase {
    
    /**
     * @var array $_conditionsList Default conditions list.
     */
    protected $_conditionsList = array('deleted' => false, 'disabled' => false,);
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '';
    
    /**
     * @var array $_templateNames View templates' names.
     */
    protected $_templateName = 'Common/page';
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
    }
    
    protected function actionIndex() {
        
        $url = str_replace($this->getRootUrl(), '', $this->getRequest()->url);
        
        $condition = array_merge($this->_conditionsList, array('url' => $url));
        $page = Factory::instance()->createModel('Page')
            ->getOneModel($condition);
        if ($page) {
            if ($this->checkAccess($page)) {
                $this->setPageViewVariablesInner($page);
                $content = $this->innerRenderContent($page);
            } else {
                $this->redirect($this->getAuthUrl());
            }
        } else {
            $content = '';
            $this->send404();
        }
        
        return $content;
    }
    
    /**
     * Checks if current user has access to the page.
     */
    protected function checkAccess(ModelPage $page): bool {
        $result = $page->userHasAccess($this->getAuth());
        
        return $result;
    }
    
    /**
     * Adds varibles to page's view.
     */
    protected function setPageViewVariablesInner(ModelPage $page): void {
        $this->getPageView()->set('pageCaption', $page->caption);
        $this->getPageView()->set('pageTitle', $page->title);
        $this->getPageView()->set('pageKeywords', $page->keywords);
        $this->getPageView()->set('pageDescription', $page->description);
    }
    
    /**
     * Render content for page.
     */
    protected function innerRenderContent(ModelPage $page): string {
        $content = '';
        
        $this->getView()->setTemplate($this->_templateName);
        
        $this->getView()->set('content', $page->content);
        
        $content = $this->getView()->render();
        
        return $content;
    }
    
}

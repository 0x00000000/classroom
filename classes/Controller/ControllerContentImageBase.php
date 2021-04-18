<?php

declare(strict_types=1);

namespace Classroom\Controller;

use Classroom\Module\Factory\Factory;
use Classroom\Module\NicImageUploader\NicImageUploader;

use Classroom\Model\ModelPage;

abstract class ControllerContentImageBase extends ControllerBase {
    
    /**
     * @var array $_conditionsList Default conditions list.
     */
    protected $_conditionsList = array('deleted' => false, 'disabled' => false,);
    
    /**
     * @var array $_innerUrl Inner url to controller's root page. Should be started from '/'.
     */
    protected $_innerUrl = '';
    
    /**
     * Class constructor.
     */
    public function __construct() {
        parent::__construct();
    }
    
    protected function actionIndex() {
        
        $this->setAjaxMode(true);
        
        if (! empty($this->getRequest()->files['image'])) {
            $result = $this->innerActionUploadFile($this->getRequest()->files['image']);
        } else {
            $this->send404();
        }
        
        $content = json_encode($result);
        return $content;
    }
    
    protected function innerActionUploadFile($fileInfo) {
        $nicImageUploader = Factory::instance()->createModule('NicImageUploader');
        $nicImageUploader->setRequest($this->getRequest());
        $nicImageUploader->setAuth($this->getAuth());
        $result = $nicImageUploader->upload($fileInfo);
        return $result;
    }
    
}

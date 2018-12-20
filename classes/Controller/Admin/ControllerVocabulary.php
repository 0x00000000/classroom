<?php

declare(strict_types=1);

namespace classroom;

include_once('ControllerAdminBase.php');

class ControllerVocabulary extends ControllerAdminBase {
    
    protected function actionIndex() {
        $content = 'Admin render content index page.';
        
        return $content;
    }
    
    protected function actionAudioAdd() {
        $content = 'Fill render content audio page.';
        
        return $content;
    }
    
    protected function actionImagesAdd() {
        $content = 'Fill render content images page.';
        
        return $content;
    }
    
}

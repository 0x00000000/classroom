<?php

declare(strict_types=1);

namespace Classroom\Controller\Admin;

use Classroom\Module\Factory\Factory;

class ControllerAdminVocabulary extends ControllerAdminBase {
    
    protected function actionIndex() {
        $content = 'Admin render content index page.';
        
        return $content;
    }
    
    protected function actionAudioAdd() {
        $content = 'Fill render content audio page.';
        
        return $content;
    }
    
    protected function actionImagesAdd() {
        $view = Factory::instance()->createView();
        $view->setTemplate('Admin/Vocabulary/FillImages');
        
        $get = $this->getRequest()->get;
        $currentPage = array_key_exists('page', $get) ? $get['page'] : '1';
        $itemsPerPage = 10;
        
        $modelWord = Factory::instance()->createModel('Word');
        $wordsList = $modelWord->getModelsList(
            array('image' => '0'),
            $itemsPerPage,
            ((int) $currentPage - 1) * (int) $itemsPerPage
        );
        
        $pagesCount = $modelWord->getCount(
            array('image' => '0')
        );
        
        $pagesList = array();
        if ($pagesCount > 1) {
            $pagesCount = floor(($pagesCount - 1) / $itemsPerPage);
            for ($i = 1; $i <= $pagesCount; $i++) {
                $pagesList[] = (string) $i;
            }
        } else {
            $pagesCount = 1;
        }
        
        $baseLink = $this->getRequest()->url;
        $baseLink = preg_replace('/\/images\/fill\/(\w+)$/', '/images/fill', $baseLink);
        $view->set('baseLink', $baseLink);
        
        $view->set('wordsList', $wordsList);
        $view->set('currentPage', $currentPage);
        $view->set('pagesCount', $pagesCount);
        $view->set('pagesList', $pagesList);
        
        $content = $view->render();
        
        return $content;
    }
    
}

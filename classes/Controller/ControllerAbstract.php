<?php

declare(strict_types=1);

namespace classroom;

/**
 * Executes actions and renders page.
 */
abstract class ControllerAbstract {
    
    /**
     * Initializes controller.
     */
    abstract public function init(Request $request, Response $response): void;
    
    /**
     * Execute controller action.
     */
    abstract public function execute(string $action): void;
    
    /**
     * Redirects UA to url and stops script execution.
     */
    abstract protected function redirect(string $url = null): void;
    
    /**
     * Sends 404 response to UA.
     */
    abstract protected function send404(): void;
    
    /**
     * Executes before controller action.
     */
    abstract protected function before(): void;
    
    /**
     * Executes after controller action.
     */
    abstract protected function after(): void;
    
    /**
     * Gets request.
     */
    abstract protected function getRequest(): ?Request;
    
    /**
     * Gets response.
     */
    abstract protected function getResponse(): ?Response;
    
    /**
     * Sets data for key. Can be gotten later.
     */
    abstract protected function setStashData(string $key, $data): bool;
    
    /**
     * Gets data for key and removes it from stash.
     */
    abstract protected function popStashData(string $key);
    
    /**
     * Gets $_POST variable by key.
     */
    abstract protected function getPost(string $key);
    
    /**
     * Gets $_GET variable by key.
     */
    abstract protected function getGet(string $key);
    
    /**
     * Gets page url.
     */
    abstract protected function getUrl(): string;
    
    /**
     * Gets site base url.
     */
    abstract protected function getBaseUrl(): string;
    
}

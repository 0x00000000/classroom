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
    abstract protected function redirect(string $url): void;
    
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
    
}

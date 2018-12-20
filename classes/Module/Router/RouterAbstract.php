<?php

declare(strict_types=1);

namespace classroom;

/**
 * Routes UA.
 */
abstract class RouterAbstract {
    
    /**
     * Routes UA.
     */
    abstract public function route(): void;
    
    /**
     * Initializes router.
     */
    abstract public function init(Request $request, Response $response): void;
    
    /**
     * Adds route rule.
     */
    abstract public function setRule(string $rule, string $controller, string $action): bool;
    
    /**
     * Sets default route rule.
     */
    abstract public function setDefaultRule(string $controller, string $action): bool;
    
    /**
     * Gets request.
     */
    abstract protected function getRequest(): Request;
    
    /**
     * Gets response.
     */
    abstract protected function getResponse(): Response;
    
}

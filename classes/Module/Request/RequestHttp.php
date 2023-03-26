<?php

declare(strict_types=1);

namespace Classroom\Module\Request;

/**
 * Allows to get current request information.
 * 
 * @property string|null $url Request's url (readonly).
 * @property array|null $get Request's get data (readonly).
 * @property array|null $post Request's post data (readonly).
 * @property array|null $files Request's files data (readonly).
 * @property array|null $session Request's session data (readonly).
 * @property array|null $headers Request's headers (readonly).
 * @property string|null $ip User ip (readonly).
 * @property string|null $userAgent Rser agent infoimation (readonly).
 */
class RequestHttp extends RequestBase {

    protected function getSessionValuesList(): array {
        if (isset($_SESSION) && is_array($_SESSION)) {
            return $_SESSION;
        } else {
            return [];
        }
    }

    protected function setSessionValue(string $key, $value): void {
        if (isset($_SESSION) && is_array($_SESSION)) {
            $_SESSION[$key] = $value;
        }
    }

    protected function unsetSessionValue(string $key): void {
        if (isset($_SESSION) && is_array($_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    protected function getSessionValue(string $key) {
        if (isset($_SESSION) && is_array($_SESSION)) {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        } else {
            return null;
        }
    }
}

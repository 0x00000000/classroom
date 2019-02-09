<?php

declare(strict_types=1);

namespace classroom;

include_once('RequestBase.php');

/**
 * Allows to get test request information.
 * 
 * @property string|null $url Request's url (readonly).
 * @property array|null $get Request's get data (readonly).
 * @property array|null $post Request's post data (readonly).
 * @property array|null $session Request's session data (readonly).
 * @property array|null $headers Request's headers (readonly).
 * @property string|null $ip User ip (readonly).
 * @property string|null $userAgent Rser agent infoimation (readonly).
 */
class RequestTest extends RequestBase {
    
    /**
     * Test constants.
     */
    public const TEST_URL = 'http://example.com/';
    public const TEST_GET = array('getParam' => 'test get');
    public const TEST_POST = array('postParam' => 'test post');
    public const TEST_SESSION = array('sessionParam' => 'test session');
    public const TEST_HEADERS = array('headersParam' => 'test headers');
    public const TEST_IP = '127.0.0.1';
    public const TEST_USER_AGENT = 'Test UA';
    public const TEST_BASE_URL = 'http://example.com';
    
    /**
     * Creates test reqeust.
     */
    protected function create() {
        $this->_currentRequest = Factory::instance()->createModel('Request');
        
        $this->_currentRequest->url = self::TEST_URL;
        $this->_currentRequest->get = self::TEST_GET;
        $this->_currentRequest->post = self::TEST_POST;
        $this->_currentRequest->session = self::TEST_SESSION;
        $this->_currentRequest->headers = self::TEST_HEADERS;
        $this->_currentRequest->ip = self::TEST_IP;
        $this->_currentRequest->userAgent = self::TEST_USER_AGENT;
        
        $this->_currentRequest->save();
    }
    
    /**
     * Gets site's base url.
     */
    public function getBaseUrl(): string {
        $url = self::TEST_BASE_URL;
        
        return $url;
    }
    
}

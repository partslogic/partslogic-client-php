<?php
/*
 * Copyright 2024 PartsLogic Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


namespace PartsLogic;

use PartsLogic\Search\Api;

use GuzzleHttp\Client as GuzzleClient;
use Monolog\Handler\StreamHandler as MonologStreamHandler;
use Monolog\Handler\SyslogHandler as MonologSyslogHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Psr7\Request;

/**
 * Parts Logic API Client
 */
class Client
{
    const PARTSLOGIC_KEY_HEADER = 'sunhammer-api-key';
    const CONFIG_PARAMETERS = ['apiKey', 'endpoint'];
    const DEFAULT_LOG_LEVEL = Logger::NOTICE;
    const APP_NAME = 'partslogic-api-php-client';

    /**
     * @var string PartsLogic API Endpoint
     */
    private $endpoint = "https://api.sunhammer.io";

    /**
     * @var string PartsLogic API Key
     */
    private $apiKey;

    /**
     * @var GuzzleClient Guzzler client
     */
    private $http;

    /**
     * @var \Monolog\Logger $logger
     */
    private $logger;

    /**
     * @var Api Search api container
     */
    private $search;

    /**
     * Construct the PartsLogic Client.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $name => $value) {
            if (! in_array($name, self::CONFIG_PARAMETERS)) {
                throw new \RuntimeException("unknown config parameter: $name");
            }

            $this->$name = $config[$name];
        }
    }

    /**
     * Custom getter magic function
     *
     * @param string $property Name of property to get
     * @return object Value store in the private property
     */
    public function __get($property)
    {
        /* Lazy load search api */
        if ($property == "search" && ! isset($this->$property)) {
            $this->$property = new Api($this);
        }

        if (property_exists($this, $property)) {
            if (isset($this->$property)) {
                return $this->$property;
            } else {
                throw new \RuntimeException("'$property' has not been set for client");
            }
        } else {
            throw new \OutOfBoundsException("'$property' does not exist.");
        }
    }
    
    /**
     * Custom setter magic function
     *
     * @param string $property Name of property to set
     * @param object $value Value of property to set
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new \OutOfBoundsException("'$property' does not exist.");
        }
    }

    /**
    * @return GuzzleClient
    */
    public function getHttpClient()
    {
        if (null === $this->http) {
            $this->http = $this->createDefaultHttpClient();
        }

        return $this->http;
    }

    /**
    * Create Guzzle HTTP client
    *
    * @return GuzzleClient
    */
    public function createDefaultHttpClient($handler = null)
    {
        $options = [
          'base_uri' => $this->endpoint,
          'http_errors' => false,
          'headers' => [ self::PARTSLOGIC_KEY_HEADER => $this->apiKey ]
        ];
        if ($handler) {
            $options['handler'] = $handler;
        }
        return new GuzzleClient($options);
    }
    
    /**
     * Set the Logger object
     * @param \Monolog\Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
        
    /**
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        if (null === $this->logger) {
            $this->logger = $this->createDefaultLogger();
        }

        return $this->logger;
    }

    /**
     * Set log level to debug
     * @todo Add test
     */
    public function enableDebug()
    {
        // $this->logger->reset();
        $this->logger = $this->createDefaultLogger(Logger::DEBUG);
    }

    protected function createDefaultLogger($logLevel = self::DEFAULT_LOG_LEVEL)
    {
        $logger = new Logger(self::APP_NAME);
        $handler = new MonologStreamHandler('php://stderr', $logLevel);
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * Send the request and then decode the results
     *
     * @param \GuzzleHttp\Psr7\Request $request Request object to fire
     * @param integer $successStatus http status we determine to be a successful response
     *
     * @ return object Json decoded object from the response body
     */
    public function sendAndDecode($request, $successStatus = 200)
    {
        $response = $this->send($request);

        if ($response->getStatusCode() != 200) {
            $this->getLogger()->error("Request failed. Response: " .
            print_r($response, true));

            return null;
        }

        return $this->decodeResponse($response->getBody());
    }

    /**
     * Fire off a Guzzle request
     *
     * @param \GuzzleHttp\Psr7\Request $request Request object to fire
     * @return \Psr\Http\Message\ResponseInterface guzzle response
     */
    public function send($request)
    {
        $response = $this->getHttpClient()->send($request);
        $this->getLogger()->debug(print_r($response, true));
        return $response;
    }

    /**
     * Ping the PartsLogic API
     *
     * @return bool Could we successfully reach the PL server and get a good response?
     */
    public function ping()
    {
        $request = new \GuzzleHttp\Psr7\Request('GET', '/');
        $body = $this->sendAndDecode($request);

        return $body == "OK";
    }

    protected function decodeResponse($body)
    {
        return json_decode($body);
    }
}

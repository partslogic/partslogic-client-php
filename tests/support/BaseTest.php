<?php
/*
 * Copyright 2024 PartsLogic Inc
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

namespace PartsLogic\Tests;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use Psr\Log\LogLevel;
use Symfony\Component\Yaml\Yaml as Yaml;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

use PartsLogic\Client;
use PartsLogic\Tests\Config;

/**
 *  Base class for all tests
 */
class BaseTest extends TestCase
{
    const YAML_EXTIONSIONS = ['yaml', 'yml'];

    protected $client;
    protected $config;
    protected $mockHandler;
    protected $mockClient;
    protected $handlerStack;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = new Config();

        $this->mockHandler = new \GuzzleHttp\Handler\MockHandler();
        if ($this->config->useMockResponses) {
            $this->handlerStack = new \GuzzleHttp\HandlerStack($this->mockHandler);
        } else {
            $this->handlerStack = \GuzzleHttp\HandlerStack::create();
            $loggerMiddleware = new \Concat\Http\Middleware\Logger(
                $this->getClient()->getLogger(),
                new \Geekdevs\Http\Message\Formatter\CurlCommandFormatterAdapter()
            );
            $this->handlerStack->push($loggerMiddleware);
        }

        $this->client = $this->createClient();
    }

    public function setUp(): void
    {
        if ($this->config->useMockResponses) {
            $this->mockHandler->reset();
        }
    }
    
    public function tearDown(): void
    {
        if ($this->config->useMockResponses) {
            $this->mockHandler->reset();
        }
    }
    
    public function getClient()
    {
        if (!$this->client) {
            $this->client = $this->createClient();
        }
        return $this->client;
    }

    public function createClient()
    {
        $client = new \PartsLogic\Client([
            'apiKey' => $this->config->apiKey,
            'endpoint' => $this->config->apiEndpoint,
        ]);
        $client->http = $client->createDefaultHttpClient($this->handlerStack);

        if ($this->config->enableDebug) {
            $client->enableDebug();
        }

        return $client;

    }

    public function addMockResponse($name)
    {
        $yaml = file_get_contents($this->getMockResponseFile($name));
        $responseData = Yaml::parseFile($this->getMockResponseFile($name));
        $status = $responseData['status'];
        $headers = isset($responseData['headers']) ? $responseData['headers'] : [];
        $body = is_array($responseData['body']) ? json_encode($responseData['body']) :
                                                  $responseData['body'] ;
        $this->mockHandler->append(new \GuzzleHttp\Psr7\Response($status, $headers, $body));
    }

    public function getMockResponseFile($name)
    {
        foreach (self::YAML_EXTIONSIONS as $ext) {
            $path = MOCK_RESPONSES_DIR . "/$name" . ".$ext";
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException("response file '$name' not found in " .  MOCK_RESPONSES_DIR);
    }
}

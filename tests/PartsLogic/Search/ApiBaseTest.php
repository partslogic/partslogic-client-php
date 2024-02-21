<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

namespace PartsLogic\Tests\Search;

use PartsLogic\Search\Base;
use PartsLogic\Search\Parameter;
use PartsLogic\Search\Response;
use PartsLogic\Tests\BaseTest;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

class SampleApiObject extends Base
{
    protected $path = '/';

    public function headers()
    {
        return [ 'testHeader' => 'testValue' ];
    }
}

class BadApiObject extends Base
{
}

class ApiBaseTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->apiObject = new SampleApiObject($this->getClient());
    }

    /* Validate we can create an object */
    public function testConstructor()
    {
        $this->assertInstanceOf(Base::class, $this->apiObject);
    }

    /* Validate path propterty is required */
    public function testConstructorException()
    {
        $this->expectException(\RuntimeException::class);
        new BadApiObject($this->getClient());
    }

    public function configValidateParameters(): array
    {
        return [
            'no-query'   => [ [], null, null ],
            'no-query-exception'   => [
                ['test' => 'foo'],
                null,
                \PartsLogic\Exception\InvalidArgumentsException::class,
                1
            ],
            'one-parameter' => [
                ['test' => 'foo'],
                [ 'test' => new Parameter()],
            ],
            'list-parameter' => [
                ['test' => [1, 2]],
                [ 'test' => new Parameter()],
            ],
            'one-parameter-required' => [
                [],
                [ 'test' => new Parameter(true) ],
                \PartsLogic\Exception\InvalidArgumentsException::class,
                1
            ],
            'two-parameters' => [
                [ 'test' => 1, 'foo' => 2 ],
                [ 'test' => new Parameter(), 'foo' => new Parameter() ]
            ],
            'two-parameters-required' => [
                [],
                [ 'test' => new Parameter(true), 'foo' => new Parameter(true) ],
                \PartsLogic\Exception\InvalidArgumentsException::class,
                2
            ],
            'extra-parameters' => [
                [ 'test' => 1 ],
                null,
                \PartsLogic\Exception\InvalidArgumentsException::class,
                1
            ]
        ];
    }

    /**
     * Validate our validation function
     *
     * @dataProvider configValidateParameters
     */
    public function testClientSetter($query, $parameters, $exception = null, $errorCount = 0)
    {
        if ($parameters) {
            foreach ($parameters as $name => $param) {
                $this->apiObject->addParameter($name, $param);
            }
        }

        if ($exception) {
            $this->expectException($exception);
            $this->apiObject->validate($query);
            $this->assertEquals(count($this->apiObject->getErrors(), $errorCount));
        } else {
            $this->assertTrue($this->apiObject->validate($query));
            $this->assertEquals(count($this->apiObject->getErrors()), 0);
        }
    }
    
    /* Validate url generation */
    public function testUri()
    {
        // Without query params
        $expectedQuery = "";
        $this->assertInstanceOf(\GuzzleHttp\Psr7\Uri::class, $this->apiObject->uri());
        $this->assertEquals($this->apiObject->uri()->getPath(), '/');
        $this->assertEquals($this->apiObject->uri()->getQuery(), $expectedQuery);

        // With query params
        $this->apiObject->addParameter('testQuery', new Parameter());
        $testQuery = ['testQuery' => 'testValue'];
        $expectedQuery = join(
            "=",
            [array_keys($testQuery)[0],
            $testQuery[array_keys($testQuery)[0]]]
        );
        $this->assertInstanceOf(\GuzzleHttp\Psr7\Uri::class, $this->apiObject->uri());
        $this->assertEquals($this->apiObject->uri($testQuery)->getPath(), '/');
        $this->assertEquals($this->apiObject->uri($testQuery)->getQuery(), $expectedQuery);
    }

    /* Validate header generation */
    public function testHeaders()
    {
        $this->assertTrue(is_array($this->apiObject->headers()));
    }

    /* Validate request generation */
    public function testRequest()
    {
        $testQuery = [ 'testQuery' => 'testValue' ];
        $expectedQuery = join(
            "=",
            [array_keys($testQuery)[0],
            $testQuery[array_keys($testQuery)[0]]]
        );
        $this->apiObject->addParameter('testQuery', new \PartsLogic\Search\Parameter());

        $request = $this->apiObject->buildRequest('GET', $testQuery);

        $this->assertInstanceOf(\GuzzleHttp\Psr7\Request::class, $request);
        $this->assertInstanceOf(\GuzzleHttp\Psr7\Uri::class, $this->apiObject->uri());
        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getUri()->getPath(), $this->apiObject->uri()->getPath());
        $this->assertEquals($request->getUri()->getQuery(), $expectedQuery);

        foreach ($this->apiObject->headers() as $header => $value) {
            $this->assertEquals($request->getHeader($header), [$value]);
        }
    }

    /* Validate send request response */
    public function testGet()
    {
        $this->addMockResponse("pingOk");
        $response = $this->apiObject->get();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccess());
    }
}

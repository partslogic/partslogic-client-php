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

use PartsLogic\Search\Response;
use PartsLogic\Search\Base;
use PartsLogic\Tests\BaseTest;

class ResponseApiObject extends Base
{
    protected $path = '/';
}

class ResponseTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();

        $apiObject = new ResponseApiObject($this->getClient());

        /* Validate send request response */
        $this->addMockResponse("pingOk");
        $request = $apiObject->buildRequest('GET');
        $httpResponse = $apiObject->sendRequest($request);

        $this->response = new Response($httpResponse);

    }

    /* Validate we can create an object */
    public function testConstructor()
    {
        $this->assertInstanceOf(Response::class, $this->response);
    }

    /* Validate decoding */
    public function testDecoder()
    {
        $this->assertEquals($this->response->getBody(), "OK");
        $this->assertEquals($this->response->getStatusCode(), 200);
        $this->assertTrue(is_array($this->response->getHeaders()));
        $this->assertTrue($this->response->isSuccess());
    }
}

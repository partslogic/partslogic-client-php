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
use PartsLogic\Tests\ApiTest;

class HealthCheckTest extends ApiTest
{
    protected $objClass = \PartsLogic\Search\HealthCheck::class;

    public function testHealthCheckResponse()
    {
        $this->addMockResponse("healthCheckOk");
        $response = $this->apiRequest->get();

        $this->assertInstanceOf(Response::class, $response);

        $this->assertTrue(is_string($response->getBody()->status));
        $this->assertTrue(is_string($response->getBody()->uptime));
        $this->assertTrue((int)($response->getBody()->uptime) > 0);
        $this->assertTrue(is_string($response->getBody()->environment));
        
        if ($this->config->useMockResponses) {
            $this->assertEquals("API Online", $response->getBody()->status);
            $this->assertEquals(123211, $response->getBody()->uptime);
            $this->assertEquals("live", $response->getBody()->environment);
        }
    }
}

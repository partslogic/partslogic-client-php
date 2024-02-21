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

namespace PartsLogic\Tests\Search\Fitment;

use PartsLogic\Search\Response;
use PartsLogic\Tests\ApiTest;

class FitmentLabelsTest extends ApiTest
{
    protected $objClass = \PartsLogic\Search\Fitment\Labels::class;

    public function testFitmentCheckOk()
    {
        $params = [ 'groupId' => 1 ];
        $this->addMockResponse("labelsOk");
        $response = $this->apiRequest->get($params);

        $this->assertInstanceOf(Response::class, $response);
        $body = $response->getBody();

        $this->assertTrue($response->isSuccess());
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertTrue(is_array($body));

        if ($this->config->useMockResponses) {
            $this->assertEquals(sizeof($body), 3);
            $item = $body[0];
            $this->assertEquals($item->id, '61d6d46dea7279c8404e5c01');
            $this->assertEquals($item->groupId, '1');
            $this->assertEquals($item->name, 'Year');
            $this->assertEquals($item->priority, '998');
        }
    }

    public function testFitmentValuesOk()
    {
        $params = [ 'groupId' => 1 ];
        $this->addMockResponse("labelsModelOk");
        $response = $this->apiRequest->getValues("model", $params);

        $this->assertInstanceOf(Response::class, $response);
        $body = $response->getBody();

        $this->assertTrue($response->isSuccess());
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertTrue(is_array($body));
    }
}

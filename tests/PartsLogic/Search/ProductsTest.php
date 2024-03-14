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

class ProductsTest extends ApiTest
{
    protected $objClass = \PartsLogic\Search\Products::class;

    public function testProductsOk()
    {
        $params = [ 'page' => 1, 'limit' => 1];
        $this->addMockResponse("productsOk");
        $response = $this->apiRequest->get($params);

        $this->assertInstanceOf(Response::class, $response);
        $body = $response->getBody();

        $this->assertTrue($response->isSuccess());
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testProductsFilteredOk()
    {
        $params = [ 
            'Drive' => [ '2wd', '4wd' ], 
            'page' => 1, 
            'limit' => 1
        ];
        $this->addMockResponse("productsFilteredOk");
        $response = $this->apiRequest->get($params);

        $this->assertInstanceOf(Response::class, $response);
        $body = $response->getBody();

        $this->assertTrue($response->isSuccess());
        $this->assertEquals($response->getStatusCode(), 200);
    }
}

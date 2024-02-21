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

namespace PartsLogic\Tests;

require_once __DIR__ . "/BaseTest.php";
use PartsLogic\Tests\BaseTest;

class ApiTest extends BaseTest
{
    protected $objClass = null;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (! isset($this->objClass)) {
            throw new \RuntimeException("objClass must be overloaded in derived class");
        }
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->apiRequest = $this->buildRequest();
    }

    public function buildRequest()
    {
        return new $this->objClass($this->getClient());
    }
    
    public function testConstructor()
    {
        $this->assertInstanceOf($this->objClass, $this->apiRequest);
    }
}

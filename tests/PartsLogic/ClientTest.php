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

namespace PartsLogic;

use PartsLogic\Client;
use PartsLogic\Tests\BaseTest;

class ClientTest extends BaseTest
{
    public function clientConfigParameters(): array
    {
        return [
            'apiKey'   => [ "apiKey", "new-api-key" ],
            'endpoint' => [ "endpoint", "new-endpoint" ],
        ];
    }

    /* Validate we can create an object */
    public function testClientConstructor()
    {
        $this->assertInstanceOf(Client::class, $this->getClient());
    }

    /**
     * Validate passing configurations to the constructor
     *
     * @dataProvider clientConfigParameters
     */
    public function testClientConstructorWithConfig($name, $value)
    {
        $defaultConfig = $this->getClient();
        $config = new Client([$name => $value]);
        
        $this->assertEquals($config->$name, $value);
    }

    /* Validate we raise an exception on an unknown config parameter */
    public function testClientConstructorWithException()
    {
        $this->expectException(\RuntimeException::class);
        $config = new Client(['foo' => 'bar']);
    }

    /**
     * Validate our setting magic function works
     *
     * @dataProvider clientConfigParameters
     */
    public function testClientSetter($name, $value)
    {
        $this->assertEquals($this->getClient()->$name = $value, $value);
    }

    /* Validate our setter throws an exception when we don't have a matching property */
    public function testClientSetterException()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->getClient()->foo = 1;
    }

    /* Validate our getter magic function works */
    public function testClientGetter()
    {
        $apiKey = 1;
        $this->getClient()->apiKey = $apiKey;
        $this->assertEquals($this->getClient()->apiKey = $apiKey, $apiKey);
    }

    /* Calidate our getter raises an exception when a required value is not configured */
    public function testClientGetterException()
    {
        $this->expectException(\RuntimeException::class);
        $client = new Client();
        $client->apiKey;
    }

    /* Validate our getter throws an exception when we don't have a matching property */
    public function testClientGetterExceptionNoProperty()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->getClient()->foo;
    }
    
    /* Validate our getter throws an exception when we don't have a matching property */
    public function testClientPing()
    {
        $this->addMockResponse("pingOk");
        $this->assertTrue($this->getClient()->ping());
    }
}

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

use PHPUnit\Framework\TestCase;

use PartsLogic\Client;
use PartsLogic\Tests\Config;

class ConfigTest extends TestCase
{
    public function testConfigConstructor()
    {
        $this->assertInstanceOf(Config::class, new Config());
    }

    public function testGetPropertyName()
    {
        $propertyName = Config::ENV_PREFIX . "_TEST_PROPERTY_NAME";
        $expectedName = "testPropertyName";

        $config = new Config();
        $this->assertEquals($config->getPropertyFromEnvName($propertyName), $expectedName);
    }

    public function testGetPropertyNamePrefixOnly()
    {
        $propertyName = Config::ENV_PREFIX;

        $config = new Config();
        $this->assertNull($config->getPropertyFromEnvName($propertyName));
    }

    public function testGetPropertyNamePrefixAndUnderscore()
    {
        $propertyName = Config::ENV_PREFIX . "_";

        $config = new Config();
        $this->assertNull($config->getPropertyFromEnvName($propertyName));
    }

    public function testGetPropertyNameNoMatch()
    {
        $propertyName = "NO_MATCH" . Config::ENV_PREFIX . "_TEST_PROPERTY_NAME";

        $config = new Config();
        $this->assertNull($config->getPropertyFromEnvName($propertyName));
    }
    
    public function environmentSettings(): array
    {
        return [
            'apiKey'   => [ Config::ENV_PREFIX . "_API_KEY", "new-api-key" ],
            'endpoint' => [ Config::ENV_PREFIX . "_API_ENDPOINT", "new-endpoint" ],
            'mock'     => [ Config::ENV_PREFIX . "_USE_MOCK_RESPONSES", 0 ],
        ];
    }

    /**
     * @dataProvider environmentSettings
     */
    public function testEnvOverride($name, $value)
    {
        $defaultConfig = new Config();
        $config = new Config([$name => $value]);
        $property = $config->getPropertyFromEnvName($name);
        
        $this->assertEquals($config->$property, $value);
    }
}

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

/**
 * Test Configuration Object
 *
 * Will use the default values unless overridden with environment variables.
 * These variables are prefixed with PARTSLOGIC and are all caps with
 * underscores. The prefix is removed and the name is camelcased to get
 * the property name.
 *
 * example:
 *
 *   PARTSLOGIC_API_KEY=some-new-key
 */
class Config
{
    const ENV_PREFIX          = "PARTSLOGIC";
    private $apiKey           = 'test-api-key';
    private $apiEndpoint      = 'https://api.sunhammer.io';
    private $useMockResponses = true;
    private $enableDebug      = false;

    public function __construct($env = null)
    {
        if (! isset($env) ) {
            $env = $_ENV;
        }

        $this->setConfigFromEnvironment($env);
    }

    public function __get($property)
    {
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

    private function setConfigFromEnvironment($env)
    {
        foreach ($env as $name => $value) {
            $property = $this->getPropertyFromEnvName($name);

            if ($property != null) {
                $this->$property = $value;
            }
        }
    }

    public function getPropertyFromEnvName($name)
    {
        if (! preg_match('/^' . self::ENV_PREFIX . '_' . '/', $name)) {
            return null;
        }

        $noPrefix = str_replace(self::ENV_PREFIX . '_', '', $name);
        $property = str_replace('_', '', lcfirst(ucwords(strtolower($noPrefix), '_')));

        return strlen($property) == 0 ? null : $property;
    }
}

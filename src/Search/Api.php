<?php
/*
 * Copyright 2024 PartsLogic Inc.
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


namespace PartsLogic\Search;

use PartsLogic\Search\HealthCheck;
use PartsLogic\Search\Brands;
use PartsLogic\Search\Categories;
use PartsLogic\Search\Products;
use PartsLogic\Search\Fitment\Labels;
use PartsLogic\Search\Fitment\Labels\Makes;
use PartsLogic\Search\Fitment\Labels\Models;
use PartsLogic\Search\Fitment\Labels\Years;
use PartsLogic\Search\Fitment\Check;

/**
 * Parts Logic Container Class for Search API
 */
class Api
{
    /* List of all available api objects */
    const API_OBJECTS = [
        'healthcheck' => HealthCheck::class,
        'brands' => Brands::class,
        'categories' => Categories::class,
        'products' => Products::class,
        'fitment' => [
            'check' => Fitment\Check::class,
            'labels' => Fitment\Labels::class
        ]
    ];

    /**
     * Construct the PartsLogic Search API.
     *
     * @param \PartsLogic\Client $client Parts Logic API client
     */
    public function __construct($client)
    {
        foreach (self::API_OBJECTS as $key => $value) {
            if (is_array($value)) {
                $this->$key = (object)[];  // @phpstan-ignore-line

                foreach ($value as $name => $class) {
                    $this->$key->$name = new $class($client);
                }
            } else {
                $this->$key = new $value($client); // @phpstan-ignore-line
            }
        }
    }

    /**
     * API Object Getter
     *
     * Returns the api object saved in that name
     *
     * @param string $property name of the property to retrieve
     */
    public function __get($property)
    {
        if (! in_array($property, array_keys(self::API_OBJECTS))) {
            throw new \OutOfBoundsException("'$property' is not a defined api object.");
        }

        return $this->$property;
    }
}

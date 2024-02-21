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


namespace PartsLogic\Search\Fitment\Labels;

use PartsLogic\Search\Base;

/**
 * Parts Logic Container Class for Search API
 */
class Values extends Base
{
    protected $path = 'fitment/labels';
    private $fitmentName;

    protected $requiredParameters = ['groupId'];
    protected $optionalParameters = ['parents'];


    /**
     * Construct the PartsLogic Search API.
     *
     * @param \PartsLogic\Client $client PartsLogic API Client
     */
    public function __construct($client, $fitmentName)
    {
        parent::__construct($client);
        $this->fitmentName = $fitmentName;
    }

    /**
     * Generate the Relative URI for a request.
     *
     * The query array passed in must be an associative array and will be added as query
     * args to the URI object.
     *
     * @param array $query array of key/value pairs to add to the request uri
     */
    public function uri($query = [])
    {
        $parts = [$this->path . "/" . $this->fitmentName];
        if (! empty($query)) {
            array_push($parts, $this->buildQuery($query));
        }

        return new \GuzzleHttp\Psr7\Uri(join('&', $parts));
    }

        /**
     * Format parameters for request
     *
     * @param array $query list of key/value pairs
     * @return string Formed query string
     */
    public function buildQuery($query)
    {
        $clean = [];
        foreach ($query as $name => $value) {
            $value = (array) $value;
            array_walk_recursive($value, function ($value) use (&$clean, $name) {
                $clean[] = urlencode($name) . '=' . urlencode($value);
            });
        }
        return implode("&", $clean);
    }
}

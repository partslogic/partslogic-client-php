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

use PartsLogic\Search\Response;
use PartsLogic\Exception\InvalidArgumentsException;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Monolog\Logger;

/**
 * Parts Logic Base Class for Search API Objects
 */
class Base
{
    /**
     * @var \PartsLogic\Client PartsLogic API Client
     */
    protected $client;

    /**
     * @var string Relative url to use for the request.
     *
     * This value MUST be overloaded in the derived classes
     */
    protected $path = null;

    /**
     * @var array List of parameter object for request validation
     *
     * If not overloaded in derived class requests wont accept any
     * arguments.
     */
    protected $parameters = [];

    /**
     * @var array List of parameters that are required.
     *
     * This is a shortcut to add minimal required parameters.
     */
    protected $requiredParameters = [];

    /**
     * @var array List of parameters that are optional.
     *
     * This is a shortcut to add minimal optional parameters.
     */
    protected $optionalParameters = [];

    /**
     * @var array list of errors from last request
     */
    protected $errors;

    /**
     * @var Logger Logger object
     */
    protected $logger;

    /**
     * Construct the PartsLogic Search API.
     *
     * @param \PartsLogic\Client $client PartsLogic API Client
     */
    public function __construct($client)
    {
        if ($this->path === null) {
            throw new \RuntimeException("path must be overloaded in derived class");
        }

        foreach ($this->requiredParameters as $name) {
            $this->addParameter($name, new Parameter(true));
        }

        foreach ($this->optionalParameters as $name) {
            $this->addParameter($name, new Parameter());
        }

        $this->client = $client;
        $this->logger = $client->getLogger();
    }

    /**
     * Send a get request to the PartsLogic API and return a response
     *
     * @param array $query Query parameters to send to the request
     * @return Response PartsLogic API reponse object
     */
    public function get($query = [])
    {
        $request = $this->buildRequest('GET', $query);
        $response = $this->sendRequest($request);

        return new Response($response);
    }

    /**
     * send request through the API client
     *
     * @param \GuzzleHttp\Psr7\Request $request Guzzle Client request
     * @return \Psr\Http\Message\ResponseInterface Guzzle Client response
     */
    public function sendRequest($request)
    {
        return $this->client->send($request);
    }

    /**
     * Build the Guzzle request object from api methods and passed in args
     *
     * @param string $method HTTP request method
     * @param array $query Any caller parameters needed to generate the request
     *
     * @return \GuzzleHttp\Psr7\Request freshly minted request object
     */
    public function buildRequest($method, $query = [])
    {
        $this->validate($query);
        return new \GuzzleHttp\Psr7\Request('GET', $this->uri($query), $this->headers());
    }

    /**
     * Validate request parameters
     *
     * @param array $query key/value parameter pairs
     * @return bool true if valid
     * @throws \PartsLogic\Exception\InvalidArgumentsException
     */
    public function validate($query)
    {
        $this->errors = [];

        $queryKeys = array_keys($query);

        # If we have parameters defined for this request then validate each of them.
        if (count($this->parameters)) {
            foreach ($this->parameters as $name => $param) {
                if (in_array($name, $queryKeys)) {
                    array_splice($queryKeys, array_search($name, $queryKeys), 1);
                    if (! $param->validate($query[$name])) {
                        array_push($this->errors, "${name} validation failed");
                    }
                } else {
                    if ($param->isRequired()) {
                        array_push($this->errors, "${name} is required");
                    }
                }
            }
        }

        # If we haven't validated a parameter in the request query that
        # means it is invalid for this requst.
        foreach ($queryKeys as $name) {
            array_push($this->errors, "${name} is an invalid parameter");
        }

        # Raise an exeception if any errors are detected.
        if (count($this->errors) > 0) {
            throw new \PartsLogic\Exception\InvalidArgumentsException(print_r($this->errors, true));
        }

        # We made it through validation.   We are good.
        return true;
    }

    /**
     * Push a new parameter into the request object
     *
     * @param string $name Parameter name
     * @param Parameter $param Parameter to add
     */
    public function addParameter($name, $param)
    {
        $this->parameters[$name] = $param;
    }

    /**
     * Get the error from the last request
     *
     * @return array list of error strings from the last request
     */
    public function getErrors()
    {
        return $this->errors;
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
        $uri = new \GuzzleHttp\Psr7\Uri($this->path);
        return \GuzzleHttp\Psr7\Uri::withQueryValues($uri, $this->formatQueryParams($query));
    }

    /**
     * Format parameters for request
     *
     * @param array $query list of key/value pairs
     * @return array List of sanitized query parameters
     */
    public function formatQueryParams($query)
    {
        if (empty($query)) {
            return $query;
        }

        $rv = [];
        foreach ($query as $name => $value) {
            if (empty($this->parameters[$name])) {
                throw new \RuntimeException("no parameter defined for '${name}'");
            }

            $rv[$name] = $this->parameters[$name]->sanitize($value);
        }

        return $rv;
    }

    /**
     * Add additional headers to the request
     *
     * note: api key is handled in the client
     * @return array Key/value pairs to send as headers with the request
     */
    public function headers()
    {
        return [];
    }
}

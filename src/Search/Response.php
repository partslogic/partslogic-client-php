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

/**
 * Parts Logic Base Class for Search API Objects
 */
class Response
{
    /**
     * integer http status code of request
     */
    private $httpStatus;

    /**
     * object parsed result of request body
     */
    private $body;

    /**
     * array list of headers returned from the request
     */
    private $headers;

    /**
     * HTTP Code considered success
     */
    private $successCode = 200;

    /**
     * Construct the PartsLogic Search API Response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response The Guzzle http response object
     */
    public function __construct($response)
    {
        $this->decodeResponse($response);
    }

    /**
     * Return the decoded body from the http response
     *
     * @return object struct containing decoded body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Was the request successful?
     *
     * @return bool Was the status code what we expected for success?
     */
    public function isSuccess()
    {
        return $this->httpStatus == $this->successCode;
    }

    /**
     * Return the reponse http status code
     *
     * @return integer Http status code response
     */
    public function getStatusCode()
    {
        return $this->httpStatus;
    }

    /**
     * Return all headers from the response
     *
     * @return array key/value pairs of all headers
     */
    public function getheaders()
    {
        return $this->headers;
    }

    private function decodeResponse($response)
    {
        $this->httpStatus = $response->getStatusCode();
        $this->headers = $this->decodeHeaders($response->getHeaders());
        $this->body = $this->decodeBody($response->getBody());
    }

    private function decodeBody($responseBody)
    {
        return json_decode($responseBody);
    }

    private function decodeHeaders($responseHeaders)
    {
        return $responseHeaders;
    }
}

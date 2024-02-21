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


namespace PartsLogic\Search\Fitment;

use PartsLogic\Search\Base;
use PartsLogic\Search\Response;
use PartsLogic\Search\Fitment\Labels\Values;

/**
 * Parts Logic Container Class for Search API
 */
class Labels extends Base
{
    protected $path = 'fitment/labels';

    protected $requiredParameters = ['groupId'];
    protected $optionalParameters = [];

    /**
     * Send a get request to the PartsLogic API and return a response
     *
     * @param string $fitmentName Name of the fitment to lookup
     * @param array $query Query parameters to send to the request
     * @return Response PartsLogic API reponse object
     */
    public function getValues($fitmentName, $query = [])
    {
        $value = new Values($this->client, $fitmentName);

        return $value->get($query);
    }
}

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
 * Parts Logic Search Parameter for validation
 */
class Parameter
{
    /**
     * separator for query args
     */
    const PARAM_JOIN_CHAR = "|";
    /**
     * @var bool is the parameter required
     */
    private $required;

    /**
     * Construct the PartsLogic Search Parameter.
     *
     * @param bool $required is the parameter required?
     */
    public function __construct($required = false)
    {
        $this->required = $required;
    }

    /**
     * Accessor for required
     *
     * @return bool is parameter required
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Validate parameter value
     *
     * @return bool Always true for now.
     */
    public function validate()
    {
        return true;
    }

    /**
     * Clean a value for request
     *
     * @param mixed $value either a list or a string
     * @return string Safe string to pass to http request
     */
    public function sanitize($value) : string
    {
        return is_array($value) ? join(self::PARAM_JOIN_CHAR, $this->cleanList($value)) :
                                  strval($value);
    }

    private function cleanList($values, $delimeter = self::PARAM_JOIN_CHAR)
    {
        $rv = [];
        foreach ($values as $key => $value) {
            $rv[$key] = str_replace($delimeter, urlencode($delimeter), $value);
        }

        return $rv;
    }
}

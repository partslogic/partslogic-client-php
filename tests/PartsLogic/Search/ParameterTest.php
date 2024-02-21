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

namespace PartsLogic\Tests\Search;

use PartsLogic\Tests\BaseTest;
use PartsLogic\Search\Parameter;

class ParameterTest extends BaseTest
{
    public function testConfigConstructor()
    {
        $param = new Parameter();
        $this->assertInstanceOf(Parameter::class, $param);
        $this->assertFalse($param->isRequired());
    }

    public function testConfigRequiredConstructor()
    {
        $param = new Parameter(true);
        $this->assertInstanceOf(Parameter::class, $param);
        $this->assertTrue($param->isRequired());
    }

    public function parameterValues()
    {
        return [
            'string' => [ 'string', 'string' ],
            'onItemList' => [ ['string'], 'string' ],
            'twoItemList' => [ [1, 2], '1|2' ],
            'listWithDelimeter' => [ ['a|b'], 'a%7Cb' ],
            'stringWithDelimeter' => [ 'a|b', 'a|b' ],
            'integer' => [ 1, '1' ]
        ];
    }

    /**
     * Validate our sanitize function
     *
     * @dataProvider parameterValues
     */
    public function testParameterSanitize($value, $expectedValue)
    {
        $param = new Parameter();
        $this->assertEquals($param->sanitize($value), $expectedValue);
    }
}

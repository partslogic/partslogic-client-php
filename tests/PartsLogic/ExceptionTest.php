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

use PartsLogic\Tests\BaseTest;

use PartsLogic\Exception;
use PartsLogic\Exception\BadRequestException;

class ExceptionTest extends BaseTest
{
    public function exceptionClasses(): array
    {
        return [
            'base'   => [ \PartsLogic\Exception::class ]
        ];
    }

    /**
     * @dataProvider exceptionClasses
     */
    public function testExceptionConstructor($exceptionClass)
    {
        $errorMessage = "test error";
        $exception = new $exceptionClass($errorMessage);
        $this->assertInstanceOf($exceptionClass, $exception);
        $this->assertInstanceOf(Exception::class, $exception);
    }
}

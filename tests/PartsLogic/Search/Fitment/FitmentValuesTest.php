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

namespace PartsLogic\Tests\Search\Fitment;

use PartsLogic\Search\Fitment\Labels\Values;
use PartsLogic\Search\Response;
use PartsLogic\Tests\ApiTest;

class FitmentValuesTest extends ApiTest
{
    protected $objClass = \PartsLogic\Search\Fitment\Labels\Values::class;

    public function buildRequest($labelName = "test'")
    {
        return new $this->objClass($this->getClient(), $labelName);
    }

    public function uriParameters(): array
    {
        return [
            'no-query'   => [ [], null ],
            'one-parameter' => [
                ['test' => 'foo'],
                "test=foo"
            ],
            'one-parameter-encoded' => [
                ['test' => "with space"],
                'test=with+space'
            ],
            'two-parameters' => [
                [ 'test' => 1, 'foo' => 2 ],
                "test=1&foo=2"
            ],
            'duplicate-parameters-bad' => [
                [ 'test' => 1, 'test' => 2 ],
                "test=2"
            ],
            'duplicate-parameters-good' => [
                [ 'test' => [1, 2] ],
                "test=1&test=2"
            ]
        ];
    }

    /**
     * Validate our uri generation function
     *
     * @dataProvider uriParameters
     */
    public function testUrlGeneration($query, $expectedString)
    {
        $label = "year";
        $request = $this->buildRequest($label);
        $baseUri = "fitment/labels/" . $label;
        $queryString = $request->buildQuery($query);
        $expectedUri = empty($query) ? $baseUri :
                                       join("?", [$baseUri, $queryString]);

        $this->assertEquals("fitment/labels/year", $baseUri);
        $this->assertEquals($queryString, $expectedString);
        $this->assertEquals($expectedUri, $request->uri($query));
    }

    public function testFitmentYearsOk()
    {
        $label = "s";
        $request = $this->buildRequest($label);
        $params = [ 'groupId' => 1 ];
        $this->addMockResponse("labelsYearOk");
        $response = $request->get($params);

        $this->assertInstanceOf(Response::class, $response);
        $body = $response->getBody();

        $this->assertTrue($response->isSuccess());
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertTrue(is_array($body));

        if ($this->config->useMockResponses) {
            $this->assertEquals(sizeof($body), 1);
            $item = $body[0];
            $this->assertEquals($item->id, '649f6e79b6e15308f92b1484');
            $this->assertEquals($item->groupId, '1');
            $this->assertEquals($item->label, 'Year');
            $this->assertEquals($item->value, '2024');
            $this->assertEquals($item->priority, '998');
        }
    }
}

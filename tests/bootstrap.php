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

require_once __DIR__ . '/../vendor/autoload.php';

const MOCK_RESPONSES_DIR = __DIR__ . "/mock/responses";

/** Load all support code from /tests/support **/
$files = glob(__DIR__ . '/support/*.php');
foreach ($files as $file) {
    require_once $file;
}

date_default_timezone_set('UTC');

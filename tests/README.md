# PartsLogic Developer Guide #

## Summary ##

A docker container is provided to facilitate development and testing of the client library. 

### Provided Tools

* PHPStan for Static Code Analysis
* PHPUnit for running tests
* PHP Code Scanner for Linting

## Requirements ##

* Docker

### Git Configuration 

Make sure you have ignorecase turned off in git.  The PHP autoloader cares about case. 

```git config core.ignorecase false```

## Test Configuration ##

Configurations are set via environment variables with defaults defined in tests/support/Config.php.

|variable|description|default|
|--------|-----------|-------|
|PARTSLOGIC_API_KEY|Client API Key|test-api-key|
|PARTSLOGIC_API_ENDPOINT|PartLogic API Endpoint|https://api.sunhammer.io|
|PARTSLOGIC_ENABLE_DEBUG|Enable debug logging|false|
|PARTSLOGIC_USE_MOCK_RESPONSES|Mock PartsLogic responses or connect to the endpoint|true|

## Running Tests ##

```docker compose run app phpunit```

## Running Static Code Analysis ##

```docker compose run app phpstan```

## Running Code Linter ##

```docker compose run app phpcs src tests -nps```

## Fix Linter Issues ##

```docker compose run app phpcbf src tests```

## Mocking Responses ##

By default $this->config->useMockResponses is enabled.  Mock responses are stored in tests/mock
responses as yamle files.  The yaml file contains three top level elements; status, headers, body. 

* status HTTP response code as a number
* response headers key: value pairs. **optional**
* body as a string or object.  If an object response will be json encoded

files are named by their identifer and can have a yaml or yml extension. 

### Adding Mock Responses

In your test you can add a mock response using the addMockResponse method in the test base class.  

example: 

**tests/mock/responses/pingOk.yaml**
```yaml
status: 200
headers: 
  head: value
  auth: please
body: OK
```
and the test that uses this mock response:
```php
    public function testClientPing()
    {
        $this->addMockResponse("pingOk");
        $this->assertTrue($this->getClient()->ping());
    }
```

### Limitations

The response object does not support multiline yaml entries using | or >.  This is a know limitation of the yaml parser we are using.  If you need a multi line response you will be required to use quotes. 

example:

```yaml
status: 200
body: 'This 
is a multiline
response'
```
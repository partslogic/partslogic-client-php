* Add request failure handling 
  - Add error handling to base 
  - What happens when the http request fails? 
  - What happens when we get a http response code other than success? 
 
* Add parameter validation to api object requests --- YES, check required, known parameter, 

* Do we need item level decoding at the response level. 
  - For example, healthcheck returns an uptime number as a string.  Should that be validated and transcoded in the response? 
 
* Add negative testing for api reponses

* Do we need custom accessors in the response to grab elements? 
  - Currently you get the valuse with ```$response->getBody()->propertyName```
  - Do we want ```$response->getResponseData('propertyName');```
  - Using the first syntax the property could not exist and is prone to typo errors
/*
https://replit.com/@BugStorm/MeaslyExternalMegabyte-1

*/

<?php

/****************************************
Example showing API Signature calculation
for an API call to the /v2/current/{station-id}
API endpoint
****************************************/

/*
Here is the list of parameters we will use for this example.
*/
$parameters = array(
  "api-key" => "3235315",
  "api-secret" => "ms154125s",
  "t" => time()
);

/*
Now we will compute the API Signature.
The signature process uses HMAC SHA-256 hashing and we will
use the API Secret as the hash secret key. That means that
right before we calculate the API Signature we will need to
remove the API Secret from the list of parameters given to
the hashing algorithm.
*/

/*
First we need to sort the paramters in ASCII order by the key.
The parameter names are all in US English so basic ASCII sorting is
safe.
*/
ksort($parameters);

/*
Let's take a moment to print out all parameters for debugging
and educational purposes.
*/
foreach ($parameters as $key => $value) {
  echo "Parameter name: \"$key\" has value \"$value\"\n";
}

/*
Save and remove the API Secret from the set of parameters.
*/
$apiSecret = $parameters["api-secret"];
unset($parameters["api-secret"]);

/*
Iterate over the remaining sorted parameters and concatenate
the parameter names and values into a single string.
*/
$data = "";
foreach ($parameters as $key => $value) {
  $data = $data . $key . $value;
}

/*
Let's print out the data we are going to hash.
*/
echo "Data string to hash is: \"$data\"\n";

/*
Calculate the HMAC SHA-256 hash that will be used as the API Signature.
*/
$apiSignature = hash_hmac("sha256", $data, $apiSecret);

/*
Let's see what the final API Signature looks like.
*/
echo "API Signature is: \"$apiSignature\"\n";

/*
Now that the API Signature is calculated let's see what the final
v2 API URL would look like for this scenario.
*/
echo "v2 API URL: https://api.weatherlink.com/v2/stations/". 
  "?api-key=" . $parameters["api-key"] . 
  "&api-signature=" . $apiSignature . 
  "&t=" . $parameters["t"] . 
  "\n";

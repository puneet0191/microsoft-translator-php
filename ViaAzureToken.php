<?php

class AccessTokenAuthentication {

	/*
	 * Get the access token.
	 *
	 * @param string $azure_key    Azure Key for Token.
	 *
	 * @return string.
	 */
	function getToken($azure_key)
	{
		$url = 'https://api.cognitive.microsoft.com/sts/v1.0/issueToken';
		$ch = curl_init();
		$data_string = json_encode('{body}');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string),
				'Ocp-Apim-Subscription-Key: ' . $azure_key
			)
		);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$strResponse = curl_exec($ch);
		curl_close($ch);
		return $strResponse;
	}
}

/*
 * Class:HTTPTranslator
 *
 * Processing the translator request.
 */
Class HTTPTranslator {

	/*
	 * Create and execute the HTTP CURL request.
	 *
	 * @param string $url        HTTP Url.
	 * @param string $authHeader Authorization Header string.
	 *
	 * @return string.
	 *
	 */
	function curlRequest($url, $authHeader)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader, "Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, False);
		$curlResponse = curl_exec($ch);
		curl_close($ch);
		return $curlResponse;
	}
}

try {
	$azure_key = "KEY_1";  // !!! TODO: secret key here !!!
	$fromLanguage = "en";
	$toLanguage = "de";
	$inputStr = "AZURE test string";

	//Create the AccessTokenAuthentication object.
	$authObj      = new AccessTokenAuthentication();

	$accessToken = $authObj->getToken($azure_key);
	$params = "text=" . urlencode($inputStr) . "&to=" . $toLanguage . "&from=" . $fromLanguage . "&appId=Bearer+" . $accessToken;
	$translateUrl = "http://api.microsofttranslator.com/v2/Http.svc/Translate?$params";
	//Create the Translator Object.
	$translatorObj = new HTTPTranslator();

	$curlResponse = $translatorObj->curlRequest($translateUrl, $authHeader);
	$xmlObj = simplexml_load_string($curlResponse);
	foreach ((array)$xmlObj[0] as $val) {
		$translatedStr = $val;
	}

	echo $translatedStr;

} catch (Exception $e) {
	echo "Exception: " . $e->getMessage() . PHP_EOL;
}
?>

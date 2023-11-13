<?php
/**
 * Copyright © 2021 Accretive Technology Group
 *
 * This code is provided for demonstration purposes only.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the “Software”), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */
// namespace icf\lib\auth;

namespace App\Library\BL;

/**
 * Class AuthSdk
 *
 * This SDK is used to generate Auth headers, and optionally
 * send requests with authorization
 *
 * @package icf\lib\auth
 */
class AuthSdk
{
    public const DEFAULT_HMAC_ALGORITHM = 'SHA512';
    public const DEFAULT_HEADER_FORMAT = 'ICF1-HMK-SHA512 timestamp=%s;applicationid=%s;signature=%s';
    public const EXPECTED_AUTHN_HEADER_COMPONENT_COUNT = 6;
    public const MAX_AUTHN_HEADER_COMPONENT_SPLIT_COUNT = 2;
    public const EXPECTED_SCHEME_COMPONENTS = 3;
    public const EXPECTED_TOKEN_COMPONENTS = 3;

    /**
     * Representing the applicationid, should always be non-empty
     *
     * @var string
     */
    private $sApplicationId;

    /**
     * Representing the secret, should always be non-empty
     *
     * @var string
     */
    private $sSecret;

    /**
     * AuthSdk constructor.
     *
     * @param string $sApplicationId applicationid that was given to you
     *
     * @param string $sSecret
     *
     * @throws \InvalidArgumentException If the applicatinoid or the secret is empty
     */
    public function __construct($sApplicationId, $sSecret)
    {
        if (empty($sApplicationId)) {
            throw new \InvalidArgumentException('AuthSdk: Must give non-empty applicationid');
        }

        if (empty($sSecret)) {
            throw new \InvalidArgumentException('AuthSdk: Must give non-empty secret');
        }

        $this->sApplicationId = (string) $sApplicationId;
        $this->sSecret = (string) $sSecret;
    }

    /**
     * Generates the auth header for the given unique request data
     *
     * @param string $sMethod http method that will be used
     *
     * @param string $sUrl the fully qualified url
     *
     * @param array $aParameters the parameters that will be handled
     * conditional on the method type given. 'GET' methods will be
     * handled as query strings, 'POST' methods will be handled as
     * json rawbody
     *
     * @return mixed
     */
    public function generateHeader($sMethod, $sUrl, array $aParameters = [])
    {
        /**
         * Start off with some guard cases
         */
        if (empty($sMethod)) {
            throw new \InvalidArgumentException("Must have non-empty value for method");
        }

        if (empty($sUrl)) {
            throw new \InvalidArgumentException("Must have non-empty value for url");
        }

        if (strpos($sUrl, '?') !== false) {
            throw new \InvalidArgumentException("Must not have a query string in the url given; please use the optional parameters instead");
        }

        // we need to parse the url to get the path
        $aUrl = parse_url($sUrl);

        // we know if we didn't find host then it wasn't fully qualified
        if (empty($aUrl['host']) || empty($aUrl['scheme'])) {
            throw new \InvalidArgumentException("Unable to parse url given; please ensure url is fully qualified");
        }

        // pull out the required data from the needed variables
        $sMethod = strtoupper($sMethod);
        $sPath = (isset($aUrl['path'])) ? $aUrl['path'] : "";
        $sTimeStamp = (string) time();
        list($sQueryString, $sRawBody) = $this->parseParamsIntoQueryStringAndRawBody($sMethod, $aParameters);

        // generate the signature
        $sSignature = $this->generateSignature($sMethod, $sPath, $sTimeStamp, $sQueryString, $sRawBody);

        // generate the header
        return sprintf(self::DEFAULT_HEADER_FORMAT, $sTimeStamp, $this->sApplicationId, $sSignature);
    }

    /**
     * Authenticates an incoming auth header by comparing it with an auth header generated for the same unique request data
     *
     * @param string $sHeaderToAuthenticate the header that will be authenticated
     *
     * @param string $sMethod http method that will be used
     *
     * @param string $sUrl the fully qualified url
     *
     * @param array $aParameters the parameters that will be handled
     * conditional on the method type given. 'GET' methods will be
     * handled as query strings, 'POST' methods will be handled as
     * json rawbody
     *
     * @throws \InvalidArgumentException if there are bad params in the header
     *  exception message will indicate which param was bad and why
     *
     * @return bool
     */
    public function authenticateHeader($sHeaderToAuthenticate, $sMethod, $sUrl, array $aParameters = array())
    {
        if (empty($sMethod)) {
            throw new \InvalidArgumentException("Must have non-empty value for method");
        }

        if (empty($sUrl)) {
            throw new \InvalidArgumentException("Must have non-empty value for url");
        }

        if (strpos($sUrl, '?') !== false) {
            throw new \InvalidArgumentException("Must not have a query string in the url given; please use the optional parameters instead");
        }

        // we need to parse the url to get the path
        $aUrl = parse_url($sUrl);

        // we know if we didn't find host then it wasn't fully qualified
        if (empty($aUrl['host']) || empty($aUrl['scheme'])) {
            throw new \InvalidArgumentException("Unable to parse url given; please ensure url is fully qualified");
        }
        $sMethod = strtoupper($sMethod);

        $sPath = (isset($aUrl['path'])) ? $aUrl['path'] : "";

        $aHeaderPieces = $this->extractAuthNParamsFromHeader($sHeaderToAuthenticate);
        //Validate various variables
        if(empty($aHeaderPieces)) {
            throw new \InvalidArgumentException("Header is empty; please ensure the header is properly formatted");
        }
        if($aHeaderPieces['version'] !== "ICF1") {
            throw new \InvalidArgumentException("The header version is out of date; please ensure you are using the latest version of ICF SDKs");
        }
        if($aHeaderPieces['type'] !== "HMK") {
            throw new \InvalidArgumentException("The header type is incorrect; please ensure you are using the correct header");
        }
        if($aHeaderPieces['algorithm'] !== "SHA256" && $aHeaderPieces['algorithm'] !== "SHA512") {
            throw new \InvalidArgumentException("The header is incorrectly encoded; please validate your encoding algorithm");
        }
        if(!ctype_digit($aHeaderPieces['timestamp'])) {
            throw new \InvalidArgumentException("The header timestamp is not an integer. Please validate your timestamp.");
        }
        if($aHeaderPieces['timestamp'] > time() + 60 || $aHeaderPieces['timestamp'] < time() - 60) {
            throw new \InvalidArgumentException("The header timestamp is out of date. Please try again at a later time.");
        }
        if($aHeaderPieces['applicationid'] !== $this->sApplicationId) {
            throw new \InvalidArgumentException("The header application ID is incorrect. Please validate your applicationID.");
        }

        list($sQueryString, $sRawBody) = $this->parseParamsIntoQueryStringAndRawBody($sMethod, $aParameters);

        // generate the signature
        $sSignature = $this->generateSignature($sMethod, $sPath, $aHeaderPieces['timestamp'], $sQueryString, $sRawBody);

        return $sSignature === $aHeaderPieces['signature'];
    }


    /**
     * Parses the header, including the scheme and token, for the associated properties
     *
     * @param string $sHeader header to be parsed
     *
     * @return array contains the scheme parts and the token parts
     */
    private function extractAuthNParamsFromHeader($sHeader)
    {
        $sHeader = (string) $sHeader;

        $aHeader = explode(' ', $sHeader, self::MAX_AUTHN_HEADER_COMPONENT_SPLIT_COUNT);

        if (count($aHeader) !== self::MAX_AUTHN_HEADER_COMPONENT_SPLIT_COUNT) {
            // Not AuthN
            return [];
        }

        list($sScheme, $sToken) = $aHeader;

        $aExplodedHeader = [];
        $aExplodedHeader += $this->explodeScheme($sScheme);
        // If the key exists in both arrays, the keys from $aExplodedHeader will be used
        $aExplodedHeader += $this->explodeToken($sToken);

        if (count($aExplodedHeader) !== self::EXPECTED_AUTHN_HEADER_COMPONENT_COUNT) {
            return [];
        }

        return $aExplodedHeader;
    }

    /**
     * Parses the scheme for the associated properties
     *
     * @param string $sScheme scheme to be parsed
     *
     * @return array
     */
    private function explodeScheme($sScheme)
    {
        $sScheme = (string) $sScheme;
        $aScheme = explode('-', $sScheme);

        // Our Auth headers should only have 3 components in the scheme
        if (count($aScheme) !== self::EXPECTED_SCHEME_COMPONENTS) {
            return [];
        }

        list($sVersion, $sType, $sAlgo) = $aScheme;

        return [
            'version' => $sVersion,
            'type' => $sType,
            'algorithm' => $sAlgo,
        ];
    }

    /**
     * Parses the token for the associated properties
     *
     * @param string $sToken token to be parsed
     *
     * @return array
     */
    private function explodeToken($sToken)
    {
        $sToken = (string) $sToken;
        $aToken = explode(';', $sToken);

        if (count($aToken) !== self::EXPECTED_TOKEN_COMPONENTS) {
            // Not AuthN
            return [];
        }

        $aExplodedToken = [];

        foreach ($aToken as $sTokenField) {
            $aTokenField = explode('=', $sTokenField);

            if (count($aTokenField) === 2) {
                list($sTokenKey, $sTokenValue) = $aTokenField;
                $aExplodedToken[$sTokenKey] = $sTokenValue;
            }
        }

        return $aExplodedToken;
    }

    /**
     * Turns an array of parameters into the query string and rawbody
     *
     * @param string $sMethod Representing the method that will be
     * the condition causing the querystring or rawbody to be generated
     *
     * @param array $aParameters data to be parsed
     *
     * @return array(querystring, rawbody)
     */
    protected function parseParamsIntoQueryStringAndRawBody($sMethod, array $aParameters)
    {
        $sMethod = strtoupper($sMethod);
        $sQueryString = '';
        $sRawBody = '';

        // handle the parameters conditional on the http method
        switch ($sMethod) {
            case 'GET':
                $sQueryString = http_build_query($aParameters);
                break;

            case 'POST':
                if (!empty($aParameters)) {
                    $sRawBody = json_encode($aParameters);

                    if ($sRawBody === false) {
                        throw new \InvalidArgumentException('Failed to encode post parameters to json');
                    }
                }
                break;

            default:
                throw new \InvalidArgumentException("Unknown http method given, method={$sMethod}");
        }

        return array($sQueryString, $sRawBody);
    }

    /**
     * Generates the hmac signature for the given data
     *
     * @param string $sAppId representing the application id
     * @param string $sSecret shared secret key
     * @param string $sMethod http method
     * @param string $sPath path, not including the url
     * @param string $sTimeStamp representing the timestamp in seconds since unix epoch
     * @param string $sQueryString query string that will be sent with request
     * @param string $sRawBody raw body that will be sent with request
     *
     * @throws \RuntimeException if the built in methods fail for unknown reason
     *
     * @return string generated signature
     */
    protected function generateSignature($sMethod, $sPath, $sTimeStamp, $sQueryString, $sRawBody)
    {
        // create the payload, which is the following format
        $sPayload = "{$sMethod}\n\n";
        $sPayload .= "{$this->sApplicationId}\n";
        $sPayload .= "{$sTimeStamp}\n";
        $sPayload .= "{$sPath}\n";
        $sPayload .= "{$sQueryString}\n";
        $sPayload .= "{$sRawBody}";

        // generate the hmac
        $sSignature = hash_hmac(self::DEFAULT_HMAC_ALGORITHM, $sPayload, $this->sSecret);

        // the hmac could return false, so check that
        if ($sSignature === false) {
            $sMsg = sprintf('Failed to create hmac with hash_hmac function payload=%s', $sPayload);
            throw new \RuntimeException($sMsg);
        }

        // then do a base 64 encode of the data
        $sSignature = base64_encode($sSignature);

        // base 64 encode could also return false, so lets check that too
        if ($sSignature === false) {
            $sMsg = sprintf('Failed to base64 encode the signature for unknown reason payload=%s signature=%s', $sPayload, $sSignature);
            throw new \RuntimeException($sMsg);
        }

        // then we urlencode, it is guaranteed to be a string
        $sSignature = urlencode($sSignature);

        return $sSignature;
    }

    /**
     * Performs a get request with auth
     *
     * @param string $sUrl representing the fully qualified URL
     *
     * @param array $aParameters the query parameters to be
     * sent with the get request. These parameters will be interpreted
     * as a query string. If this value is not included then they are
     * considered to be empty
     *
     * @param array $aHeaders (optional) the headers to be sent with the
     * get request. This should be a list of strings to include as the
     * headers. If this value is not included then only the standard headers
     * will be sent
     *
     * @return array
     */
    public function get($sUrl, array $aParameters = [], $aHeaders = [])
    {
        return $this->sendRequest('GET', $sUrl, $aParameters, $aHeaders);
    }

    /**
     * Performs a post request with auth
     *
     * @param string $sUrl the fully qualified url
     *
     * @param array $aParameters (optional) the parameters to be sent
     * with the post request. These parameters will be interpreted
     * as json raw body. If this value is not included then they are
     * considered to be empty
     *
     * @param array $aHeaders (optional) the headers to be sent with the
     * post request. This should be a list of strings to include as the
     * headers. If this value is not included then only the standard headers
     * will be sent
     *
     * @return array
     */
    public function post($sUrl, array $aParameters = [], array $aHeaders = [])
    {
        return $this->sendRequest('POST', $sUrl, $aParameters, $aHeaders);
    }

    /**
     * Sends the given request
     *
     * @param string $sMethod representing the http method
     *
     * @param string $sUrl the url that should be contacted
     *
     * @param array $aParameters parameters sent with request
     *
     * @param array $aHeaders optional headers
     *
     * @return array|string
     */
    protected function sendRequest($sMethod, $sUrl, array $aParameters, array $aHeaders)
    {
        if (empty($sUrl)) {
            throw new \InvalidArgumentException("Must have non-empty url. url={$sUrl}");
        }

        $sAuthHeader = $this->generateHeader($sMethod, $sUrl, $aParameters);
        $aHeaders['Authorization'] = "Authorization: {$sAuthHeader}";
        return $this->sendHttpRequest($sMethod, $sUrl, $aParameters, $aHeaders);
    }

    /**
     * Sends the http request to the given url, with the given headers, and the given data
     *
     * @param string $sMethod Representing the method of the request being made
     * @param string $sUrl Representing the url for the request to be sent to
     * @param array $aParameters parameters to be sent with request
     * @param array $aHeaders Representing the headers data that should be sent with the request
     *
     * @return array|string response
     */
    protected function sendHttpRequest($sMethod, $sUrl, array $aParameters, array $aHeaders)
    {
        if (!extension_loaded('curl')) {
            throw new \RuntimeException('Cannot send HTTP request curl extension is not installed');
        }

        // organize the options for the curl
        $aOptions = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $aHeaders,
        );

        list($sQueryString, $sRawBody) = $this->parseParamsIntoQueryStringAndRawBody($sMethod, $aParameters);

        switch ($sMethod) {
            case 'GET':
                $sUrl .= "?{$sQueryString}";
                break;

            case 'POST':
                $aOptions[CURLOPT_POST] = true;
                $aOptions[CURLOPT_POSTFIELDS] = $sRawBody;
                $aOptions[CURLOPT_HTTPHEADER]['Content-Length'] = 'Content-Length: ' . strlen($sRawBody);
                $aOptions[CURLOPT_HTTPHEADER]['Content-Type'] = 'Content-Type: application/json';
                break;

            default:
                throw new \InvalidArgumentException("Unknown/Unsupported http method given. method={$sMethod}");
        }

        // create the curl
        $hCurl = $this->initCurl($sUrl);

        // curl can be false, so check it
        if (false === $hCurl) {
            $sMsg = sprintf('Initializing curl returned false. Cannot perform curl; url=%s data=%s', $sUrl, print_r($aParameters, true));
            throw new \UnexpectedValueException($sMsg);
        }

        // set the options
        if (!$this->setCurlOpt($hCurl, $aOptions)) {
            $sMsg = sprintf('Failed to set options into curl object; options=%s', print_r($aOptions, true));
            throw new \UnexpectedValueException($sMsg);
        }

        // execute the curl
        $sResult = $this->execCurlRequest($hCurl);

        if (false === $sResult) {
            $sMsg = sprintf('Failed to execute curl request, request returned false; url=%s, headers=%s', $sUrl, print_r($aHeaders, true));
            throw new \UnexpectedValueException($sMsg);
        }

        $sContentType = $this->getCurlContentType($hCurl);

        // close the session
        $this->closeCurlHandle($hCurl);

        $mResult = $this->handleContentFromResponse($sContentType, $sResult);
        return $mResult;
    }

    /**
     * Handles the request data conditional on the given content type
     *
     * @param string $sContentType Representing the content type
     * @param string $sResult raw body of response returned
     *
     * @return string|array
     */
    protected function handleContentFromResponse($sContentType, $sResult)
    {
        if (strncmp($sContentType, 'application/json', 16) === 0) {
            $aData = json_decode($sResult, true);

            if ($aData === null) {
                throw new \UnexpectedValueException('Received request response that is content type json, but could not be decoded');
            }

            return $aData;
        }

        return $sResult;
    }

    /**
     * Protected method to abstract curl init for testing
     *
     * @param string $sUrl The URL for the request
     * @return CurlHandle
     */
    protected function initCurl($sUrl)
    {
        return curl_init($sUrl);
    }

    /**
     * Protected method to abstract curl_setopt_array for testing
     *
     * @param object $hCurl Curl handle returned from curl_init
     * @param array $aOptions Options to set to curl request
     * @return boolean True if successfully set the options to curl; otherwise false
     */
    protected function setCurlOpt($hCurl, $aOptions)
    {
        return curl_setopt_array($hCurl, $aOptions);
    }

    /**
     * Protected method to abstract curl_exec for testing
     *
     * @param object $hCurl Curl handle returned from curl_init
     * @return string Raw response from the curl request
     */
    protected function execCurlRequest($hCurl)
    {
        return curl_exec($hCurl);
    }

    /**
     * Protected method to abstract curl_getinfo for testing
     *
     * @param object $hCurl Curl handle returned from curl_init
     * @return string Content-Type string from the response
     */
    protected function getCurlContentType($hCurl)
    {
        return curl_getinfo($hCurl, CURLINFO_CONTENT_TYPE);
    }

    /**
     * Protected method to abstract curl_close for testing
     *
     * @param object $hCurl Curl handle returned from curl_init
     */
    protected function closeCurlHandle($hCurl)
    {
        curl_close($hCurl);
    }
}

<?php

if (!function_exists('curl_init'))
	throw new Exception('OkPhpSdk needs the CURL PHP extension.');
if (!function_exists('json_decode'))
	throw new Exception('OkPhpSdk needs the JSON PHP extension.');

class OkPhpSdk
{
    /**
     * @var string $clientSecret Application secret key
     */
    private $clientSecret;

    /**
     * @var string $applicationKey Public application key
     */
    private $applicationKey;

    /**
     * @var string $accessToken Access token, lifetime 30 minutes
     */
    private $accessToken;

    /**
     * @var array $curlOptions Default Curl options
     */
    public static $curlOptions = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => 'OkPhpSdk-0.1',
    );

    /**
     * @var array $domainMap Odnoklassniki domain map
     */
    public static $domainMap = array(
        'api' => 'http://api.odnoklassniki.ru/fb.do',
    );

    public function __construct(
        $clientId,
        $clientSecret,
        $appPublicKey
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->applicationKey = $appPublicKey;
    }

    /**
     * Get Access token
     *
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set Access token
     *
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Get client secret
     *
     * @return null|string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Set client secret
     *
     * @param string $accessToken
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Request odnoklassniki REST api method
     *
     * @param string $method odnoklassniki REST api method
     *
     * @param array $params parameters
     *
     * @throws \RuntimeException
     * @return mixed|array
     */
    public function api($method, $params = array())
    {
        $params['format'] = 'JSON';
        $params['application_key'] = $this->applicationKey;
        $params['method'] = $method;

        ksort($params);
        $paramsString = '';
        foreach ($params as $key => $value) {
            $paramsString .= $key . '=' . $value;
        }

        $signature = md5($paramsString . md5($this->accessToken . $this->clientSecret));

        $url = static::$domainMap['api'] . 
                '?access_token=' . $this->accessToken .
                '&' . http_build_query($params) .
                '&sig=' . $signature;
 
        $response = $this->curlRequest($url);
        $result = json_decode($response, true);
        if (isset($result['error_code']) && $result['error_code']) {
            throw new \RuntimeException($result['error_msg'], $result['error_code']);

        }

        return $result;
    }

    /**
     * Make CURL request
     *
     * @param string $url     Url to request
     * @param array  $options Curl options
     *
     * @throws \RuntimeException
     * @return string
     */
    protected function curlRequest($url, $options = array())
    {
        $curlHandler = curl_init($url);
        curl_setopt_array($curlHandler, $options + static::$curlOptions);
        $response = curl_exec($curlHandler);
        if ($response === false) {
            throw new \RuntimeException(curl_error($curlHandler), curl_errno($curlHandler));
        }
        curl_close($curlHandler);

        return $response;
    }
}

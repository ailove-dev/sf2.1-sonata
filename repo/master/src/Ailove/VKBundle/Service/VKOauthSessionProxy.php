<?php

namespace Ailove\VKBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Oauth2Proxy;

/**
 * VK oauth proxy with session support.
 */
class VKOauthSessionProxy extends Oauth2Proxy
{
    protected $clientId;
    protected $clientSecret;
    protected $dialogUrl;
    protected $redirectUri;
    protected $scope;
    protected $responseType;
    protected $accessTokenUrl;
    protected $accessParams;
    protected $authJson;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $serviceContainer;

    /**
     * @var \Symfony\Component\HttpFoundation\Session
     */
    protected $session;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    const PREFIX = '_vk_';

    /**
     * Constructor.
     *
     * @param string $clientId       Id of the client application
     * @param string $clientSecret   Application secret key
     * @param string $accessTokenUrl Access token url
     * @param string $dialogUrl      Dialog url
     * @param string $responseType   Response type (for example: code)
     * @param string $redirectUri    Redirect uri
     * @param string $scope          Access scope (for example: friends,video,offline)
     */
    public function __construct(
        $clientId,
        $clientSecret,
        $accessTokenUrl,
        $dialogUrl,
        $responseType,
        $redirectUri = null,
        $scope = null
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessTokenUrl = $accessTokenUrl;
        $this->dialogUrl = $dialogUrl;
        $this->responseType = $responseType;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;
    }

    /**
     * Container setter injection (because interface blocks constructor injection)
     *
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function setContainer(Container $container)
    {
        $this->serviceContainer = $container;
    }

    /**
     * Stores the given ($key, $value) pair, so that future calls to getPersistentData($key) return $value. This call may be in another request.
     *
     * @param string $key   Key for persisting value
     * @param array  $value Value to persist
     *
     * @return void
     */
    protected function setPersistentData($key, $value)
    {
        $this->getSession()->set($this->constructSessionVariableName($key), $value);
    }

    /**
     * Get the data for $key
     *
     * @param string  $key     The key of the data to retrieve
     * @param boolean $default The default value to return if $key is not found
     *
     * @return mixed
     */
    protected function getPersistentData($key, $default = false)
    {
        $sessionVariableName = $this->constructSessionVariableName($key);
        if ($this->getSession()->has($sessionVariableName)) {
            return $this->getSession()->get($sessionVariableName);
        }

        return $default;
    }

    /**
     * Construct session variable name.
     *
     * @param string $key Key name
     *
     * @return string
     */
    protected function constructSessionVariableName($key)
    {
        return self::PREFIX . implode('_', array($this->clientId, $key));
    }

    /**
     * Authorize VK client.
     *
     * @param string $redirectUri Redirect URI
     *
     * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function authorize($redirectUri = null)
    {
        $result = false;

        if (null === $this->redirectUri && null !== $redirectUri) {
            $this->redirectUri = $redirectUri;
        }

        if (($this->authJson = $this->getPersistentData('authJson'))) {
            // Data already stored in the session
            $result = true;
        } else {
            $code = $this->getRequest()->get('code');
            if (empty($code)) {
                // Redirect to VK auth
                $this->setPersistentData('state', md5(uniqid(rand(), true))); // CSRF protection
                $this->dialogUrl .= '?client_id=' . $this->clientId .
                        '&redirect_uri=' . urlencode($this->redirectUri).
                        '&scope=' . $this->scope.
                        '&response_type=' . $this->responseType.
                        '&state=' . $this->getPersistentData('state');


                return new RedirectResponse($this->dialogUrl);
            } else {
                $this->authJson = file_get_contents($this->accessTokenUrl .
                    '?client_id=' . $this->clientId .
                    '&client_secret=' . $this->clientSecret .
                    '&code=' . $code .
                    '&redirect_uri=' . urlencode($this->redirectUri));

                if ($this->authJson !== false) {
                    $this->setPersistentData('authJson', $this->authJson);
                    $result = true;
                } else {
                    $result = false;
                }

            }
        }

        return $result;
    }

    /**
     * Get access token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        if (null === $this->accessParams) {
            $this->accessParams = json_decode($this->getAuthJson(), true);
        }

        return $this->accessParams['access_token'];
    }

    /**
     * Get expires time.
     *
     * @return string
     */
    public function getExpiresIn()
    {
        if (null === $this->accessParams) {
            $this->accessParams = json_decode($this->getAuthJson(), true);
        }

        return $this->accessParams['expires_in'];
    }

    /**
     * Get user id.
     *
     * @return string
     */
    public function getUserId()
    {
        if (null === $this->accessParams) {
            $this->accessParams = json_decode($this->getAuthJson(), true);
        }

        return $this->accessParams['user_id'];
    }

    /**
     * Get authorization JSON string.
     *
     * @return string
     */
    protected function getAuthJson()
    {
        if (null === $this->authJson) {
            $this->authJson = $this->getPersistentData('authJson');
        }

        return $this->authJson;
    }

    /**
     * Return request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if (empty($this->request)) {
            $this->request = $this->serviceContainer->get('request');
        }

        return $this->request;
    }

    /**
     * Return session
     *
     * @return \Symfony\Component\HttpFoundation\Session
     */
    public function getSession()
    {
        if (empty($this->session)) {
            $this->session = $this->getRequest()->getSession();
        }

        return $this->session;
    }

    /**
     * Get client Id
     * @return type 
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get Client Secret
     * 
     * @return type 
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }
}

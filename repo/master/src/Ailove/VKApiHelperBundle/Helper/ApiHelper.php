<?php

namespace Ailove\VKApiHelperBundle\Helper;

use Ailove\VKBundle\Service\VKOauthSessionProxy;
use Ailove\VKApiHelperBundle\Helper\SigHelper;
use VkPhpSdk;

/**
 * Description of ApiHelper
 */
class ApiHelper
{
    const KEY_STORE_FRIENDS = 'key_store_friends';
    const KEY_STORE_FRIENDS_IDS = 'key_store_friends_ids';
    const KEY_STORE_PROFILES = 'key_store_profiles';

    protected $oauthProxy;
    protected $vkApi;
    protected $sigHelper;

    protected $clientId;
    protected $clientSecret;
    protected $vkUid;
    protected $accessToken;
    protected $session;

    /**
     * Constructor
     * 
     * @param \Ailove\VKBundle\Service\VKOauthSessionProxy $oauthProxy OAuth proxy
     * @param \VkPhpSdk                                    $vkApi      VK api
     * @param \Ailove\VKApiHelperBundle\Helper\SigHelper   $sigHelper  sig helper
     */
    public function __construct(VKOauthSessionProxy $oauthProxy, VkPhpSdk $vkApi, SigHelper $sigHelper)
    {
        $this->oauthProxy = $oauthProxy;
        $this->vkApi = $vkApi;
        $this->sigHelper = $sigHelper;

        if ($oauthProxy->authorize()) {
            $this->vkUid = $oauthProxy->getUserId();
            $this->accessToken = $oauthProxy->getAccessToken();
            $this->clientId = $oauthProxy->getClientId();
            $this->clientSecret = $oauthProxy->getClientSecret();

            $this->vkApi->setAccessToken($this->accessToken);
            $this->vkApi->setUserId($this->vkUid);
        }
    }

    /**
     * Get profiles
     * 
     * @param array $fields Array of fields
     * @param array $uids   Array of uids
     * 
     * @return type
     */
    public function getProfiles(array $fields = null, array $uids = null)
    {
        $key = static::KEY_STORE_PROFILES;
        $profiles = array();
        // Check in store
        if (($profiles = $this->getPersistentData($key))) {
            return $profiles;
        }

        $profiles = $this->getProfilesFromApi($fields, $uids);
        $this->setPersistentData($key, $profiles);

        return $profiles;
    }

    /**
     * Get friends
     * 
     * @param array $fields
     * 
     * @return type
     */
    public function getFriends(array $fields = null)
    {
        $key = null === $fields ? static::KEY_STORE_FRIENDS_IDS : static::KEY_STORE_FRIENDS;
        $friends = array();
        // Check in store
        if (($friends = $this->getPersistentData($key))) {
            return $friends;
        }

        $friends = $this->getFriendsFromApi($fields);
        $this->setPersistentData($key, $friends);

        return $friends;
    }

    /**
     * Get countries
     * 
     * @param array $countriesIds
     * 
     * @return type
     */
    public function getCountries(array $countriesIds)
    {
        $response = $this->vkApi->api('getCountries', array(
                'cids' => implode(',', $countriesIds)
        ));

        return isset($response['response']) ? $response['response'] : $response;
    }

    /**
     * Get cities
     * 
     * @param array $citiesIds
     * 
     * @return type
     */
    public function getCities(array $citiesIds)
    {
        $response = $this->vkApi->api('getCities', array(
                'cids' => implode(',', $citiesIds)
        ));

        return isset($response['response']) ? $response['response'] : $response;
    }

    /**
     * Get friends from api
     * 
     * @param array $fields
     * 
     * @return type
     */
    public function getFriendsFromApi(array $fields = null)
    {
        $params['uid'] = $this->vkUid;

        if (null !== $fields && is_array($fields)) {
            $params['fields'] = implode(',', $fields);
        }

        $response = $this->vkApi->api('friends.get', $params);

        return isset($response['response']) ? $response['response'] : $response;
    }

    /**
     * Get profiles from api
     * 
     * @param array $fields Array of fields
     * @param array $uids   Array of uids
     * 
     * @return type
     */
    public function getProfilesFromApi(array $fields = null, array $uids = null)
    {
        $params['uids'] = $this->vkUid;

        if (null !== $uids && is_array($uids)) {
            $params['uids'] .= ',' . implode(',', $uids);
        }

        if (null !== $fields && is_array($fields)) {
            $params['fields'] = implode(',', $fields);
        }

        $response = $this->vkApi->api('users.get', $params);

        return isset($response['response']) ? $response['response'] : $response;
    }

    /**
     * Get VK user uid
     * 
     * @return type
     */
    public function getVkUid()
    {
        return $this->vkUid;
    }

    protected function getSession()
    {
        if (empty($this->session)) {
            $this->session = $this->oauthProxy->getSession();
        }

        return $this->session;
    }

    protected function getPersistentData($key, $default = false)
    {
        if ($this->getSession() && $this->getSession()->has($key)) {
            return $this->getSession()->get($key);
        }

        return $default;
    }

    protected function setPersistentData($key, $value)
    {
        if ($this->getSession()) {
            $this->getSession()->set($key, $value);

            return true;
        }

        return false;
    }
}
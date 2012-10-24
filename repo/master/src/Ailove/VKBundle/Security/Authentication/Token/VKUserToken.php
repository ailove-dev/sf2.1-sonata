<?php

namespace Ailove\VKBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * VK user token.
 */
class VKUserToken extends AbstractToken
{
    /**
     * Constructor.
     *
     * @param string $uid   User ID or name
     * @param array  $roles User roles
     */
    public function __construct($uid = '', array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($uid);

        if (!empty($uid)) {
            $this->setAuthenticated(true);
        }
    }

    /**
     * Return user's credentials.
     *
     * @return string
     */
    public function getCredentials()
    {
        return '';
    }
}

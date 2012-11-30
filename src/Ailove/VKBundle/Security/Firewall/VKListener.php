<?php

namespace Ailove\VKBundle\Security\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;
use Ailove\VKBundle\Security\Authentication\Token\VKUserToken;

/**
 * VK firewall listener
 */
class VKListener extends AbstractAuthenticationListener
{
    private $checkUser = true;

    /**
     * Attempt to authenticate user.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Request instance
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface|void
     */
    protected function attemptAuthentication(Request $request)
    {
        return $this->authenticationManager->authenticate(new VKUserToken(), $this->checkUser);
    }

    /**
     * Set check user
     * 
     * @param type $status
     */
    public function setCheckUser($status)
    {
        $this->checkUser = $status;
    }
}

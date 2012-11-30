<?php

/**
 * This file is part of the "AiloveOliport" package.
 *
 * Copyright Ailove company <info@ailove.ru>
 *
 */

namespace Ailove\OKBundle\Security\Firewall;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

use Ailove\OKBundle\Security\Authentication\Token\OKUserToken;

/**
 * Odnoklassniki firewall listener
 *
 * @author Marsel Kagarmanov <m.kagarmanov@ailove.ru>
 */
class OKListener extends AbstractAuthenticationListener
{
    /**
     * Attempt to authenticate user.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Request instance
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface|void
     */
    protected function attemptAuthentication(Request $request)
    {
        return $this->authenticationManager->authenticate(new OKUserToken());
    }
}

<?php

namespace Application\Sonata\UserBundle\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Authentication handler listener.
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * Constructor
     *
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router Router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * onAuthenticationSuccess
     *
     * @param \Symfony\Component\HttpFoundation\Request                            $request Request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token   Auth token
     *
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($request->isXmlHttpRequest()) {
            // Handle XHR here
            $result = array(
                'status' => 'ok',
                'message' => 'Вход выполнен успешно',
                'redirect' => $this->router->generate('HelloBundle_homepage')
            );

            return new Response(json_encode($result));
        } else {
            return new RedirectResponse($this->router->generate('HelloBundle_homepage', array()));
        }
    }

    /**
     * onAuthenticationFailure
     *
     * @param \Symfony\Component\HttpFoundation\Request                          $request   Request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception Auth exception
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            // Handle XHR here
            $result = array(
                'status' => 'error',
                'message' => "Неправильно введен email или пароль" // $exception->getMessage(),
            );

            return new Response(json_encode($result));
        } else {
            // Create a flash message with the authentication error message
            $request->getSession()->setFlash('error', $exception->getMessage());
            $url = $this->router->generate('StoryBundle_homepage');

            return new RedirectResponse($url);
        }
    }
}

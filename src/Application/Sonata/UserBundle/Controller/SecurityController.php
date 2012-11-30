<?php

namespace Application\Sonata\UserBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use FOS\UserBundle\Controller\SecurityController as FOSUserSecurityController;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Security controller.
 */
class SecurityController extends FOSUserSecurityController
{
    private $loginTemplate;

    /**
     * Login action.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loginAction($popup = false)
    {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted(User::ROLE_REGISTERED)) {
            return new RedirectResponse($this->container->get('router')->generate('HelloBundle_homepage'));
        }

        $templateEngine = $this->container->getParameter('fos_user.template.engine');
        if (true === $popup) {
            $this->loginTemplate = sprintf('ApplicationSonataUserBundle:Security:loginPopup.html.%s', $templateEngine);
        } else {
            $this->loginTemplate = sprintf('ApplicationSonataUserBundle:Security:login.html.%s', $templateEngine);
        }

        return parent::loginAction();
    }

    /**
     * Security check action.
     *
     */
    public function checkAction()
    {
        parent::checkAction();
    }

    /**
     * Logout action.
     *
     */
    public function logoutAction()
    {
        parent::logoutAction();
    }

    
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->container->get('templating')->renderResponse($this->loginTemplate, $data);
    }
}

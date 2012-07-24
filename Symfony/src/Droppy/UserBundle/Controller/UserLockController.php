<?php

namespace Droppy\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserLockController extends ContainerAware
{
    public function lockUserAction()
    {
        if($this->container->get('request')->getMethod() === 'POST')
        {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $this->container->get('droppy_user.user_manager')->lockUser($user);
            $this->container->get('request')->getSession()->invalidate();
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_logout'));
        }
        else
        {
            return $this->container->get('templating')->renderResponse('DroppyUserBundle:Layout:settings_deactivation.html.twig');
        }
    }
}

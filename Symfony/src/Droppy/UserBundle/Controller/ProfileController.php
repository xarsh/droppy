<?php

namespace Droppy\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends ContainerAware
{
    public function changeProfileAction()
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$form = $this->container->get('droppy_user.user.form');
    	$form->setData($user);
    	$formHandler = $this->container->get('droppy_user.user.form.handler');
    	if($formHandler->process($user))
    	{
    		return new RedirectResponse($this->container->get('router')->generate('droppy_main_index'));
    	}
    	return $this->container->get('templating')->renderResponse('DroppyUserBundle:Layout:settings_profile.html.twig', array(
    		'form' => $form->createView()
    	));
    }
}

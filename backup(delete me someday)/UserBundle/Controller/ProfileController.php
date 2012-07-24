<?php

namespace Droppy\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends ContainerAware
{
    public function changeProfileAction()
    {
    	$form = $this->container->get('droppy_user.personal_datas.form');
    	$datas = $this->container->get('security.context')->getToken()->getUser()->getPersonalDatas();
    	$form->setData($datas);
    	$formHandler = $this->container->get('droppy_user.personal_datas.form.handler');
    	if($formHandler->process())
    	{
    		return new RedirectResponse($this->container->get('router')->generate('droppy_main_top'));
    	}
    	return $this->container->get('templating')->renderResponse('DroppyUserBundle:Layout:profile_edit.html.twig', array(
    		'form' => $form->createView()
    	));
    }
}
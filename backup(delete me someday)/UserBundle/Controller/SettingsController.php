<?php

namespace Droppy\UserBundle\Controller;

use Droppy\UserBundle\Form\Handler\SettingsFormHandler;

use Droppy\UserBundle\Form\Type\SettingsFormType;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SettingsController extends ContainerAware
{
    public function changeSettingsAction()
    {
        $settings = $this->container->get('security.context')->getToken()->getUser()->getSettings();
    	$form = $this->container->get('form.factory')->create(new SettingsFormType($this->container->get('translator'), true));
    	$form->setData($settings);
    	$formHandler = new SettingsFormHandler($form, $this->container->get('request'), 
    	        $this->container->get('doctrine.orm.entity_manager'));
    	if($formHandler->process())
    	{
    		return new RedirectResponse($this->container->get('router')->generate('droppy_main_top'));
    	}
    	return $this->container->get('templating')->renderResponse('DroppyUserBundle:Layout:settings_edit.html.twig', array(
    		'form' => $form->createView()
    	));
    }
}
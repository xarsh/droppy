<?php

namespace Droppy\EventBundle\Controller;

use Droppy\MainBundle\Entity\PrivacySettings;

use Droppy\UserBundle\Entity\Settings;

use Droppy\MainBundle\Entity\Timezone;

use Droppy\EventBundle\Entity\EventDateTime;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Droppy\EventBundle\Entity\Event;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

class EventCreationController extends ContainerAware
{
    public function createEventAction()
    {
    	$form = $this->container->get('droppy_event.creation.form');
    	$formHandler = $this->container->get('droppy_event.creation.form.handler');
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$defaultDatas = $this->container->get('droppy_event.event_creation_helper')->getDefaultEvent();
    	$form->setData($defaultDatas);
    	if($formHandler->process($user))
    	{
    		return new RedirectResponse($this->container->get('router')->generate('droppy_main_top'));
    	}
    	return $this->container->get('templating')->renderResponse('DroppyEventBundle:Layout:event_creation.html.twig', array(
    		'form' => $form->createView()
    	));
    }
}

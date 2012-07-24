<?php

namespace Droppy\EventBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Droppy\EventBundle\Entity\Event;

class EventDisplayController extends ContainerAware
{
    public function showEventAction(Event $event)
    {
    	return new Response('ok');
    }
    
    public function showDetailsAction($id)
	{
		// if ($id != [number])
		// 	redirect;
		return $this->container->get('templating')->renderResponse('DroppyEventBundle:Layout:event_details.html.twig',array(
			'event' => array()
		));	
    }
}

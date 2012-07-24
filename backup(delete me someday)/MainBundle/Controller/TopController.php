<?php

namespace Droppy\MainBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Droppy\EventBundle\Util\EventSelector;
use Droppy\EventBundle\Util\DateManipulator;

class TopController extends ContainerAware
{
    public function topAction()
    {
    	$user = $this->container->get('security.context')->getToken()->getUser(); 
		$eventSelector = $this->container->get('droppy_event.event_selector');
		$timelineEvents = $eventSelector->getDroppedEventsByPeriod($user);
		$timelineEventPrototype = $this->container->get('templating')->
		    render('DroppyEventBundle:Includes:list_event_prototype.html.twig');

		return $this->container->get('templating')->renderResponse('DroppyMainBundle:Layout:top.html.twig', array(
			'time_line' => $timelineEvents,
		    'time_line_event_prototype' => $timelineEventPrototype
		));
    }
}

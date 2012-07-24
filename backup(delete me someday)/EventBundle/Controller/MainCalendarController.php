<?php

namespace Droppy\EventBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Droppy\EventBundle\Entity\Event;
use Droppy\EventBundle\Form\Type\EventCreationFormType;

class MainCalendarController extends ContainerAware
{
	public function getMainCalendarAction($yearmonth)
	{
		$dateTime = new \DateTime($yearmonth == "now" ? "now" : $yearmonth."01");
		$form = $this->container->get('form.factory')->create(new EventCreationFormType(false));

		return $this->container->get('templating')->renderResponse('DroppyEventBundle:Includes:main_calendar.html.twig',array(
			'dateTime' => $dateTime,
			'form' => $form->createView()
		));
	}
}

<?php

namespace Droppy\WebApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Droppy\WebApplicationBundle\Entity\Event;
use Droppy\WebApplicationBundle\Form\Type\MakeEventFormType;
use Droppy\WebApplicationBundle\Form\Handler\MakeEventFormHandler;

class MakeEventController extends Controller
{
	public function makeEventAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$form = $this->get('droppy_webapp.make_event.form');
		$formHandler = $this->get('droppy_webapp.make_event.form.handler');
		if($formHandler->process($user))
		{
			return $this->redirect($this->generateUrl('droppy_webapp_index'));
		}
		return $this->render('DroppyWebApplicationBundle:MakeEvent:make_event.html.twig', array(
				'form' => $form->createView()));
	}
}

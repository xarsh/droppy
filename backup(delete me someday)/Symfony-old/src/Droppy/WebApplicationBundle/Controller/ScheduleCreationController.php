<?php

namespace Droppy\WebApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Droppy\WebApplicationBundle\Entity\Schedule;
use Droppy\WebApplicationBundle\Form\Type\ScheduleCreationFormType;
use Droppy\WebApplicationBundle\Form\Handler\ScheduleCreationFormHandler;

class ScheduleCreationController extends Controller
{
	public function createScheduleAction()
	{
		$form = $this->get('droppy_webapp.schedule_creation.form');
		$formHandler = $this->get('droppy_webapp.schedule_creation.form.handler');
		$user = $this->get('security.context')->getToken()->getUser();
		if($formHandler->process($user))
		{
			return $this->redirect($this->generateUrl('droppy_webapp_index'));
		}
		return $this->render('DroppyWebApplicationBundle:ScheduleCreation:make_schedule.html.twig', array(
				'form' => $form->createView()));
	}
}

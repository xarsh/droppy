<?php

namespace Droppy\WebApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Droppy\WebApplicationBundle\Entity\Schedule;

//TODO error gestion

class ScheduleDropController extends Controller
{
	public function getSchedule($id)
	{
		return $this->getDoctrine()->getRepository('DroppyWebApplicationBundle:Schedule')
			                    ->getScheduleById($id);
	}
	
 	public function dropScheduleAction() 
	{
		$schedule = $this->getSchedule($this->get('request')->request->get('schedule_id'));

		if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
		   return new Response('Something wrong!', 302);
		}
		
		try{
			$user = $this->get('security.context')->getToken()->getUser();
			$this->get('droppy_user_manager')->dropSchedule($user, $schedule);
			$this->get('droppy_user_manager')->updateUser($user);
		}catch(Exception $e){
			return new Response('Internal server error!', 500);
		}

        	return new Response('Hello, Droppy!', 200);
	}

	public function undropScheduleAction()
	{
		$schedule = $this->getSchedule($this->get('request')->request->get('schedule_id'));

		if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
		   return new Response('Something wrong!', 302);
		}

		try{
			$user = $this->get('security.context')->getToken()->getUser();
			$this->get('droppy_user_manager')->undropSchedule($user, $schedule);
			$this->get('droppy_user_manager')->updateUser($user);
		}catch(Exception $e){
			return new Response('Internal server error!', 500);
		}
        	return new Response('Hello, Droppy!', 200);
	}	
}  

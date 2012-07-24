<?php

namespace Droppy\WebApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Droppy\WebApplicationBundle\Entity\Event;

//TODO error gestion

class EventDropController extends Controller
{
	public function getEvent($id)
	{
		return $this->getDoctrine()->getRepository('DroppyWebApplicationBundle:Event')
			                    ->getEventById($id);
	}
	
 	public function dropEventAction() 
	{
		$event = $this->getEvent($this->get('request')->request->get('event_id'));

		if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
		   return new Response('Something wrong!', 302);
		}
		
		try{
			$user = $this->get('security.context')->getToken()->getUser();
			$this->get('droppy_user_manager')->dropEvent($user, $event);
			$this->get('droppy_user_manager')->updateUser($user);
		}catch(Exception $e){
			return new Response('Internal server error!', 500);
		}

        	return new Response('Hello, Droppy!', 200);
	}

	public function undropEventAction()
	{
		$event = $this->getEvent($this->get('request')->request->get('event_id'));

		if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
		   return new Response('Something wrong!', 302);
		}

		try{
			$user = $this->get('security.context')->getToken()->getUser();
			$this->get('droppy_user_manager')->undropEvent($user, $event);
			$this->get('droppy_user_manager')->updateUser($user);
		}catch(Exception $e){
			return new Response('Internal server error!', 500);
		}
        	return new Response('Hello, Droppy!', 200);
	}	
}  

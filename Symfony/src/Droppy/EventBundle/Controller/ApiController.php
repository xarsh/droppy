<?php

namespace Droppy\EventBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Droppy\UserBundle\Entity\UserDropsEventRelation;
use Droppy\EventBundle\Entity\Event;
use Droppy\EventBundle\Exception\XMLHttpRequestException;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    protected $container;
    protected $tools;
    
    public function __construct($container)
    {
        $this->container = $container;
        $this->tools = $this->container->get('droppy_main.controller_tools');
    }
    
    public function testAction()
    {
        $user = $this->tools->getUser();
        return new Response('Logged in as ' . $user->getUsername() . '.');
    }
    
    public function getDroppedEventsAction()
    {
        $currentUser = $this->tools->getUser();
        list($user, $date, $offset, $maxResults) = array_values($this->tools->getParameters());
        $results = $this->tools->getEventRepository()->getDroppedEvents($user, $currentUser, $date, $offset, $maxResults);
        $encodedDatas = $this->tools->normalizeData($results, array(
        	'droppy_user.normalizer.user_drops_event_relation',
        	'droppy_event.normalizer.event_minimal'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }

    public function validateDate($param) {
        try{
            $param = trim($param);
            if(empty($param)) throw new \Exception();
            $param = new \DateTime($param);
        }catch(\Exception $e){
            $param = null;
        }

        return $param; 
    }
    
    public function searchEventsAction()
    {
        $user = $this->tools->getUser();
        list($keywords, $places, $start, $end, $offset, $maxResults) = array_values($this->tools->getParameters());
        $start = $this->validateDate($start);
        $end = $this->validateDate($end);

        $results = $this->tools->getEventRepository()
            ->searchEvents($user, str_replace('　', ' ', $keywords), str_replace('　', ' ', $places), $start, $end, $offset, $maxResults);
        $encodedDatas = $this->tools->normalizeData($results, array(
        	'droppy_event.normalizer.event_minimal',
        	'droppy_user.normalizer.user_drops_event_relation'
        ));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));        
    }

    public function eventsInRangeAction()
    {
        $currentUser = $this->tools->getUser();
        list($user, $start, $end) = array_values($this->tools->getParameters());
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $results = $this->tools->getEventRepository()->eventsInRange($user, $currentUser, $start, $end);
        $encodedDatas = $this->tools->normalizeData($results, array(
        	'droppy_event.normalizer.event_minimal',
        	'droppy_user.normalizer.user_drops_event_relation'
        ));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));        
    }
    
    public function updateEventAction()
    {
        $user = $this->tools->getUser();
        $eventArray = $this->container->get('request')->getContent();
        $event = $this->tools->denormalizeDatas($eventArray, 'DroppyEventBundle:Event', array('droppy_event.normalizer.event'));
        $eventId = $event->getId();
        if(isset($eventId) && !($user->equals($event->getCreator()) || $this->tools->isAdmin()))
        {
            throw new HttpException(403);
        }
        $errors = $this->container->get('validator')->validate($event);
        $this->tools->checkErrors($errors);
        if(isset($eventId) === false)
        {
            $this->container->get('droppy_user.user_manager')->addCreatedEvent($user, $event);
        }
        $this->container->get('droppy_user.user_manager')->updateUser($user);
        $event = $this->container->get('droppy_event.event_manager')->getEventAndRelationFromObject($event, $user);
        $encodedDatas = $this->tools->normalizeData($event, array(
                        'droppy_user.normalizer.user_drops_event_relation',
                        'droppy_event.normalizer.event'));
        
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function getEventAction()
    {
    	$eventId = $this->tools->getParameter('event_id');
    	$event = $this->container->get('droppy_event.event_manager')->getEventById($eventId);
    	$encodedDatas = $this->tools->normalizeData($event, array('droppy_event.normalizer.event'));
    	return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function getEventAndRelationAction()
    {
        $user = $this->tools->getUser();
        $eventId = $this->tools->getParameter('event_id');
        $event = $this->container->get('droppy_event.event_manager')->getEventAndRelation($eventId, $user);
        $encodedDatas = $this->tools->normalizeData($event, array(
                'droppy_user.normalizer.user_drops_event_relation',
                'droppy_event.normalizer.event'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function getTimelineAction()
    {
        $currentUser = $this->tools->getUser();
        list($user, $type, $offset, $maxResults, $date) = array_values($this->tools->getParameters());
        if($type === 'new' || $type === 'by_date')
        {
            $results = $this->tools->getEventRepository()->getDroppedEvents($user, $currentUser, $offset, $maxResults, $type, $date);
        }
        else
        {
            $results = array();
        }
        $encodedDatas = $this->tools->normalizeData($results, array(
        	'droppy_user.normalizer.user_drops_event_relation',
        	'droppy_event.normalizer.event_minimal'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function removeEventAction()
    {
    	$user = $this->tools->getUser();
    	$eventId = $this->tools->getParameter('event_id');
    	$event = $this->tools->getEventRepository()->find($eventId);
    	if($event === null)
    	{
    		throw new HttpException(404, 'No such event.');
    	}
    	if($user->equals($event->getCreator()) === false)
    	{
    		throw new HttpException(404);
    	}
    	$this->container->get('droppy_user.user_manager')->removeCreatedEvent($user, $event);
    	$this->container->get('droppy_event.event_manager')->removeEvent($event);
    	
    	return new Response('Removed.', 200);
    }
    
    public function setInCalendarAction()
    {
        $user = $this->tools->getUser();
        $event = $this->container->get('droppy_event.event_manager')->getEventById($this->tools->getParameter('event_id'));
        $this->container->get('droppy_event.event_manager')->setEventInCalendar($user, $event, true);
        $this->container->get('doctrine')->getEntityManager()->flush();
        return new Response('Success.', 200);
    }
    
    public function setOutOfCalendarAction()
    {
        $user = $this->tools->getUser();
        $event = $this->container->get('droppy_event.event_manager')->getEventById($this->tools->getParameter('event_id'));
        $this->container->get('droppy_event.event_manager')->setEventInCalendar($user, $event, false);
        $this->container->get('doctrine')->getEntityManager()->flush();
        return new Response('Success.', 200);
    }
    
    public function getCreatedEventsAction()
    {
        $currentUser = $this->tools->getUser();
        list($user, $offset, $maxResults) = array_values($this->tools->getParameters());
        $results = $this->tools->getEventRepository()->getCreatedEvents($user, $currentUser, $offset, $maxResults);
        $encodedDatas = $this->tools->normalizeData($results, array(
                'droppy_user.normalizer.user_drops_event_relation',
                'droppy_event.normalizer.event_minimal'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function dropEventAction()
    {
    	$currentUser = $this->tools->getUser();
    	$event = $this->container->get('droppy_event.event_manager')->getEventById($this->tools->getParameter('event_id'));
    	$this->container->get('droppy_user.user_manager')->dropEvent($currentUser, $event);
    	$this->container->get('doctrine')->getEntityManager()->flush();
    	return new Response('Success.', 200, array('Content-Type' => 'text/plain'));
    }
    
    public function undropEventAction()
    {
    	$currentUser = $this->tools->getUser();
    	$event = $this->container->get('droppy_event.event_manager')->getEventById($this->tools->getParameter('event_id'));
    	$this->container->get('droppy_user.user_manager')->undropEvent($currentUser, $event);
    	$this->container->get('doctrine')->getEntityManager()->flush();
    	return new Response('Success.', 200, array('Content-Type' => 'text/plain'));
    }
    
    public function latestEventsAction()
    {
        $user = $this->tools->getUser();
        list($date, $offset, $maxResults) = array_values($this->tools->getParameters());
        $results = $this->tools->getEventRepository()->getLatestEvents($user, $date, $offset, $maxResults);
        $encodedDatas = $this->tools->normalizeData($results, array(
                'droppy_user.normalizer.user_drops_event_relation',
                'droppy_event.normalizer.event_minimal'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
}

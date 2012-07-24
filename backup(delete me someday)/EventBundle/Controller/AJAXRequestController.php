<?php

namespace Droppy\EventBundle\Controller;

use JMS\AopBundle\Exception\Exception;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Droppy\UserBundle\Entity\UserDropsEventRelation;
use Droppy\EventBundle\Entity\Event;
use Droppy\EventBundle\Exception\XMLHttpRequestException;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

class AJAXRequestController extends ContainerAware
{
	public function setEventInAction(Event $event)
	{
		$user = $this->checkInfosAndGetUser($event);
		$eventRelation = $this->container->get('droppy_main.data_checker')->getDroppedEventRelationOrDie($event);
		$eventRelation->setInCalendar(true);
		$this->container->get('droppy_user.user_manager')->updateUser($user);
		return new Response('success', 200);
	}
	
	public function setEventOutAction(Event $event)
	{
		$user = $this->checkInfosAndGetUser();
		$eventRelation = $this->container->get('droppy_main.data_checker')->getDroppedEventRelationOrDie($event);
		$eventRelation->setInCalendar(false);
		$this->container->get('droppy_user.user_manager')->updateUser($user);
		return new Response('success', 200);
	}
	
	public function likeEventAction(Event $event)
	{
	    $user = $this->checkInfosAndGetUser();
	    $this->container->get('droppy_main.data_checker')->checkLikedEvent($event, true);
	    $this->container->get('droppy_user.user_manager')->addLikedEvent($user, $event);
	    $this->container->get('droppy_user.user_manager')->updateUser($user);
	    return new Response('success', 200);
	}
	
	public function unlikeEventAction(Event $event)
	{
	    $user = $this->checkInfosAndGetUser();
	    $this->container->get('droppy_main.data_checker')->checkLikedEvent($event, false);
	    $this->container->get('droppy_user.user_manager')->removeLikedEvent($user, $event);
	    $this->container->get('droppy_user.user_manager')->updateUser($user);
	    return new Response('success', 200);
	}
	
	protected function checkInfosAndGetUser()
	{
	    $dataChecker = $this->container->get('droppy_main.data_checker');
		$dataChecker->checkXmlHttpRequest($this->container->get('request'));
		$user = $dataChecker->getUserOrDie();
		return $user;
	}
	
	public function getEventInfosAction(Event $event)
	{
	    $dataChecker = $this->container->get('droppy_main.data_checker');
	    $dataChecker->checkXmlHttpRequest($this->container->get('request'));
	    $serializer = new Serializer(array($this->container->get('droppy_event.normalizer.event')),
	            array('json' => new JsonEncoder()));
	    $json = $serializer->serialize($event, 'json');
	    return new Response($json, 200);
	}
	
	public function getEventsInIntervalAction()
	{
	    $user = $this->checkInfosAndGetUser();
	    $intervalJSON = $this->checkRequestAndGetDatas('interval');
	    $events = $this->getEventsInRangeResults($user, $intervalJSON);
	    $serializer = new Serializer(array($this->container->get('droppy_event.normalizer.event')),
	            array('json' => new JsonEncoder()));
	    $json = $serializer->serialize($events, 'json');
	    return new Response($json, 200);
	    
	}
	
	protected function checkRequestAndGetDatas($name)
	{
	    $request = $this->container->get('request');
	    $datas = $request->request->get($name);
	    if($request->getMethod() !== 'POST' ||
	            isset($datas) === false || empty($datas) === true)
	    {
	        throw new XMLHttpRequestException('Could not get datas from request');
	    }
	    return $datas;
	}
	
	protected function getEventsInRangeResults($user, $jsonDatas)
	{
	    $interval = json_decode($jsonDatas, true);
	    if(isset($interval['start']) === false || isset($interval['end']) === false)
	    {
	        throw new XMLHttpRequestException('Could not get interval. ' . var_dump($interval));
	    }
	    $start = $this->getDateTimeFromArray($interval['start']);
	    $end = $this->getDateTimeFromArray($interval['end']);
	    $eventRepository = $this->container->get('doctrine')->getRepository('DroppyEventBundle:Event');
	    return $eventRepository->eventsInRange($user, $start, $end);
	}
	
	protected function getDateTimeFromArray($array)
	{
	    $year = $array['year'];
	    $month = $array['month'];
	    $day = $array['day'];
	    return new \DateTime($year . '-' . $month . '-' . $day);
	}
	
	public function getTimelineEventsAction()
	{
        $user = $this->checkInfosAndGetUser();
        $datas = $this->checkRequestAndGetDatas('request_settings');
        $eventsRelations = $this->getTimelineDatas($user, $datas);
        $serializer = new Serializer(array(
                    $this->container->get('droppy_user.normalizer.user_drops_event_relation')
                ),
                array('json' => new JsonEncoder()));
        $json = $serializer->serialize($eventsRelations, 'json');
        return new Response($json, 200, array('Content-Type' => 'application/json'));
	}
	
	protected function getTimelineDatas($user, $jsonDatas)
	{
	    $datas = json_decode($jsonDatas, true);
	    if(isset($datas['type']) === false)
	    {
	        throw new XMLHttpRequestException('Could not get type of datas requested. ' . var_dump($datas));
	    }
	    
	    if($datas['type'] === 'by_date')
	    {
	        $method = 'getDroppedEventsByPeriod';
	    }
	    else if($datas['type'] === 'by_arrival')
	    {
	        $method = 'getDroppedEventsByArrival';
	    }
	    else if($datas['type'] === 'by_recommendation')
	    {
	        $method = 'getRecommendedEvents';
	    }
	    else
	    {
	        throw new XMLHttpRequestException('Requested data not recognized.');
	    }
	    return $this->queryTimelineDatas($user, $method, $datas);
	}
	
	protected function queryTimelineDatas($user, $method, $datas)
	{
	    $eventSelector = $this->container->get('droppy_event.event_selector');
	    if(isset($datas['date_time']) === false)
	    {
	        $datas['date_time'] = null;
	    }
	    if(isset($datas['offset']) === false)
	    {
	        $datas['offset'] = 0;
	    }
	    if(isset($datas['max_results']) === false)
	    {
	        $datas['max_results'] = 20;
	    }
	    $results = $eventSelector->$method($user, $datas['date_time'], $datas['offset'], $datas['max_results']);
	    return $results;
	}
	
	public function createSimpleEventAction()
	{
        $user = $this->checkInfosAndGetUser();
        $datas = $this->checkRequestAndGetDatas('event');	    
	    
	}
	
	protected function getEvent($jsonDatas) 
	{
	    $event = $this->container('droppy_event.event_creation_helper')->getDefaultEvent();
	    $serializer = new Serializer(array(new GetSetMethodNormalizer()),
	                    array('json' => new JsonEncoder()));
	    $json = $serializer->deserialize($jsonDatas, 'json');
	      
	}
}
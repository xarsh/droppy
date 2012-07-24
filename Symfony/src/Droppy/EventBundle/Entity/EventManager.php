<?php

namespace Droppy\EventBundle\Entity;

use Droppy\UserBundle\Entity\User;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Doctrine\ORM\EntityManager;

class EventManager
{
    protected $em;
    protected $repository;
    
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
    }
    
    public function setEventInCalendar(User $user, Event $event, $inCalendar)
    {
        $relation = $this->getRelation($user, $event);
        if($relation->isInCalendar() === $inCalendar)
        {
            throw new HttpException(400, $inCalendar ? 'Event already in calendar.' : 'Event not in calendar.');
        }
        $relation->setInCalendar($inCalendar);
    }
    
    public function getRelation(User $user, Event $event)
    {
        $relation = $this->repository->getEventRelation($user, $event);
        if($relation === null)
        {
            throw new HttpException(404, 'Not dropping this event.');
        }
        return $relation;
    }
    
    public function getEventAndRelation($eventId, User $user)
    {
        $event = $this->getEventById($eventId);
        return $this->repository->getEventAndRelation($event, $user);
    }
    
    public function getEventAndRelationFromObject(Event $event, User $user)
    {
        return $this->repository->getEventAndRelation($event, $user);
    }
    
    public function getEventById($id)
    {
        $event = $this->repository->find($id);
        if($event === null)
        {
            throw new HttpException(404, 'No such event.');
        }
        return $event;
    }
    
    public function eventsToEventsAndRelations(User $user, $events)
    {
        $relations = $this->repository->getEventsRelations($user, $events);
        $result = array();
        foreach($events as $event)
        {
            $result[] = array('event' => $event, 'relation' => $this->getRelationOrNullFromArray($event, $relations));
        }
        return $result;
    }
    
    protected function getRelationOrNullFromArray(Event $event, $relations)
    {
        foreach($relations as $relation)
        {
            if($event->equals($relation->getEvent()))
            {
                return $relation;
            }
        }
        return null;
    }
    
    public function removeEvent(Event $event)
    {
        $this->em->remove($event);
        $this->em->flush();
    }
    
}
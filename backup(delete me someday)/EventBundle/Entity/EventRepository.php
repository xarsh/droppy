<?php

namespace Droppy\EventBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Droppy\UserBundle\Entity\User;

/**
 * EventRepository
 */
class EventRepository extends EntityRepository
{
	public function getCreatedEvents(User $user, $offset = 0, $maxResults=20)
	{
        $query = $this->getEntityManager()->createQuery('
                SELECT e 
                FROM DroppyEventBundle:Event e 
                WHERE e.creator = :creator ORDER BY e.creationDate DESC 
                ')->setParameter('creator', $user);
        $query->setFirstResult($offset)->setMaxResults($maxResults);
		
        return $query->getResult();
    }

    public function latestEvents(\DateTime $date = null, $offset=0, $maxResults=20)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT e, d 
                FROM DroppyEventBundle:Event e 
                JOIN e.startDateTime d 
                WHERE d.date >= :today ORDER BY d.date
                ')->setParameter('today', $date);
        $query->setFirstResult($offset)->setMaxResults($maxResults);
		
        return new ArrayCollection($query->getResult());
    }
    
    public function getDroppedEvents(User $user, \DateTime $date=null, $offset=0, $maxResults=20)
    {
    	$query = $this->getEntityManager()->createQuery('
    			SELECT u, r, e, sd, ed
    			FROM DroppyUserBundle:User u
    			JOIN u.droppedEvents r
    			JOIN r.event e
    			JOIN e.startDateTime sd
    	        LEFT JOIN e.endDateTime ed
    			WHERE u = :user 
    	        AND (sd.date >= :today OR ed.date >= :today)
    			ORDER BY sd.date
    	');
    	$query->setParameters(array(
    		'user' => $user,
    		'today' => $date
    		));
    	$query->setFirstResult($offset)->setMaxResults($maxResults);
    	
    	return $query->getOneOrNullResult();
    }
    
    public function eventsInRange(User $user, \DateTime $start, \DateTime $end)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT e, rel, sd, ed
                FROM DroppyEventBundle:Event e
                JOIN e.startDateTime sd
                LEFT JOIN e.endDateTime ed
                JOIN e.droppingUsers rel
                WHERE rel.user = :user 
                AND (sd.date >= :start
                OR ed.date < :end)
                AND rel.inCalendar = true
                ORDER BY sd.date ASC, sd.allDay ASC
                ');
        $query->setParameters(array(
                'user' => $user,
                'start' => $start,
                'end' => $end
                ));
        return $query->getResult();                
    }
    
    public function getEventsToUndrop(User $dropping, User $creator)
    {
    	$query = $this->getEntityManager()->createQuery('
    			SELECT r, e
    			FROM DroppyUserBundle:UserDropsEventRelation r
    			JOIN r.event e
    			WHERE r.user = :user
    			AND e.creator = :creator
    		')->setParameter('user', $dropping)->setParameter('creator', $creator);
    	return $query->getResult();
    }
    
    public function getEventRelation(User $user, Event $event)
    {
    	$query = $this->getEntityManager()->createQuery('
    			SELECT r, u, e
    			FROM DroppyUserBundle:UserDropsEventRelation r
    			JOIN r.user u
    			JOIN r.event e
    			WHERE r.user = :user
    			AND r.event = :event
    		')->setParameter('user', $user)->setParameter('event', $event);
    	return $query->getSingleResult();
    }
}

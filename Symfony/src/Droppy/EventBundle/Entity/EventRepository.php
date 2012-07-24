<?php

namespace Droppy\EventBundle\Entity;

use Droppy\UserBundle\Entity\UserDropsEventRelation;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Droppy\UserBundle\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * EventRepository
 */
class EventRepository extends EntityRepository
{
	public function getCreatedEvents(User $user, User $currentUser, $offset = 0, $maxResults=20)
	{
        $query = $this->getEntityManager()->createQuery('
                SELECT e, p, c, i
                FROM DroppyEventBundle:Event e
                JOIN e.privacySettings p
                JOIN e.color c
                JOIN e.iconSet i
                WHERE e.creator = :creator
                AND e.locked = false
                AND (p.visibility = :visibility
                    OR e.creator = :user)
                ORDER BY e.creationDate DESC 
                ')->setParameter('creator', $user)
                  ->setParameter('visibility', 'public')
                  ->setParameter('user', $currentUser);        
        $query->setFirstResult($offset)->setMaxResults($maxResults);
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $results = iterator_to_array($paginator);
		
        return $this->eventsToEventsRelations($results, $currentUser);
    }
    
    public function getEventsRelations(User $user, $events)
    {
        $eventsIds = array_map(function(Event $event) { 
            return $event->getId(); 
        }, $events);
        $query = $this->getEntityManager()->createQuery('
                SELECT r, e
                FROM DroppyUserBundle:UserDropsEventRelation r
                JOIN r.event e
                WHERE r.user = :user
                AND e.id IN (:ids)
                ')->setParameter('user', $user)->setParameter('ids', $eventsIds);
    
        return $query->getResult();
    }

    public function getLatestEvents(User $user, \DateTime $date, $offset=0, $maxResults=20)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT e, d, p
                FROM DroppyEventBundle:Event e 
                JOIN e.startDateTime d
                JOIN e.privacySettings p 
                WHERE d.date >= :today
                AND p.visibility = :visibility
                AND e.locked = false 
                ORDER BY d.date DESC
                ')->setParameter('today', $date)->setParameter('visibility', 'public');
        $query->setFirstResult($offset)->setMaxResults($maxResults);
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $results = iterator_to_array($paginator);
		
        return $this->eventsToEventsRelations($results, $user);
    }

    public function searchEvents(User $user, $keywords, $places, $start, $end, $offset=0, $maxResults=20)
    {   
    	$qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('e')
            ->from('DroppyEventBundle:Event', 'e')
            ->leftJoin('e.startDateTime', 'sd')
            ->addSelect('sd')
            ->leftJoin('e.endDateTime', 'ed')
            ->addSelect('ed')
            ->leftJoin('e.creator', 'c')
            ->addSelect('c')
            ->leftJoin('e.location', 'l')
            ->addSelect('l');

        foreach(explode(' ', $keywords) as $keyword) {
            if($keyword == ' ' || strlen($keyword) < 1) continue;
            $qb->andWhere($qb->expr()->orx(
                $qb->expr()->like("e.name", $qb->expr()->literal('%'. $keyword . '%')),
                $qb->expr()->like("e.details", $qb->expr()->literal('%' . $keyword . '%')),
                $qb->expr()->like("c.username", $qb->expr()->literal('%' . $keyword . '%'))
            ));
        }

        foreach(explode(' ', $places) as $place) {
            if($place == ' ' || strlen($place) < 1) continue;
            $qb->andwhere($qb->expr()->orx(
                $qb->expr()->like("e.address", $qb->expr()->literal('%'. $place . '%')),
                $qb->expr()->like("l.place", $qb->expr()->literal('%' . $place . '%'))
            ));
        }
        
        if( !is_null($start) ) {
            $qb->andWhere('sd.date >= :start')
                ->setParameters(array(
                    'start' => $start
                ));
        }
        if( !is_null($end) ){ 
            $qb->andWhere('ed.date <= :end')
                ->setParameters(array(
                    'end' => $end
                ));
        }

        $qb->andWhere('e.locked = false')
            ->orderBy('sd.date', 'ASC');
        
        $query = $qb->getQuery();
        $query->setFirstResult($offset)->setMaxResults($maxResults);

        return $this->eventsToEventsRelations($query->getResult(), $user);
    }

    public function getDroppedEvents(User $user, User $current, $offset=0, $maxResults=20)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $today = new \DateTime;
        $today->setTime(0, 0, 0);
    	$qb->select('u')
    	   ->from('DroppyUserBundle:User', 'u')
           ->join('u.droppedEvents', 'r')
           ->addSelect('r')
           ->join('r.event', 'e')
           ->addSelect('e')
           ->join('e.startDateTime', 'sd')
           ->addSelect('sd')
           ->join('e.endDateTime', 'ed')
           ->addSelect('ed')
           ->leftJoin('e.droppingUsers', 'd')
           ->addSelect('d')
           ->leftJoin('d.user', 'du')
           ->addSelect('du')
           ->join('e.iconSet', 'i')
           ->addSelect('i')
           ->join('e.privacySettings', 'p')
           ->addSelect('p')
           ->join('e.color', 'co')
           ->addSelect('co')
           ->leftJoin('e.location', 'l')
           ->addSelect('l')
    	   ->join('e.creator', 'c')
    	   ->addSelect('c')
       	   ->join('c.personalDatas', 'pd')
    	   ->addSelect('pd')
    	   ->join('pd.iconSet', 'i2')
    	   ->addSelect('i2')
           ->where('r.user = :user')
    	   ->andWhere('du = :current')
           ->andWhere('ed.date >= :today')
           ->andWhere('e.locked = false')
//           ->orderBy('sd.date', 'ASC')
           ->setParameters(array(
           		   'current' => $current,
                   'user' => $user,
                   'today' => $today
                   ));
        $query = $qb->getQuery();
        /*$query->setFirstResult($offset)->setMaxResults($maxResults);
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $results = iterator_to_array($paginator);*/
        $result = $query->getOneOrNullResult();
        if($result == null)
        {
        	return array();
        }
        else
        {
        	return $this->relationsToEventsRelations($result->getDroppedEvents());
        }
    }
    
    public function eventsInRange(User $user, User $current, \DateTime $start, \DateTime $end)
    {
    	$qb = $this->getEntityManager()->createQueryBuilder();
    	$qb->select('u')
    	   ->from('DroppyUserBundle:User', 'u')
           ->join('u.droppedEvents', 'r')
           ->addSelect('r')
    	   ->join('r.event', 'e')
    	   ->addSelect('e')
    	   ->join('e.startDateTime', 'sd')
    	   ->addSelect('sd')
    	   ->join('e.endDateTime', 'ed')
    	   ->addSelect('ed')
           ->leftJoin('e.droppingUsers', 'd')
           ->addSelect('d')
           ->leftJoin('d.user', 'du')
           ->join('e.iconSet', 'i')
           ->addSelect('i')
    	   ->join('e.privacySettings', 'p')
    	   ->addSelect('p')
    	   ->join('e.color', 'co')
   		   ->addSelect('co')
   		   ->leftJoin('e.location', 'l')
   		   ->addSelect('l')
   		   ->join('e.creator', 'c')
   		   ->addSelect('c')
   		   ->join('c.personalDatas', 'pd')
   		   ->addSelect('pd')
   		   ->join('pd.iconSet', 'i2')
   		   ->addSelect('i2')
    	   ->where('r.user = :user')
    	   ->andWhere('du = :current')
    	   ->andWhere('r.inCalendar = true')
    	   ->andWhere('e.locked = false')
    	   ->andWhere($qb->expr()->orx(
    	   				$qb->expr()->andx(
    	   					$qb->expr()->lte('sd.date', ':end'),
    	   					$qb->expr()->gte('sd.date', ':start')),
    	   				$qb->expr()->andx(
    						$qb->expr()->gte('ed.date', ':start'),
    						$qb->expr()->lte('ed.date', ':end')
    	   				))
    				);
    	$query = $qb->getQuery();
    	   					
    	$query->setParameters(array(
                    'user' => $user,
                    'current' => $current,
                    'start' => $start,
                    'end' => $end
    	));
    	$result = $query->getOneOrNullResult();
    	if($result == null)
    	{
    		return array();
    	}
    	else
    	{
    		return $this->relationsToEventsRelations($result->getDroppedEvents());
    	}
    }
    
    public function eventsToEventsRelations($events, User $user)
    {
        $toReturn = array();
        foreach($events as $event)
        {
            $toReturn[] = $this->getEventAndRelation($event, $user);
        }
        return $toReturn;
    }
    
    protected function relationsToEventsRelations($relations) 
    {
    	$toReturn = array();
    	foreach($relations as $relation)
    	{
    		$event = $relation->getEvent();
    		$toReturn[] = array(
    			'event' => $event,
    			'relation' => $event->getDroppingUsers()->first(),
    			'liked' => false
    		); 
    	}
    	return $toReturn;
    }
    
    public function getEventAndRelation(Event $event, User $user)
    {
    	return array(
    		'event' => $event,
    		'relation' => $this->getRelationFromUser($user, $event->getDroppingUsers()),
    	    'liked' => $this->isLikingEvent($event, $user),
    	);
    }
    
    protected function isLikingEvent(Event $event, User $user)
    {
        foreach($user->getLikedEvents() as $likedEvent)
        {
            if($event->equals($likedEvent))
            {
                return true;
            }
        }
        return false;
    }
        
    protected function getRelationFromUser(User $user, $relations) {
    	foreach($relations as $relation) {
    		if($relation->getUser()->equals($user)) {
    			return $relation;
    		}
    	}
    	return null;
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
    	return $query->getOneOrNullResult();
    }
}

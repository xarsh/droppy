<?php

namespace Droppy\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Droppy\UserBundle\Entity\User;
use Droppy\EventBundle\Entity\Event;
use Droppy\UserBundle\Entity\UserDropsUser;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
	public function getDroppingUsers(User $user, $offset = 0, $maxResults=20)
	{
        $query = $this->getEntityManager()->createQuery('
			SELECT r, current_user, dropped_user, dropped_user_droppers_r, dropped_user_dropper
			FROM DroppyUserBundle:UserDropsUserRelation r
			JOIN r.dropping current_user
			JOIN r.dropped dropped_user
			LEFT JOIN dropped_user.droppers dropped_user_droppers_r
			LEFT JOIN dropped_user_droppers_r.dropping dropped_user_dropper
			WHERE current_user = :user
			ORDER BY r.date DESC
                ')->setParameter('user', $user);
        $query->setFirstResult($offset)->setMaxResults($maxResults);
        $results = new ArrayCollection($query->getResult());
        return $results->map(function(UserDropsUserRelation $rel) {
        	return $rel->getDropped();
        });
		
    }

	public function getDroppers(User $user, $offset = 0, $maxResults=20)
	{
        $query = $this->getEntityManager()->createQuery('
			SELECT r, current_user, dropping_user, dropping_user_droppers_r, dropping_user_dropper
			FROM DroppyUserBundle:UserDropsUserRelation r
			JOIN r.dropped current_user
			JOIN r.dropping dropping_user
			LEFT JOIN dropping_user.droppers dropping_user_droppers_r
			LEFT JOIN dropping_user_droppers_r.dropping dropping_user_dropper
			WHERE current_user = :user
			ORDER BY r.date DESC
                ')->setParameter('user', $user);
        $query->setFirstResult($offset)->setMaxResults($maxResults);
        $results = new ArrayCollection($query->getResult()); 
        return $results->map(function(UserDropsUserRelation $rel) { 
        	return $rel->getDropping(); 
        });
    }

	public function getPopularUsers(User $user = null, $offset = 0, $maxResults = 20)
	{
		$query = $this->getEntityManager()->createQuery('
		        SELECT u, d, du
		        FROM DroppyUserBundle:User u
		        LEFT JOIN u.droppers d
		        LEFT JOIN d.dropping du
		        WHERE u.id NOT IN (:id)
		        ORDER BY u.droppersNumber DESC
		        ')->setParameter('id', $user === null ? -1 : $user->getId());
		$query->setFirstResult($offset)->setMaxResults($maxResults);
	    return new ArrayCollection($query->getResult());
	}
	
	public function isLikingEvent(User $user, Event $event)
	{
		$query = $this->getEntityManager()->createQuery('
		    			SELECT u.id, e.id
		    			FROM DroppyUserBundle:User u
		    			JOIN u.likedEvents e
		    			WHERE e.id = :id
		    		')->setParameter('id', $event->getId());
		return $query->getOneOrNullResult() !== null;
	}
	
	public function getRelation(User $dropping, User $dropped)
	{
		$query = $this->getEntityManager()->createQuery('
				SELECT r, dg, dd
				FROM DroppyUserBundle:UserDropsUserRelation r
				JOIN r.dropping dg
				JOIN r.dropped dd
				WHERE dg = :dropping
				AND dd = :dropped
			')->setParameter('dropping', $dropping)->setParameter('dropped', $dropped);
		return $query->getSingleResult();
	}
}

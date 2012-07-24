<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
*/
class EventRepository extends EntityRepository
{
    public function getEventById($event_id)
    {
        $query = $this->_em->createQuery(
				'SELECT c FROM DroppyWebApplicationBundle:Event c WHERE c.id = :id'
				)->setParameter('id', $event_id);

	return $query->getSingleResult();

    }
}

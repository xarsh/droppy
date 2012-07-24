<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ScheduleRepository
 */
class ScheduleRepository extends EntityRepository
{
	public function getScheduleById($schedule_id)
	{
		$query = $this->_em->createQuery(
				'SELECT c FROM DroppyWebApplicationBundle:Schedule c WHERE c.id = :id'
				)->setParameter('id', $schedule_id);

		return $query->getSingleResult();
	}

	public function getRecommendedSchedules()
	{
		$query = $this->_em->createQuery(
				'SELECT c FROM DroppyWebApplicationBundle:Schedule c WHERE c.id IN(1,2,3,4,5)' // numbers are dummy
				);

		return $query->getResult();   
	}

	public function getOfficialSchedules()
	{
		$query = $this->_em->createQuery(
				'SELECT c FROM DroppyWebApplicationBundle:Schedule c WHERE c.id IN(1,2,3,4,5)'  // numbers are dummy
				);

		return $query->getResult();      
	}

	public function getNewSchedules($maxResults = "5")
	{
		$query = $this->_em->createQuery(
				'SELECT c FROM DroppyWebApplicationBundle:Schedule c ORDER BY c.id DESC'
				)->setMaxResults($maxResults);

		return $query->getResult();
	}

	public function getPopularSchedules($maxResults = "5") // fix it later
	{
		$query = $this->_em->createQuery(
				'SELECT c FROM DroppyWebApplicationBundle:Schedule c ORDER BY c.id DESC'
				)->setMaxResults($maxResults);

		return $query->getResult();
	}

	/*public function getTodayPopularCalendars($maxResults = "5")
	{
		$query = $this->_em->createQuery(
				'SELECT c FROM
	}*/

	public function getScheduleQueryByCreator($userId)
	{
		$qb = $this->_em->createQueryBuilder();
		$qb->select('s')->from('DroppyWebApplicationBundle:Schedule','s')->join('s.creator', 'c')->where('c.id = :id')->setParameter('id', $userId);
		return $qb;

	}
}


<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 */
class TagRepository extends EntityRepository
{
	/**
	 * Get Tag array collection from string array 
	 * 
	 * @param array $strArray
	 * @return ArrayCollection
	 */
	public function getTagsInArray($strArray)
	{
		$str = implode(", ", array_map(function ($s) { return '\'' . $s . '\''; }, $strArray));
		return $this->_em->createQuery(
				'SELECT t FROM DroppyWebApplicationBundle:Tag t WHERE t.name IN (?1)')
				->setParameter(1, $strArray)
				->getResult();
	}
}

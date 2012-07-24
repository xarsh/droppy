<?php

namespace Droppy\MainBundle\Normalizer;

use Doctrine\ORM\EntityManager;
use Droppy\MainBundle\Exception\NormalizationException;

class NormalizerHelper
{
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
	public function denormalizeScalars($object, $datas)
	{
		foreach($datas as $property => $value)
		{
			if(!is_array($value) && $value !== null)
			{
				$setter = $this->getSetter($property);
				if(method_exists($object, $setter))
				{
					$object->$setter($value);
				}
			}
		}
		return $object;
	}
	
	public function getSetter($variableName, $adder=false)
	{
		$prefix = $adder ? 'add' : 'set';
		return $prefix . $this->getClass($variableName);
	}
	
	public function getClass($variableName)
	{
	    $methodArray = explode('_', $variableName);
	    return implode('', array_map('ucwords', $methodArray));
	}
	
	public function getFromId($id, $repository)
	{
	    if(($object = $this->em->getRepository($repository)->find($id)) !== null)
	    {
	        return $object;
	    }
	    else
	    {
	        throw new NormalizationException('Object does not exist.');
	    }
	}
	
	public function getFromField($field, $value, $repository)
	{
	    $method = 'findOneBy' . ucwords($field);
	    return $this->em->getRepository($repository)->$method($value);
	}
	
	public function persist($entity)
	{
		$this->em->persist($entity);
	}
}
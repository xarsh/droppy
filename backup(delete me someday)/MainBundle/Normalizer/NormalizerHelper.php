<?php

namespace Droppy\MainBundle\Normalizer;

class NormalizerHelper
{
	public function denormalizeScalars($object, $datas)
	{
		foreach($datas as $property => $value)
		{
			if(!is_array($value))
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
	
	protected function getSetter($variableName)
	{
		$methodArray = split('_', $variableName);
		return 'set' . implode('', array_map(ucwords, $methodArray));
	}
}
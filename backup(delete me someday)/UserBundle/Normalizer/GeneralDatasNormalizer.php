<?php 

namespace Droppy\UserBundle\Normalizer;
 
use Droppy\UserBundle\Entity\CurrentLocation;
use Droppy\UserBundle\Entity\BirthPlace;
use Droppy\UserBundle\Entity\BirthDate;

use Symfony\Component\Validator\Constraints\DateTime;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Normalizer\PrivacySettingsNormalizer;
 
class GeneralDatasNormalizer implements NormalizerInterface
{
	protected $privacySettingsNormalizer;
	
	public function __construct(PrivacySettingsNormalizer $psn)
	{
		$this->privacySettingsNormalizer = $psn;
	}
	
    public function normalize($object, $format = null)
    {
        $reflectionClass = new \ReflectionClass($object);
 
        $data = array();
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if (strtolower(substr($reflectionMethod->getName(), 0, 3)) === 'get'
            	&& $reflectionMethod->getNumberOfRequiredParameters() > 0) {
 
	            $property = lcfirst(substr($reflectionMethod->getName(), 3));
            	$value = $reflectionMethod->invoke($object);
 
	            $data[$property] = $this->encodeValue($value);
        	}
        }
        return $data;
    }
    
    protected function encodeValue($value) {
    	if(!is_object($value))
    	{
    		return $value;
    	}
    	else if(get_class($value) === 'DateTime')
    	{
    		return $value->format('Y-m-d H:i');
    	}
    	else if(get_class($value) === 'PrivacySettings')
    	{
    		return $this->privacySettingsNormalizer->normalized($value);
    	}
    	else
    	{
    		throw new \Exception(get_class($value) . ' is not supported');
    	}
    }
    
    public function denormalize($data, $class, $format=null)
    {
    	return new BirthDate();
    }
    
    public function supportsNormalization($data, $format=null)
    {
    	return $data instanceof BirthDate || $data instanceof BirthPlace || $data instanceof CurrentLocation;
    }
    
    public function supportsDenormalization($data, $type, $format=null)
    {
    	return false;
    }
}

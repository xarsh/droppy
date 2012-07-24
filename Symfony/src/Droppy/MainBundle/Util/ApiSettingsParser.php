<?php

namespace Droppy\MainBundle\Util;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Droppy\UserBundle\Entity\UserManager;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Droppy\MainBundle\Exception\MissingParameterException;
use Droppy\MainBundle\Exception\ObjectNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class ApiSettingsParser
{
	protected $em;
	protected $securityContext;
	protected $settingsPath;
    
    public function __construct(SecurityContextInterface $securityContext, EntityManager $em, $settingsPath)
    {
        $this->settingsPath = $settingsPath;
        $this->securityContext = $securityContext;
        $this->em = $em;
    }
    
    public function parseParameters(Request $request)
    {
        $route = $request->attributes->get('_route');
        $route = str_replace('ajax', 'api', $route);
        $bundleName = $this->getBundle($route);
        $dom = new \DOMDocument;
        $dom->load($this->getBundleSettingsFile($bundleName));
        $setting = $this->getSetting($dom, $route);
        $parameterList = array();
        foreach($setting->getElementsByTagName('parameter') as $parameter)
        {
            $parameterList[$parameter->getAttribute('id')] = $this->parseSingleParameter($request, $parameter);
        }
        return $parameterList;
    }
    
    protected function parseSingleParameter(Request $request, $parameter)
    {
        $parameterId = $parameter->getAttribute('id');
        if($request->getMethod() === 'GET')
        {
            $requestParameters = $request->query;
        }
        else
        {
        	$requestParameters = $request->request;
        }
        if($requestParameters->has($parameterId) === false)
        {
            if($parameter->getAttribute('optional') === "false" && $parameter->hasAttribute('default') === false)
            {
                throw new MissingParameterException(sprintf('This route needs parameter "%s".', $parameterId));
            }
            else if($parameter->hasAttribute('default'))
            {
                $default = $parameter->getAttribute('default'); 
                return $this->parseDefault($default);
            }
        }
        $value = $requestParameters->get($parameterId);
        if($parameterId === 'user_id')
        {
        	$value = $this->em->getRepository('DroppyUserBundle:User')->find($value);
        }
        $min = $parameter->getAttribute('min');
        $max = $parameter->getAttribute('max');
        if(empty($min) === false && $value < $min)
        {
            $value = $min;
        }
        if(empty($max) === false && $value > $max)
        {
            $value = $max;
        }
        return $value;
    }
    
    protected function parseDefault($default)
    {
        if($default[0] !== '_')
        {
            return $default;
        }
        switch(substr($default, 1))
        {
            case "now": 
                return new \DateTime();
            case "today":
                $now = new \DateTime();
                $now->setTime(0, 0, 0);
                return $now;
            case "current_user":
            	if($this->securityContext->isGranted('ROLE_USER') === false)
            	{
            		throw new HttpException(403);
            	}
            	return $this->securityContext->getToken()->getUser();
            default:
                throw new InvalidArgumentException(sprintf("Argument %s is invalid.", $default));
        }
    }
    
    protected function getBundle($route)
    {
        $tmp = explode('_', $route);
        return ucfirst($tmp[1]);
    }
        
    protected function getSetting($dom, $route)
    {
        if(($setting = $dom->getElementById($route)) !== null)
        {
            return $setting;
        }
        throw new ObjectNotFoundException(sprintf('Settings not found for route %s.', $route));
    }
    
    protected function getBundleSettingsFile($bundleName)
    {
        $file = __DIR__ . '/../../' . $bundleName . 'Bundle/' . $this->settingsPath;
        if(file_exists($file) === false)
        {
            throw new FileNotFoundException(sprintf('File "%s" does not exists', $file));
        }
        return $file;
    }
}

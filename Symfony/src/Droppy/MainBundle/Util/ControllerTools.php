<?php

namespace Droppy\MainBundle\Util;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class ControllerTools
{
    protected $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function normalizeData($toEncode, $normalizerArray)
    {
        $format = $this->container->getParameter('droppy_main.encoding_format');
        $normalizers = $this->getNormalizers($normalizerArray);
        $serializer = new Serializer($normalizers, $this->getEncoderArray($format));
        $datas = $serializer->serialize($toEncode, $format);
        return $datas;
    }
    
    protected function getNormalizers($normalizerArray)
    {
        $normalizers = array();
        foreach($normalizerArray as $normalizer)
        {
            $normalizers[] = $this->container->get($normalizer);
        }
        return $normalizers;
    }
    
    public function getEncoderArray($format)
    {
        if($format === 'json')
        {
            return array('json' => new JsonEncoder());
        }
        else if($format === 'xml')
        {
            return array('xml' => new XmlEncoder());
        }
        else
        {
            throw new InvalidArgumentException(sprintf('Format %s not supported.', $format));
        }
    
    }
    
    public function checkOrCreateDirectory($directory)
    {
        if(file_exists($directory) === false)
        {
            mkdir($directory, 0777, true);
        }
    }
    
    public function isAdmin()
    {
        return $this->container->get('security.context')->isGranted('ROLE_ADMIN');
    }
    
    public function getEventRepository()
    {
        return $this->container->get('doctrine')->getRepository('DroppyEventBundle:Event');
    }
    
    public function getUserRepository()
    {
    	return $this->container->get('doctrine')->getRepository('DroppyUserBundle:User');
    }
    
    public function getUser()
    {
        return $this->container->get('security.context')->getToken()->getUser();
    }
    
    public function getAllParameters()
    {
        $request = $this->container->get('request');
        $container = ($request->getMethod() === 'POST') ? $request->request : $request->query;
        return $container->all();
    }
    
    
    public function getParameters()
    {
        $request = $this->container->get('request');
        return $this->container->get('droppy_main.api_settings_parser')->parseParameters($request);
    }
    
    public function getParameterOrNull($name)
    {
        $request = $this->container->get('request');
        $container = ($request->getMethod() === 'POST') ? $request->request : $request->query;
        if($container->has($name) === false)
        {
            return null;
        }
        return $container->get($name);
    }
    
    public function getParameter($name)
    {
        $parameter = $this->getParameterOrNull($name);
        if($parameter === null)
        {
            throw new HttpException(400, sprintf('Missing parameter %s.', $name));
        }
        return $parameter;
    }
    
    public function denormalizeDatas($toDecode, $type, $normalizerArray)
    {
        $format = $this->container->getParameter('droppy_main.encoding_format');
        $normalizers = $this->getNormalizers($normalizerArray);
        $serializer = new Serializer($normalizers, $this->getEncoderArray($format));
        $entity = $serializer->deserialize($toDecode, $type, $format);
        return $entity;
    }
    
    public function checkErrors($errors)
    {
        if(count($errors) > 0)
        {
            throw new HttpException(400, $errors[0]->getMessage());
        }
    }
    
    public function updateEntity($entity, $andFlush=true)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $em->persist($entity);
        if($andFlush === true)
        {
            $em->flush();
        }
    }

} 

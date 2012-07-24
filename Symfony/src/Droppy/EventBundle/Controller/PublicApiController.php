<?php

namespace Droppy\EventBundle\Controller;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;


use Droppy\UserBundle\Entity\UserDropsEventRelation;
use Droppy\EventBundle\Entity\Event;
use Symfony\Component\HttpFoundation\Response;

class PublicApiController
{
    protected $container;
    protected $tools;
    
    public function __construct($container)
    {
        $this->container = $container;
        $this->tools = $this->container->get('droppy_main.controller_tools');
    }
    
    public function getLatestEventsAction()
    {
        list($date, $offset, $maxResults) = array_values($this->tools->getParameters());
        $results = $this->tools->getEventRepository()->latestEvents($date, $offset, $maxResults);
        $encodedDatas = $this->tools->normalizeData($results, array('droppy_event.normalizer.event'));
        return new Response($encodedDatas, 200);
    }
    

}
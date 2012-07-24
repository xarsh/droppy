<?php

namespace Droppy\EventBundle\Normalizer;

use Symfony\Component\DependencyInjection\Container;


class EventNormalizer extends EventBaseNormalizer
{
	public function __construct(Container $container)
	{
        parent::__construct($container);
	}
	
	public function normalize($event, $format=null)
	{
	    $data = parent::normalize($event, $format);
	    $data['creator'] = $this->userNormalizer->normalize($event->getCreator());
		return $data;
	}
}
<?php

namespace Droppy\UserBundle\Listener;

use Symfony\Component\HttpFoundation\Session;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Droppy\UserBundle\Entity\User;

use Droppy\UserBundle\Entity\Settings;

class UserCreationListener
{
    protected $session;
    
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		if($entity instanceof User)
		{
		    $settings = $entity->getSettings();
			$this->setColor($settings, $em);
            $this->setLocale($settings, $em);
            $this->setTimezone($settings, $em);
			$em->flush();
		}
	}
	
	//TODO get timezone from ip
	protected function setTimezone(Settings $settings, EntityManager $em)
	{
	    $timezone = $em->getRepository('DroppyMainBundle:Timezone')->findOneByName('Tokyo');
	    $settings->setTimezone($timezone);
	}
	
	protected function setLocale(Settings $settings, EntityManager $em)
	{
	    $locale = $this->session->getLocale();
	    $language = $em->getRepository('DroppyUserBundle:Language')->findOneByLocale($locale);
	    if($language === null)
	    {
	        $language = $em->getRepository('DroppyUserBundle:Language')->findOneByLocale('ja'); //TODO
	    }
	    $this->session->setLocale($language->getLocale());
	    $settings->setLanguage($language);
	}
	
	protected function setColor(Settings $settings, EntityManager $em)
	{
	    $colorsNumber = $em->createQuery('SELECT COUNT(c.id) FROM DroppyMainBundle:Color c')->getSingleScalarResult();
	    $color = $em->getRepository('DroppyMainBundle:Color')->find(rand(1, $colorsNumber));
	    $settings->setColor($color);
	}
}
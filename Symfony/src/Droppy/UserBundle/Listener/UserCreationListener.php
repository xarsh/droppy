<?php

namespace Droppy\UserBundle\Listener;

use Droppy\UserBundle\Entity\PersonalDatas;

use Droppy\MainBundle\Entity\PrivacySettings;

use Symfony\Component\HttpFoundation\Session;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Droppy\UserBundle\Entity\User;

use Droppy\UserBundle\Entity\Settings;

class UserCreationListener
{
    protected $session;
    protected $iconPath;
    protected $iconExtension;
    
    public function __construct(Session $session, $iconPath, $iconExtension)
    {
        $this->session = $session;
        $this->iconPath = $iconPath;
        $this->iconExtension = $iconExtension;
    }
    
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		if($entity instanceof User)
		{
		    $settings = $entity->getSettings();
		    $this->setPrivacySettings($settings);
			$this->setColor($settings, $em);
            $this->setLocale($settings, $em);
            $this->setTimezone($settings, $em);
            $this->setIconSet($entity->getPersonalDatas());
			$em->flush();
		}
	}
	
	protected function setIconSet(PersonalDatas $personalDatas)
	{
	    $nb = rand(0, 10);
	    $nb = ($nb < 10) ? ('0' . strval($nb)) : strval($nb); 
	    $path = $this->iconPath . $nb . '.' . $this->iconExtension;
	    $iconSet = $personalDatas->getIconSet();
	    $iconSet->setThumbnailPath($path);
	    $iconSet->setSmallIconPath($path);
	    $iconSet->setUploaded(true);
	}
	
	protected function setPrivacySettings(Settings $settings)
	{
	    $privacySettings = new PrivacySettings();
	    $privacySettings->setVisibility('public');
	    $settings->setPrivacySettings($privacySettings);
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
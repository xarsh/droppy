<?php

namespace Droppy\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Droppy\UserBundle\Entity\User;

class AJAXRequestController extends ContainerAware
{
	public function dropUserAction(User $toDrop)
	{
	    $user = $this->checkInfosAndGetUser($toDrop, true);
	    $this->container->get('droppy_user.user_manager')->dropUser($user, $toDrop);
	    $this->container->get('droppy_user.user_manager')->updateUser($user);
	    return new Response('success', 200);
	}
	
	public function undropUserAction(User $toUndrop)
	{
        $user = $this->checkInfosAndGetUser($toUndrop, false);
	    $this->container->get('droppy_user.user_manager')->undropUser($user, $toUndrop);
	    $this->container->get('droppy_user.user_manager')->updateUser($user);
	    return new Response('success', 200);
	}
	
	/**
	 * Checks if user is logged
	 * Checks if user can drop or undrop
	 * 
	 * @param User $toDrop
	 * @param boolean $drop
	 * @return User
	 */
	public function checkInfosAndGetUser(User $other, $drop)
	{
		$dataChecker = $this->container->get('droppy_main.data_checker');
		$dataChecker->checkXmlHttpRequest($this->container->get('request'));
		$user = $dataChecker->getUserOrDie();
		$dataChecker->checkDroppedUser($other, $drop);
		return $user;
	}
	
	public function getUserDetailsHtmlAction()
	{
	    return $this->container->get('templating')->renderResponse('DroppyUserBundle:Includes:user_details.html.twig');
	}
	
		
}
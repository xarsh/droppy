<?php

namespace Droppy\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Droppy\UserBundle\Entity\User;
use Droppy\EventBundle\Entity\Event;

class UserInformationController extends ContainerAware
{
	public function getUserInformationAction(User $user)
	{
		$currentUser = $this->container->get('security.context')->getToken()->getUser();
		$eventRepository = $this->container->get('doctrine')->getRepository('DroppyEventBundle:Event');
		$userRepository = $this->container->get('doctrine')->getRepository('DroppyUserBundle:User');
		$um = $this->container->get('droppy_user.user_manager');

		$createdEvents = $eventRepository->getCreatedEvents($user);
		$droppingUsers = $um->usersToUsersAndDropStatus($userRepository->getDroppingUsers($user), $currentUser);
		$droppers = $um->usersToUsersAndDropStatus($userRepository->getDroppers($user), $currentUser);
		$popularUser = $um->usersToUsersAndDropStatus($userRepository->getPopularUsers($currentUser), $currentUser);

		return $this->container->get('templating')->renderResponse('DroppyUserBundle:Layout:user_information_details.html.twig', array(
			'user' => $user,
			'created_events' => $createdEvents,
			'dropping_users' => $droppingUsers,
			'droppers' => $droppers,
			'popular_users' => $popularUser
		));	
	}
}

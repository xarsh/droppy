<?php

namespace Droppy\UserBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

use Droppy\UserBundle\Entity\UserDropsUserRelation;
use Doctrine\Common\Collections\ArrayCollection;
use Droppy\UserBundle\Entity\User;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Droppy\UserBundle\Entity\UserDropsEventRelation;
use Droppy\EventBundle\Entity\Event;
use Droppy\EventBundle\Exception\XMLHttpRequestException;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    protected $container;
    protected $tools;
    
    public function __construct($container)
    {
        $this->container = $container;
        $this->tools = $this->container->get('droppy_main.controller_tools');
    }

    public function getIsobeSelectedUsersAction()
    {
    	$user = $this->tools->getUser();
    	list($offset, $maxResults) = array_values($this->tools->getParameters());
    	$results = $this->tools->getUserRepository()->getIsobeSelectedUsers($offset, $maxResults);
    	$results = $this->container->get('droppy_user.user_manager')->usersToUsersAndDropStatus($results, $user);
    	$encodedDatas = $this->tools->normalizeData($results, array('droppy_user.normalizer.user_minimal'));
    	return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function getAetelRecommendedUsersAction()
    {
    	$user = $this->tools->getUser();
    	list($offset, $maxResults) = array_values($this->tools->getParameters());
    	$results = $this->tools->getUserRepository()->getAetelRecommendedUsers($offset, $maxResults);
    	$results = $this->container->get('droppy_user.user_manager')->usersToUsersAndDropStatus($results, $user);
    	$encodedDatas = $this->tools->normalizeData($results, array('droppy_user.normalizer.user_minimal'));
    	return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function getRecommendedUsersAction()
    {
    	$user = $this->tools->getUser();
    	list($offset, $maxResults) = array_values($this->tools->getParameters());
    	$results = $this->tools->getUserRepository()->getPopularUsers($user, $offset, $maxResults);
    	$results = $this->container->get('droppy_user.user_manager')->usersToUsersAndDropStatus($results, $user);
    	$encodedDatas = $this->tools->normalizeData($results, array('droppy_user.normalizer.user_minimal'));
    	return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function lockUserAction()
    {
        $user = $this->tools->getUser();
        $userId = $this->tools->getParameter('user_id');
        if($userId !== $user->getId() && $this->tools->isAdmin() === false)
        {
            throw new AccessDeniedException();
        }
        $this->container->get('droppy_user.user_manager')->lockUser($user);
        return new Response('ok', 200, array('Content-Type' => 'text/plain'));
    }
    
    
    public function dropUserAction()
    {
        $user = $this->tools->getUser();
    	$userId = $this->tools->getParameter('user_id');
    	$toDrop = $this->container->get('droppy_user.user_manager')->getUserById($userId);
    	$events = $this->container->get('droppy_user.user_manager')->dropUser($user, $toDrop);
    	$this->container->get('droppy_user.user_manager')->updateUser($user);
    	$encodedDatas = $this->tools->normalizeData($events, array(
    	        'droppy_user.normalizer.user_drops_event_relation',
    	        'droppy_event.normalizer.event_minimal'));
    	return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function undropUserAction()
    {
    	$user = $this->tools->getUser();
    	$userId = $this->tools->getParameter('user_id');
    	$toDrop = $this->container->get('droppy_user.user_manager')->getUserById($userId);
    	$events = $this->container->get('droppy_user.user_manager')->undropUser($user, $toDrop);
    	$this->container->get('droppy_user.user_manager')->updateUser($user);
    	$encodedDatas = $this->tools->normalizeData($events, array(
    	        'droppy_user.normalizer.user_drops_event_relation',
    	        'droppy_event.normalizer.event_minimal'));
    	
    	return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    

    public function getUserAction()
    {
        $currentUser = $this->tools->getUser();
        $userId = $this->tools->getParameterOrNull('user_id');
        if($userId === null) {
            $user = $this->tools->getUser();
            $dropStatus = true;
        } else {
            $user = $this->container->get('droppy_user.user_manager')->getUserById($userId);
            $dropStatus = $this->container->get('droppy_user.user_manager')->isDroppingUser($currentUser, $user);
        }
        $userAndDropStatus = array('user' => $user, 'dropped' => $dropStatus);
        $encodedDatas = $this->tools->normalizeData($userAndDropStatus, array('droppy_user.normalizer.user'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function getUserByUsernameAction()
    {
        $currentUser = $this->tools->getUser();
        $username = $this->tools->getParameterOrNull('username');
        if($username === null) {
            $user = $this->tools->getUser();
            $dropStatus = true;
        } else {
            $user = $this->container->get('droppy_user.user_manager')->loadUserByUsername($username);
            $dropStatus = $this->container->get('droppy_user.user_manager')->isDroppingUser($currentUser, $user);
        }
        $userAndDropStatus = array('user' => $user, 'dropped' => $dropStatus);
        $encodedDatas = $this->tools->normalizeData($userAndDropStatus, array('droppy_user.normalizer.user'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function getDroppingUsersAction()
    {
        $currentUser = $this->tools->getUser();
        list($user, $offset, $maxResults) = array_values($this->tools->getParameters());
        $results = $user->getDroppingUsers()->slice($offset, $maxResults);
        $results = new ArrayCollection(array_map(function(UserDropsUserRelation $relation) {
            return $relation->getDropped();
        }, $results));
        $results = $this->container->get('droppy_user.user_manager')->usersToUsersAndDropStatus($results, $currentUser);
        $encodedDatas = $this->tools->normalizeData($results, array('droppy_user.normalizer.user_minimal'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function getDroppersAction()
    {
        $currentUser = $this->tools->getUser();
        list($user, $offset, $maxResults) = array_values($this->tools->getParameters());
        $results = $user->getDroppers()->slice($offset, $maxResults);
        $results = new ArrayCollection(array_map(function(UserDropsUserRelation $relation) {
            return $relation->getDropping();
        }, $results));
        $results = $this->container->get('droppy_user.user_manager')->usersToUsersAndDropStatus($results, $currentUser);
        $encodedDatas = $this->tools->normalizeData($results, array('droppy_user.normalizer.user_minimal'));
        return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }
    
    public function searchUsersAction()
    {
        $currentUser = $this->tools->getUser();
    	list($keywords, $places, $offset, $maxResults) = array_values($this->tools->getParameters());
        $results = $this->tools->getUserRepository()
            ->searchUsers(str_replace('　', ' ', $keywords), str_replace('　', ' ', $places), $offset, $maxResults);
        $results = $this->container->get('droppy_user.user_manager')->usersToUsersAndDropStatus($results, $currentUser);
    	list($keywords, $places, $offset, $maxResults) = array_values($this->tools->getParameters());
    	$encodedDatas = $this->tools->normalizeData($results, array('droppy_user.normalizer.user_minimal'));
    	return new Response($encodedDatas, 200, array('Content-Type' => 'application/json'));
    }

    public function setStartedAction()
    {
    	$user = $this->tools->getUser();
    	$user->setHasStarted(true);
    	$this->container->get('droppy_user.user_manager')->updateUser($user);
    	return new Response('Success', 200);
    }
}

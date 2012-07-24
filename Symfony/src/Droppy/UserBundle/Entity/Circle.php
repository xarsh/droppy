<?php

namespace Droppy\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\UserBundle\Entity\Circle
 *
 * @ORM\Table(name="circle")
 * @ORM\Entity
 */
class Circle
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string $name
	 *
	 * @ORM\Column(name="name", type="string", length=45)
	 */
	private $name;

	/**
	 * @var ArrayCollection<Tag> $tags
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\UserBundle\Entity\User", mappedBy="circles")
	 */
	private $users;
	
	public function __construct()
	{
		$this->users = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Add user
	 *
	 * @param User $user
	 */
	public function addUser(User $user)
	{
		$this->users[] = $user;
	}

	/**
	 * Get users
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getUsers()
	{
		return $this->users;
	}
}

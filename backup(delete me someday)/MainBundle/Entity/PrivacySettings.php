<?php

namespace Droppy\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\MainBundle\Entity\PrivacySettings
 *
 * @ORM\Table(name="privacy_settings")
 * @ORM\Entity
 */
class PrivacySettings
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
	 * @var string $visibility
	 *
	 * @ORM\Column(name="visibility", type="string", length=10, nullable=false)
	 * @Assert\NotBlank(message="error.privacy_settings.visibility.blank")
	 * @Assert\Choice(choices={"public", "private", "protected"}, 
	 * 				message="error.privacy_settings.visibility.wrong_choice")
	 */
	private $visibility;

	/**
	 * @var ArrayCollection<User> $authorizedUsers
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\UserBundle\Entity\User")
	 * @ORM\JoinTable(name="users_authorization",
	 * 		joinColumns={@ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")})
	 */
	private $authorizedUsers;

	/**
	 * @var ArrayCollection<User> $authorizedCircles
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\UserBundle\Entity\Circle")
	 * @ORM\JoinTable(name="circles_authorization",
	 * 		joinColumns={@ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="circle_id", referencedColumnName="id")})
	 */
	private $authorizedCircles;

	public function __construct()
	{
		$this->authorizedUsers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->authorizedCircles = new \Doctrine\Common\Collections\ArrayCollection();
		$this->visibility = 'private';
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
	 * Set visibility
	 *
	 * @param string $visibility
	 */
	public function setVisibility($visibility)
	{
		$this->visibility = $visibility;
	}

	/**
	 * Get visibility
	 *
	 * @return string
	 */
	public function getVisibility()
	{
		return $this->visibility;
	}

	/**
	 * Add authorized user
	 *
	 * @param Droppy\UserBundle\Entity\User $authorizedUser
	 */
	public function addAuthorizedUser(\Droppy\UserBundle\Entity\User $authorizedUser)
	{
		$this->authorizedUsers[] = $authorizedUser;
	}
	
	/**
	 * Set authorized users
	 *
	 * @param Doctrine\Common\Collections\Collection
	 */
	public function setAuthorizedUsers(ArrayCollection $authorizedUsers)
	{
	    $this->authorizedUsers = $authorizedUsers;
	}

	/**
	 * Get authorizedUsers
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getAuthorizedUsers()
	{
		return $this->authorizedUsers;
	}
	
	/**
	 * Add authorized circle
	 *
	 * @param Droppy\UserBundle\Entity\User $authorizedCircle
	 */
	public function addAuthorizedCircle(\Droppy\UserBundle\Entity\Circle $authorizedCircle)
	{
		$this->authorizedCircles[] = $authorizedCircle;
	}

	/**
	 * Get authorizedCircles
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getAuthorizedCircles()
	{
		return $this->authorizedCircles;
	}
	
	/**
	 * Set authorized circles
	 *
	 * @param Doctrine\Common\Collections\Collection
	 */
	public function setAuthorizedCircles(ArrayCollection $authorizedCircles)
	{
	    $this->authorizedCircles = $authorizedCircles;
	}
}

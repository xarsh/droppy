<?php

namespace Droppy\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\MainBundle\Entity\Color
 *
 * @ORM\Table(name="color")
 * @ORM\Entity
 */
class Color
{
	/**
	* @var integer $id
	*
	* @ORM\Column(name="id", type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;
	
	/**
	* @var string $name
	*
	* @ORM\Column(name="name", type="string", length=20)
	* @Assert\NotNull()
	*/
	private $name;
	
	/**
	 * @var string $code
	 *
	 * @ORM\Column(name="code", type="string", length=7)
	 * @Assert\NotNull()
	 */
	private $code;
	
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
	 * Set code
	 *
	 * @param string $code
	 */
	public function setCode($code)
	{
	    $this->code = $code;
	}
	
	/**
	 * Get code
	 *
	 * @return string
	 */
	public function getCode()
	{
	    return $this->code;
	}
	
	/**
	 * Returns the name of the color
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}
}
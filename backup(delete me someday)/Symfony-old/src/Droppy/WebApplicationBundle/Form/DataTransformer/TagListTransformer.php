<?php

namespace Droppy\WebApplicationBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Droppy\WebApplicationBundle\Entity\Tag;

class TagListTransformer implements DataTransformerInterface
{
	/**
		ObjectManager $om
	 */
	private $om;
	
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}
	
	/**
	 * Transform tag ArrayCollection to tag string separated by commas
	 *  
	 * @param ArrayCollection $tagList
	 * @return string
	 */
	public function transform($tagList)
	{
		if($tagList == null || empty($tagList))
		{
			return '';
		} 
		$tagString = "";
		$tagNames = array();
		foreach($tagList as $tag)
		{
			$tagNames[] = $tag->getName();  
		} 
		return implode(", ", $tagNames);
	}
	
	/**
	 * Transform tag string separated by commas to tag ArrayCollection
	 * 
	 * @param string $tagString
	 * @return ArrayCollection
	 */
	public function reverseTransform($tagString)
	{
		if(!is_string($tagString))
		{
			return new ArrayCollection();
		}
		$tagNameList = array_filter(array_map("trim", explode(",", $tagString)));
		$registeredTags = 
			new ArrayCollection($this->om->getRepository('DroppyWebApplicationBundle:Tag')->getTagsInArray($tagNameList));
		$unRegisteredTagNameList = array_filter($tagNameList, function($str) use ($registeredTags) {
			return !($registeredTags->exists(function($key, $tag) use ($str) {
					return $tag->getName() == $str;
			}));
		});
		foreach($unRegisteredTagNameList as $unRegisteredTagName)
		{
			$newTag = new Tag;
			$newTag->setName($unRegisteredTagName);
			$registeredTags[] = $newTag; 
		}
		 
		return $registeredTags;
	}
}
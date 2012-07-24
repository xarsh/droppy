<?php

namespace Droppy\WebApplicationBundle\Util;

use Droppy\WebApplicationBundle\Entity\IconSet;
use Droppy\WebApplicationBundle\Entity\Schedule;
use Droppy\WebApplicationBundle\Entity\Event;
use Droppy\WebApplicationBundle\Exception\FileUploadException;
use Doctrine\Common\Persistence\ObjectManager;


class IconUploader
{
	/**
	 * Upload web path
	 */
	protected $webPath;
	
	/**
	 * Upload direcotry path
	 * 
	 * @var string directoryPath
	 */
	protected $directoryPath;
	
	/**
	 * @var ObjectManager $om
	 */
	protected $om;
	
	/**
	 *  @var IconSet $iconSet
	 */
	protected $iconSet;
	
	
	/**
	 * @var string $thumbnailFileName;
	 */
	protected $thumbnailFileName;
	
	/**
	 * @var string $smallIconFileName;
	 */
	protected $smallIconFileName;
	
	/**
	 * @var string $tmpFile;
	 */
	protected $tmpFile;
	
	
	public function __construct($thumbnailFileName, $smallIconFileName, ObjectManager $om)
	{
		$this->thumbnailFileName = $thumbnailFileName;
		$this->smallIconFileName = $smallIconFileName;
		$this->om = $om;
	}
	
	
	/**
	 * Transforms and resize the uploaded file in png file
	 * 
	 * @throws FileUploadException
	 * @return File
	 */
	protected function makeIcon($size, $outputName)
	{
		if($this->resize($size, $this->directoryPath . $outputName) !== 0)
		{
			throw new FileUploadException('Error when resizing icon.');
		}
	}
	

	/**
	 * Resize file with 'convert' command 
	 * 
	 * @param int $size
	 * @param string $outputPath
	 * @return execution code
	 */
	protected function resize($size, $outputPath)
	{
		$inputPath = $this->directoryPath . $this->tmpFile;
		$command = '/usr/bin/convert -sample ' . $size . 'x' . $size . '! ' . $inputPath . ' ' . $outputPath;
		$returnVal = 0;
		passthru($command, $returnVal);
		return $returnVal; 
	}
	
	
	/**
	 * Uploads user's icons
	 * 
	 * @param User $user
	 */
	public function uploadUserIcon(IconSet $iconSet, User $user)
	{
		$id = $user->getId();
		$this->checkId($id);
		$path = $id;
		$this->upload($iconSet, $path);
	}
	
	
	/**
	 * Uploads schedule's icons
	 *  
	 * @param Schedule $schedule
	 */
	public function uploadScheduleIcon(IconSet $iconSet, Schedule $schedule)
	{
		$id = $schedule->getId();
		$this->checkId($id);
		$path = $schedule->getCreator()->getId() . '/' . $id; 
		$this->upload($iconSet, $path);
	}
	
	/**
	 * Uploads event's icons
	 *
	 * @param Event $event
	 */
	public function uploadEventIcon(IconSet $iconSet, Event $event)
	{
		$id = $event->getId();
		$this->checkId($id);
		$path = $event->getCreator()->getId() . '/' . $event->getSchedule()->getId() . '/' . $id;
		$this->upload($iconSet, $path);
	}
	
	/**
	 * Checks if the entity passed has a valid id
	 * 
	 * @param int $id
	 * @throws FileUploadException
	 */
	public function checkId($id)
	{
		if(empty($id))
		{
			throw new FileUploadException("Entity's id is empty. Entity has probably not been persisted yet.");
		}
	}
	
	/**
	 * Sets Icon and create necessary paths
	 * 
	 * @param IconSet $iconSet
	 */
	protected function setIconAndPaths(IconSet $iconSet, $path)
	{
		if($iconSet->file === null)
		{
			return false;
		}
		$this->iconSet = $iconSet;
		$this->tmpFile = "tmp." . $this->iconSet->getFileExtension();
		$this->webPath = $this->getUploadDirectory() . $path;
		$this->directoryPath = $this->getUploadRootDirectory() . $path . '/';
		$this->iconSet->file->move($this->directoryPath, $this->tmpFile);
		return true;
	}
	
	/**
	 * 
	 * Uploads small icon and thumbnail
	 * 
	 * @param IconSet $iconSet
	 * @param string $path
	 */
	protected function upload(IconSet $iconSet, $path)
	{
		if(!$this->setIconAndPaths($iconSet, $path))
		{
			return;
		}
		$thumbnail = $this->makeIcon(48, $this->thumbnailFileName);
		$smallIcon = $this->makeIcon(128, $this->smallIconFileName);
		unlink($this->directoryPath . $this->tmpFile);
		$this->iconSet->updatePaths($this->webPath . '/' . $this->smallIconFileName, 
					$this->webPath . '/' . $this->thumbnailFileName);
		$this->om->flush();
		
	}
	
	protected function getUploadRootDirectory()
	{
		return __DIR__ . '/../../../../web/' . $this->getUploadDirectory();
	}
	
	protected function getUploadDirectory()
	{
		return 'uploads/';
	}

}
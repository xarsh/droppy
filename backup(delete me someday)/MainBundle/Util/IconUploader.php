<?php

namespace Droppy\MainBundle\Util;

use Droppy\MainBundle\Entity\IconSet;
use Droppy\EventBundle\Entity\Event;
use Droppy\UserBundle\Entity\PersonalDatas;
use Droppy\MainBundle\Exception\FileUploadException;

class IconUploader
{
    /**
     * Binaries path
     * 
     * @var string $binPath
     */
    protected $binPath;
    
	/**
	 * Upload web path
	 * 
	 * @var string $webPath
	 */
	protected $webPath;
	
	/**
	 * Upload direcotry path
	 * 
	 * @var string directoryPath
	 */
	protected $directoryPath;
	
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
	
	
	public function __construct($thumbnailFileName, $smallIconFileName, $binPath)
	{
		$this->thumbnailFileName = $thumbnailFileName;
		$this->smallIconFileName = $smallIconFileName;
		$this->binPath = $binPath;
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
		$command = $this->binPath . '/convert -sample ' . $size . 'x' . $size . '! ' . $inputPath . ' ' . $outputPath;
		$returnVal = 0;
		passthru($command, $returnVal);
		return $returnVal; 
	}
	
	
	/**
	 * Uploads user's icons
	 * 
	 * @param PersonalDatas $personalDatas
	 */
	public function uploadUserIcon(PersonalDatas $personalDatas)
	{
		$id = $personalDatas->getId();
		$this->checkId($id);
		$path = $id;
		$this->upload($personalDatas->getIconSet(), $path);
	}
	
	/**
	 * Uploads event's icons
	 *
	 * @param Event $event
	 */
	public function uploadEventIcon(Event $event)
	{
		$id = $event->getId();
		$this->checkId($id);
		$path = $event->getCreator()->getId() . '/' . $id;
		$this->upload($event->getIconSet(), $path);
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
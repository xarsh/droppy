<?php

namespace Droppy\MainBundle\Util;

use Droppy\MainBundle\Entity\IconSet;
use Droppy\EventBundle\Entity\Event;
use Droppy\UserBundle\Entity\PersonalDatas;
use Droppy\MainBundle\Exception\FileUploadException;

require_once "sdk-1.5.8/sdk.class.php";
require_once "sdk-1.5.8/config.inc.php";

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
	
    protected $s3;
	
	public function __construct($thumbnailFileName, $smallIconFileName, $binPath)
	{
		$this->thumbnailFileName = $thumbnailFileName;
		$this->smallIconFileName = $smallIconFileName;
		$this->binPath = $binPath;
        $this->s3 = new \AmazonS3();
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

		$command = $this->binPath . '/convert "' . $inputPath . '" -thumbnail x' . $size . ' -resize "' . $size . 'x<" -resize x' . $size . ' -gravity center -crop ' . $size . 'x' . $size . '+0+0 +repage ' . $outputPath;

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

		$thumbnail = $this->makeIcon(48, $this->thumbnailFileName . '.png');
		$smallIcon = $this->makeIcon(128, $this->smallIconFileName . '.png');
		//unlink($this->directoryPath . $this->tmpFile);

        $uTime = time();
        $this->iconSet->updatePaths(
            'droppy-image/user/' . $path . '/' . $this->smallIconFileName . '_' . $uTime . '.png',
            'droppy-image/user/' . $path . '/' . $this->thumbnailFileName . '_' . $uTime . '.png'
        );

		
        foreach(glob($this->getUploadRootDirectory() . $path . "/*.png") as $pngPath)
        {
            $pathParts = pathinfo($pngPath);
            $filename = 'user/'. $path . '/' . $pathParts['filename'] . '_' . $uTime . '.png';

            $this->s3->create_object("droppy-image", $filename, array(
                'fileUpload' => $pngPath,
                'contentType' => 'image/png',
                'acl' => \AmazonS3::ACL_PUBLIC
            ));
        }
        system('rm -rf ' . $this->getUploadRootDirectory() . '*');
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

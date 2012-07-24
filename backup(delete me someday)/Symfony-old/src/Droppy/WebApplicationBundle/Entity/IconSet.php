<?php

namespace Droppy\WebApplicationBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\WebApplicationBundle\Entity\IconSet
 * 
 * @ORM\Table(name="icon_set")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class IconSet
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
     * @var string $thumbnailPath
     * 
     * @ORM\Column(name="thumbnail_path", type="string", length=255, nullable=true)
     */
    private $thumbnailPath;
    
    /**
     * @var string $smallIconPath
     * 
     * @ORM\Column(name="small_icon_path", type="string", length=255, nullable=true) 
     */
    private $smallIconPath;
    
    /**
     * @var boolean $uploaded
     * 
     * @ORM\Column(name="uploaded", type="boolean", nullable=false)
     */
    private $uploaded;
    
    /**
     * Icon file
     * 
     * @var File $file
     */
    public $file;
    
    public function __construct()
    {
    	$this->uploaded = false;
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
     * Set id
     * 
     * @param  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get thumbnailPath
     * 
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->thumbnailPath;
    }

    /**
     * Set thumbnailPath
     * 
     * @param string $thumbnailPath
     */
    public function setThumbnailPath($thumbnailPath)
    {
        $this->thumbnailPath = $thumbnailPath;
    }
    
    /**
    * Get smallIconPath
    *
    * @return string
    */
    public function getSmallIconPath()
    {
    	return $this->smallIconPath;
    }
    
    /**
     * Set smallIconPath
     *
     * @param string $smallIconPath
     */
    public function setSmallIconPath($smallIconPath)
    {
    	$this->smallIconPath = $smallIconPath;
    }
    
    /**
    * Returns absolute path for thumbnail
    *
    * @return string
    */
    public function getThumbnailAbsolutePath()
    {
    	return ($this->thumbnailPath === null) ? null : ($this->getWebRootDir() . '/' . $this->getThumbnailPath());
    }
    
    /**
    * Returns absolute path for small icon
    *
    * @return string
    */
    public function getSmallIconAbsolutePath()
    {
    	return ($this->smallIconPath === null) ? null : ($this->getWebRootDir() . '/' . $this->getSmallIconPath());
    }
    
    
    /**
     * called after image resizing is completed
     * 
     * @param string $smallIconPath
     * @param string $thumbnailPath
     */
    public function updatePaths($smallIconPath, $thumbnailPath)
    {
    	$this->smallIconPath = $smallIconPath;
    	$this->thumbnailPath = $thumbnailPath;
    	$this->uploaded = true;
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
    	if($thumbnailFile = $this->getThumbnailAbsolutePath())
    	{
    		unlink($thumbnailFile);
    	}
    	if($smallIconFile = $this->getSmallIconAbsolutePath())
    	{
    		unlink($smallIconFile);
    	}
    }
    
    
    /**
     * set uploaded status
     * 
     * @param boolean $uploaded
     */
    public function setUploaded($uploaded)
    {
    	$this->uploaded = $uploaded;
    }

    /**
     * returns upload status
     *  
     * @return boolean 
     */
    public function isUploaded()
    {
    	return $this->uploaded;
    }
    
    /**
     * Returns web root directory
     * 
     * @return string
     */
    protected function getWebRootDir()
    {
        return __DIR__ . '/../../../../web/';
    }
    
    public function getFileExtension()
    {
    	return $this->file === null ? "" : $this->file->guessExtension();
    }

}

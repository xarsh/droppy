<?php

namespace Droppy\MainBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;

class MainBundleExtension extends \Twig_Extension
{
    protected $s3path;
    
    public function __construct($s3path)
    {
        $this->s3path = $s3path;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
                's3asset' => new \Twig_Function_Method($this, 's3asset')
        );
    }
     
    /**
     * Gets the path for s3 storage
     *
     * @param string $path
     * @return string
     */
    public function s3asset($path)
    {
        return $this->s3path . '/' . $path;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'main_bundle';
    }
}
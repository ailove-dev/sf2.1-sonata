<?php
namespace Application\Sonata\MediaBundle\Twig\Extension;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\DependencyInjection\ContainerInterface;

use \Twig_Extension;

/**
 * MediaPathExtension 
 */
class MediaPathExtension extends Twig_Extension
{

    protected $mediaService;
    protected $ressources = array();
    protected $mediaManager;
    protected $container;

    /**
     * Construct
     * @param Pool                  $mediaService Media Service
     * @param MediaManagerInterface $mediaManager Media manager
     * @param ContainerInterface    $container    Container
     */
    public function __construct(Pool $mediaService, MediaManagerInterface $mediaManager, ContainerInterface $container)
    {
        $this->mediaService = $mediaService;
        $this->mediaManager = $mediaManager;
        $this->container = $container;
    }

    /**
     * Get functions
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'media_path' => new \Twig_Function_Method($this, 'path'),
        );
    }

    /**
     * Get media
     * @param mixed $media
     * 
     * @return null|\Sonata\MediaBundle\Model\MediaInterface
     */
    private function getMedia($media)
    {
        if ($media instanceof MediaInterface) {
            return $media;
        }

        if (strlen($media) > 0) {
            $media = $this->mediaManager->findOneBy(array(
                'id' => $media
            ));
        }

        return $media;
    }

    /**
     * Method for get path to media
     * @param \Sonata\MediaBundle\Model\MediaInterface $media    Media
     * @param string                                   $format   Format
     * @param bool                                     $absolute Absolute path
     * 
     * @return string
     */
    public function path($media = null, $format = 'admin', $absolute = false)
    {
        $media = $this->getMedia($media);

        if (!$media) {
             return '';
        }

        $provider = $this->getMediaService()
           ->getProvider($media->getProviderName());

        $format = $provider->getFormatName($media, $format);

        $relativePublicUrl = $provider->generatePublicUrl($media, $format);

        if (true === $absolute) {
            $request = $this->container->get('request');
            $hostname = $request->getHost();
            $scheme = $request->getScheme();

            return $scheme.'://'.$hostname.$relativePublicUrl;
        }

        return $relativePublicUrl;
    }

    /**
     * @return \Sonata\MediaBundle\Provider\Pool
     */
    public function getMediaService()
    {
        return $this->mediaService;
    }

    /**
     * For a service we need a name/
     * @return string
     */
    public function getName()
    {
        return 'media_path';
    }
}
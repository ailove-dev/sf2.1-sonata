<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\MediaBundle\Thumbnail;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Thumbnail
 */
class LiipImagineThumbnail implements ThumbnailInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * Construct
     * @param RouterInterface $router 
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }


    /**
     * Generate public url
     * @param Sonata\MediaBundle\Provider\MediaProviderInterface $provider Provider
     * @param Sonata\MediaBundle\Model\MediaInterface            $media    Media
     * @param string                                             $format   Format
     * 
     * @return mixed
     */
    public function generatePublicUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        $ext = $media->getExtension() ? $media->getExtension() : 'jpg';

        if ($format == 'reference') {
            $path = $provider->getReferenceImage($media);
        } else {

            $path = $this->router->generate(
                sprintf('_imagine_%s', $format),
                array(
                    'path' => sprintf('%s/%s_%s.%s', $provider->generatePath($media), $media->getId(), $format, $ext),
                    'filter' => $format)
            );
        }

        $baseUrl = $this->router->getContext()->getBaseUrl();
        $cdnPath = $provider->getCdnPath('', $media->getCdnIsFlushable());
        $path = str_replace($baseUrl . $cdnPath, '', $path);

        return $path;
//        return $provider->getCdnPath($path, $media->getCdnIsFlushable());
    }

    /**
     * Generate private url
     * @param Sonata\MediaBundle\Provider\MediaProviderInterface $provider Provider
     * @param Sonata\MediaBundle\Model\MediaInterface            $media    Media
     * @param string                                             $format   Format
     * 
     * @return mixed
     */
    public function generatePrivateUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        if ($format != 'reference') {
            throw new \RuntimeException('No private url for LiipImagineThumbnail');
        }

        $path = $provider->getReferenceImage($media);

        return $path;
    }

    /**
     * Generate method
     * @param Sonata\MediaBundle\Provider\MediaProviderInterface $provider Provider
     * @param Sonata\MediaBundle\Model\MediaInterface            $media    Media
     * 
     * @return mixed
     */
    public function generate(MediaProviderInterface $provider, MediaInterface $media)
    {
        // nothing to generate, as generated on demand
        return;
    }

    /**
     * Delete
     * @param Sonata\MediaBundle\Provider\MediaProviderInterface $provider Provider
     * @param Sonata\MediaBundle\Model\MediaInterface            $media    Media
     */
    public function delete(MediaProviderInterface $provider, MediaInterface $media)
    {
        // feature not available
        return;
    }
}
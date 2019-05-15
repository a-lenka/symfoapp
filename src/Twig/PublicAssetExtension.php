<?php

namespace App\Twig;

use App\Service\FileUploader;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This Twig extension adds a new `getUploadedAssetPath()` function
 * to easily get path to public uploads folder
 * @see https://symfonycasts.com/screencast/symfony-uploads/public-path#play
 *
 * Class PublicAssetExtension
 * @package App\Twig
 */
class PublicAssetExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    private $container;


    /**
     * AssetsExtension constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return [
            FileUploader::class,
        ];
    }


    /**
     * @return array
     */
    final public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'uploaded_asset',
                [$this, 'getUploadedAssetPath']
            ),
        ];
    }


    /**
     * @param string $fileName
     *
     * @return string
     */
    final public function getUploadedAssetPath(string $fileName): string
    {
        return $this->container
            ->get(FileUploader::class)
            ->getPublicPath($fileName);
    }
}
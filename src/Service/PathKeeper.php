<?php

namespace App\Service;

use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class PathKeeper
 * @package App\Service
 */
class PathKeeper
{
    /** KernelInterface $appKernel */
    private $appKernel;

    /** @var RequestStackContext */
    private $requestStackContext;

    /** @const string PUBLIC_DIR */
    public const PUBLIC_DIR = 'public';

    /** @const string BUILD_IMAGES_DIR */
    public const BUILD_IMAGES_DIR = 'build/images';

    /** @const string PUBLIC_UPLOADS_DIR */
    public const PUBLIC_UPLOADS_DIR = 'uploads';

    /** @var string UPLOADED_AVATARS_DIR */
    public const UPLOADED_AVATARS_DIR = 'avatars';

    /** @var string UPLOADED_ICONS_DIR */
    public const UPLOADED_ICONS_DIR = 'icons';

    /** @const string ANONYMOUS_ICON_NAME */
    public const ANONYMOUS_ICON_NAME = "anonymous.png";


    /**
     * FileUploader constructor
     *
     * @param KernelInterface     $appKernel
     * @param RequestStackContext $requestStackContext
     */
    public function __construct(KernelInterface $appKernel, RequestStackContext $requestStackContext)
    {
        $this->appKernel           = $appKernel;
        $this->requestStackContext = $requestStackContext;
    }


    /**
     * Returns absolute path to project root folder
     *
     * @returns string
     */
    final public function getProjectSystemPath(): string
    {
        return $this->appKernel->getProjectDir();
    }


    /**
     * Returns relative path to public uploads folder
     *
     * @return string "/uploads"
     */
    final public function getPublicUploadsPath(): string
    {
        return $this->requestStackContext
                ->getBasePath().'/'.self::PUBLIC_UPLOADS_DIR;
    }


    /**
     * Returns absolute path to public folder
     *
     * @return string
     */
    final public function getPublicSystemPath(): string
    {
        return $this->getProjectSystemPath().'/'.self::PUBLIC_DIR;
    }


    /**
     * Returns system path to public uploads folder
     *
     * @return string
     */
    final public function getPublicUploadsSystemPath(): string
    {
        return $this->getPublicSystemPath().'/'.self::PUBLIC_UPLOADS_DIR;
    }
}

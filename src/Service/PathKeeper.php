<?php

namespace App\Service;

use Symfony\Component\Asset\Context\RequestStackContext;

/**
 * Class PathKeeper
 * @package App\Service
 */
class PathKeeper
{
    /** @var RequestStackContext */
    private $requestStackContext;

    /** @const string PUBLIC_UPLOADS_DIR */
    public const PUBLIC_UPLOADS_DIR = 'uploads';


    /**
     * FileUploader constructor
     *
     * @param RequestStackContext $requestStackContext
     */
    public function __construct(RequestStackContext $requestStackContext)
    {
        $this->requestStackContext = $requestStackContext;
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
}
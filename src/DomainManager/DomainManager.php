<?php

namespace App\DomainManager;

use App\Service\FileUploader;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DomainManager
 * @package App\DomainManager
 */
class DomainManager
{
    /** @var ObjectManager */
    protected $appEntityManager;

    /** @var FileUploader */
    protected $fileUploader;

    /**
     * DomainManager constructor
     *
     * @param ObjectManager $objectManager
     * @param FileUploader  $fileUploader
     */
    public function __construct(ObjectManager $objectManager, FileUploader $fileUploader)
    {
        $this->appEntityManager = $objectManager;
        $this->fileUploader     = $fileUploader;
    }
}

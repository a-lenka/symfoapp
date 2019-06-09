<?php

namespace App\Form\Handlers;

use App\Service\FileUploader;

/**
 * Class FormHandler
 * @package App\Form\Handlers
 */
class FormHandler
{
    /** @var FileUploader */
    protected $fileUploader;

    /**
     * FormHandler constructor
     *
     * @param FileUploader $fileUploader
     */
    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }
}

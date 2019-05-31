<?php

namespace App\Service\Forms;

use App\Service\FileUploader;

/**
 * Class FormHandler
 * @package App\Service\Forms
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

<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package App\Service
 */
class FileUploader
{
    /** @var string $uploadsPath */
    private $uploadsPath;

    /** @var string anonymous */
    private $anonymous;


    /**
     * FileUploader constructor
     *
     * @param string $uploadsPath
     * @param string $anonymous
     */
    public function __construct(string $uploadsPath, string $anonymous)
    {
        $this->uploadsPath = $uploadsPath;
        $this->anonymous   = $anonymous;
    }


    /**
     * @param UploadedFile|null $uploadedFile
     *
     * @return string
     */
    public function uploadUserAvatar(?UploadedFile $uploadedFile): string
    {
        if ($uploadedFile) {
            // TODO: $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid(mt_rand().'.'.$uploadedFile->guessExtension();
            $newFileName = uniqid('', false).'.'.$uploadedFile->guessExtension();
            $destination = $this->uploadsPath.'/avatars';
            $uploadedFile->move($destination, $newFileName);

            return $newFileName;
        }

        return $this->anonymous;
    }
}

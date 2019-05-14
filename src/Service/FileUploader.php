<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package App\Service
 */
class FileUploader
{
    /** @var FilesystemInterface */
    private $filesystem;

    /** @const string AVATARS_DIR */
    public const AVATARS_DIR = 'avatars';


    /**
     * FileUploader constructor
     *
     * @param FilesystemInterface $publicUploadFilesystem
     */
    public function __construct(
        FilesystemInterface $publicUploadFilesystem
    ) {
        $this->filesystem  = $publicUploadFilesystem;
    }


    /**
     * @param UploadedFile|null $uploadedFile
     * @param string|null       $existingFilename
     *
     * @return string|null
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function uploadUserAvatar(?UploadedFile $uploadedFile, ?string $existingFilename): string
    {
        if (!$uploadedFile) {
            throw new FileNotFoundException('The User avatar was not uploaded');
        }

        // Old filename
        if ($uploadedFile instanceof UploadedFile) {
            $oldFileName = $uploadedFile->getClientOriginalName();
        } else {
            $oldFileName = $uploadedFile->getFileName();
        }

        $trimmed = pathinfo($oldFileName, PATHINFO_FILENAME);

        // New filename
        $unique      = uniqid('', false).'.'.$uploadedFile->guessExtension();
        $newFileName = Urlizer::urlize($trimmed).'_'.$unique;

        // Move
        $stream = fopen($uploadedFile->getPathname(), 'r');
        $this->filesystem->writeStream(
            self::AVATARS_DIR.'/'.$newFileName,
            $stream
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($existingFilename) {
            $this->filesystem->delete(self::AVATARS_DIR.'/'.$existingFilename);
        }

        return $newFileName;
    }
}

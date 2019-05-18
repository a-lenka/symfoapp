<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package App\Service
 */
class FileUploader
{
    /** @var PathKeeper */
    private $pathKeeper;

    /** @var FilesystemInterface */
    private $filesystem;

    /** @const string AVATARS_DIR */
    public const AVATARS_DIR = 'avatars';

    /** @const string ICONS_DIR */
    public const ICONS_DIR = 'icons';


    /**
     * FileUploader constructor
     * @param PathKeeper          $pathKeeper
     * @param FilesystemInterface $publicUploadFilesystem
     */
    public function __construct(
        PathKeeper $pathKeeper,
        FilesystemInterface $publicUploadFilesystem
    ) {
        $this->pathKeeper = $pathKeeper;
        $this->filesystem = $publicUploadFilesystem;
    }


    /**
     * @param string $avatarName
     *
     * @throws FileNotFoundException
     */
    final public function deleteAvatar(?string $avatarName): void
    {
        if ($avatarName) {
            $this->filesystem->delete('/'.$avatarName);
        }
    }


    /**
     * Rename the uploaded file, move it to the given directory in public uploads folder,
     * then delete existing file, which is not used anywhere else
     *
     * @param string      $dirname
     * @param File|null   $uploadedFile
     * @param string|null $existingFilename
     *
     * @return string|null
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function uploadEntityIcon(string $dirname, ?File $uploadedFile, ?string $existingFilename): string
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
            $dirname.'/'.$newFileName,
            $stream
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        $this->deleteAvatar($existingFilename);

        return $newFileName;
    }
}

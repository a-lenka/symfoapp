<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package App\Service
 */
class FileUploader
{
    /** @var RequestStackContext */
    private $requestStackContext;

    /** @var FilesystemInterface */
    private $filesystem;

    /** @const string AVATARS_DIR */
    public const AVATARS_DIR = 'avatars';

    /** @const string ICONS_DIR */
    public const ICONS_DIR = 'icons';


    /**
     * FileUploader constructor
     *
     * @param RequestStackContext $requestStackContext
     * @param FilesystemInterface $publicUploadFilesystem
     */
    public function __construct(
        RequestStackContext $requestStackContext,
        FilesystemInterface $publicUploadFilesystem
    ) {
        $this->requestStackContext = $requestStackContext;
        $this->filesystem          = $publicUploadFilesystem;
    }


    /**
     * @param string $fileName
     *
     * @return string
     */
    final public function getPublicPath(string $fileName): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$fileName;
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
     * @param File|null   $uploadedFile
     * @param string|null $existingFilename
     *
     * @return string|null
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function uploadUserAvatar(?File $uploadedFile, ?string $existingFilename): string
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

        $this->deleteAvatar($existingFilename);

        return $newFileName;
    }
}

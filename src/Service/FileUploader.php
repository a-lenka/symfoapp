<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package App\Service
 */
class FileUploader
{
    /** @var FilesystemInterface */
    private $filesystem;

    /** @var PathKeeper */
    private $pathKeeper;


    /**
     * FileUploader constructor
     *
     * @param FilesystemInterface $publicUploadFilesystem
     * @param PathKeeper          $pathKeeper
     */
    public function __construct(FilesystemInterface $publicUploadFilesystem, PathKeeper $pathKeeper) {
        $this->filesystem = $publicUploadFilesystem;
        $this->pathKeeper = $pathKeeper;
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
     * Delete all the files from the given directory
     *
     * @param string $path - System path to directory where all the files will be deleted
     */
    final public function clearDir(string $path): void
    {
        $files = glob($path.'/*');

        foreach($files as $file) {
            unlink($file);
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
    final public function uploadEntityIcon(
        string  $dirname,
        ?File   $uploadedFile,
        ?string $existingFilename
    ): string {
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


    /**
     * Get `anonymous` icon from the public uploads directory
     * replace it to temporary directory and then uploads it as usually
     *
     * @return string|null           - New file name
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function uploadAnonymous(): string
    {
        $fs = new Filesystem();

        $sourceFile = $this->pathKeeper->getPublicSystemPath().
            '/'.$this->pathKeeper::BUILD_IMAGES_DIR.
            '/'.$this->pathKeeper::ANONYMOUS_ICON_NAME;

        $targetFile = sys_get_temp_dir().'/'.$this->pathKeeper::ANONYMOUS_ICON_NAME;

        $fs->copy($sourceFile, $targetFile, true);

        return $this->uploadEntityIcon(
            $this->pathKeeper::UPLOADED_AVATARS_DIR,
            new File($targetFile),
            null
        );
    }
}

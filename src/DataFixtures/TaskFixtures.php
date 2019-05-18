<?php

namespace App\DataFixtures;

use App\Service\FileUploader;
use App\Service\PathKeeper;
use DateTime;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Task;
use Exception;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class TaskFixtures
 * @package App\DataFixtures
 */
class TaskFixtures extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{

    /** @var FileUploader */
    private $fileUploader;

    /** @var PathKeeper */
    private $pathKeeper;

    /** @const string FIXTURE_ICONS_DIR */
    private const FIXTURE_ICONS_DIR = 'images/icons';


    /**
     * @param FileUploader $uploader
     * @param PathKeeper   $pathKeeper
     */
    public function __construct(FileUploader $uploader, PathKeeper $pathKeeper)
    {
        $this->fileUploader = $uploader;
        $this->pathKeeper   = $pathKeeper;
    }


    /**
     * Copy file from `DataFixtures/images/icons` folder to temporary directory
     * Then pass it from there to File Uploader.
     * File Uploader rename file and put it to `public/uploads/icons` folder.
     * So fixture images has their own folder and will not be replaced during uploading.
     *
     * @param string $filename
     * @return string
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    private function uploadDummyIcon(string $filename): string
    {
        $fs = new Filesystem();

        $sourceFile = __DIR__.'/'.self::FIXTURE_ICONS_DIR.'/'.$filename;
        $targetFile = sys_get_temp_dir().'/'.$filename;

        $fs->copy($sourceFile, $targetFile, true);

        return $this->fileUploader->uploadEntityIcon(
            PathKeeper::UPLOADED_ICONS_DIR, new File($targetFile), null
        );
    }


    /**
     * Loads Task Fixtures into Database
     *
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $path = $this->pathKeeper->getPublicUploadsSystemPath().'/icons';
        $this->fileUploader->clearDir($path);

        // Admin Tasks
        $firstAdminTask = new Task();
        $firstAdminTask->setIcon($this->uploadDummyIcon('art.png'));
        $firstAdminTask->setTitle('First Admin task');
        $firstAdminTask->setDateDeadline(new DateTime('2000-09-30 20:00:00'));
        $firstAdminTask->setState('In progress');
        $firstAdminTask->setOwner($this->getReference('admin'));

        $manager->persist($firstAdminTask);

        $secondAdminTask = new Task();
        $secondAdminTask->setIcon($this->uploadDummyIcon('bookshelf.png'));
        $secondAdminTask->setTitle('Second Admin task');
        $secondAdminTask->setDateDeadline(new DateTime('2000-10-30 20:00:00'));
        $secondAdminTask->setState('In progress');
        $secondAdminTask->setOwner($this->getReference('admin'));

        $manager->persist($secondAdminTask);


        // User Tasks
        $firstUserTask = new Task();
        $firstUserTask->setIcon($this->uploadDummyIcon('briefcase.png'));
        $firstUserTask->setTitle('First User task');
        $firstUserTask->setDateDeadline(new DateTime('2001-09-30 20:00:00'));
        $firstUserTask->setState('In progress');
        $firstUserTask->setOwner($this->getReference('user'));

        $manager->persist($firstUserTask);

        $secondUserTask = new Task();
        $secondUserTask->setIcon($this->uploadDummyIcon('brightness.png'));
        $secondUserTask->setTitle('Second User task');
        $secondUserTask->setDateDeadline(new DateTime('2001-10-30 20:00:00'));
        $secondUserTask->setState('In progress');
        $secondUserTask->setOwner($this->getReference('user'));

        $manager->persist($secondUserTask);

        $thirdUserTask = new Task();
        $thirdUserTask->setIcon($this->uploadDummyIcon('brush-pencil.png'));
        $thirdUserTask->setTitle('Third User task');
        $thirdUserTask->setDateDeadline(new DateTime('2001-10-30 20:00:00'));
        $thirdUserTask->setState('In progress');
        $thirdUserTask->setOwner($this->getReference('user'));

        $manager->persist($thirdUserTask);


        $manager->flush();
    }


    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 2;
    }
}

<?php

namespace App\DataFixtures;

use App\Service\FileUploader;
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
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class TaskFixtures
 * @package App\DataFixtures
 */
class TaskFixtures extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{

    /** @var FileUploader */
    private $fileUploader;

    /** @const string ICONS_DIR - Avatars fixtures directory */
    private const ICONS_DIR = '/images/icons/';

    /** KernelInterface $appKernel */
    private $appKernel;


    /**
     * @param KernelInterface $appKernel
     * @param FileUploader    $uploader
     */
    public function __construct(KernelInterface $appKernel, FileUploader $uploader)
    {
        $this->fileUploader = $uploader;
        $this->appKernel    = $appKernel;
    }


    /**
     * Clear directory before fixtures loading
     */
    private function clearDir(): void
    {
        $files = glob($this->appKernel->getProjectDir().'/public/uploads/icons/*');

        foreach($files as $file) {
            unlink($file);
        }
    }


    /**
     * @param string $filename
     * @return string
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    private function uploadDummyIcons(string $filename): string
    {
        $fs = new Filesystem();

        $sourceFile = __DIR__.self::ICONS_DIR.$filename;
        $targetFile = sys_get_temp_dir().'/'.$filename;

        $fs->copy($sourceFile, $targetFile, true);

        return $this->fileUploader->uploadTaskIcon(
            new File($targetFile), null
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
        $this->clearDir();

        // Admin Tasks
        $firstAdminTask = new Task();
        $firstAdminTask->setIcon($this->uploadDummyIcons('art.png'));
        $firstAdminTask->setTitle('First Admin task');
        $firstAdminTask->setDateDeadline(new DateTime('2000-09-30 20:00:00'));
        $firstAdminTask->setState('In progress');
        $firstAdminTask->setOwner($this->getReference('admin'));

        $manager->persist($firstAdminTask);

        $secondAdminTask = new Task();
        $secondAdminTask->setIcon($this->uploadDummyIcons('bookshelf.png'));
        $secondAdminTask->setTitle('Second Admin task');
        $secondAdminTask->setDateDeadline(new DateTime('2000-10-30 20:00:00'));
        $secondAdminTask->setState('In progress');
        $secondAdminTask->setOwner($this->getReference('admin'));

        $manager->persist($secondAdminTask);


        // User Tasks
        $firstUserTask = new Task();
        $firstUserTask->setIcon($this->uploadDummyIcons('briefcase.png'));
        $firstUserTask->setTitle('First User task');
        $firstUserTask->setDateDeadline(new DateTime('2001-09-30 20:00:00'));
        $firstUserTask->setState('In progress');
        $firstUserTask->setOwner($this->getReference('user'));

        $manager->persist($firstUserTask);

        $secondUserTask = new Task();
        $secondUserTask->setIcon($this->uploadDummyIcons('brightness.png'));
        $secondUserTask->setTitle('Second User task');
        $secondUserTask->setDateDeadline(new DateTime('2001-10-30 20:00:00'));
        $secondUserTask->setState('In progress');
        $secondUserTask->setOwner($this->getReference('user'));

        $manager->persist($secondUserTask);

        $thirdUserTask = new Task();
        $thirdUserTask->setIcon($this->uploadDummyIcons('brush-pencil.png'));
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

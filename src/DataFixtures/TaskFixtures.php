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
    final public function load(ObjectManager $manager): void
    {
        $path = $this->pathKeeper->getPublicUploadsSystemPath().'/icons';
        $this->fileUploader->clearDir($path);


        // Admin Tasks
        $firstAdminTask = new Task();
        $firstAdminTask->setIcon($this->uploadDummyIcon('colorwheel.png'));
        $firstAdminTask->setTitle('First Admin task');
        $firstAdminTask->setDateDeadline(new DateTime('2019-09-30 20:00:00'));
        $firstAdminTask->setState('In progress');
        $firstAdminTask->setOwner($this->getReference('admin'));

        $manager->persist($firstAdminTask);

        $secondAdminTask = new Task();
        $secondAdminTask->setIcon($this->uploadDummyIcon('summer.png'));
        $secondAdminTask->setTitle('Second Admin task');
        $secondAdminTask->setDateDeadline(new DateTime('2019-10-30 20:00:00'));
        $secondAdminTask->setState('In progress');
        $secondAdminTask->setOwner($this->getReference('admin'));

        $manager->persist($secondAdminTask);


        // User Tasks
        $firstUserTask = new Task();
        $firstUserTask->setIcon($this->uploadDummyIcon('volume.png'));
        $firstUserTask->setTitle('First User task');
        $firstUserTask->setDateDeadline(new DateTime('2019-09-30 20:00:00'));
        $firstUserTask->setState('In progress');
        $firstUserTask->setOwner($this->getReference('user'));

        $manager->persist($firstUserTask);

        $secondUserTask = new Task();
        $secondUserTask->setIcon($this->uploadDummyIcon('genius.png'));
        $secondUserTask->setTitle('Second User task');
        $secondUserTask->setDateDeadline(new DateTime('2019-10-30 20:00:00'));
        $secondUserTask->setState('In progress');
        $secondUserTask->setOwner($this->getReference('user'));

        $manager->persist($secondUserTask);

        $thirdUserTask = new Task();
        $thirdUserTask->setIcon($this->uploadDummyIcon('starfish.png'));
        $thirdUserTask->setTitle('Third User task');
        $thirdUserTask->setDateDeadline(new DateTime('2019-10-30 20:00:00'));
        $thirdUserTask->setState('In progress');
        $thirdUserTask->setOwner($this->getReference('user'));

        $manager->persist($thirdUserTask);


        // Housewife tasks
        $goShopping = new Task();
        $goShopping->setIcon($this->uploadDummyIcon('cart.png'));
        $goShopping->setTitle('Go shopping');
        $goShopping->setDateDeadline((new DateTime('now'))->modify('+1 hour'));
        $goShopping->setState('In progress');
        $goShopping->setOwner($this->getReference('housewife'));

        $manager->persist($goShopping);

        $cleanTheRoom = new Task();
        $cleanTheRoom->setIcon($this->uploadDummyIcon('home.png'));
        $cleanTheRoom->setTitle('Clean the room');
        $cleanTheRoom->setDateDeadline((new DateTime('now'))->modify('-1 day'));
        $cleanTheRoom->setState('In progress');
        $cleanTheRoom->setOwner($this->getReference('housewife'));

        $manager->persist($cleanTheRoom);

        $trackFinances = new Task();
        $trackFinances->setIcon($this->uploadDummyIcon('money.png'));
        $trackFinances->setTitle('Keep your finances on track');
        $trackFinances->setDateDeadline(new DateTime('midnight'));
        $trackFinances->setState('In progress');
        $trackFinances->setOwner($this->getReference('housewife'));

        $manager->persist($trackFinances);


        // Student tasks
        $readTheBook = new Task();
        $readTheBook->setIcon($this->uploadDummyIcon('bookshelf.png'));
        $readTheBook->setTitle('Read the book');
        $readTheBook->setDateDeadline((new DateTime('now'))->modify('-1 week'));
        $readTheBook->setState('In progress');
        $readTheBook->setOwner($this->getReference('student'));

        $manager->persist($readTheBook);

        $makeCalls = new Task();
        $makeCalls->setIcon($this->uploadDummyIcon('phone.png'));
        $makeCalls->setTitle('Make personal calls');
        $makeCalls->setDateDeadline((new DateTime('now'))->modify('+1 month'));
        $makeCalls->setState('In progress');
        $makeCalls->setOwner($this->getReference('student'));

        $manager->persist($makeCalls);

        $sendEmail = new Task();
        $sendEmail->setIcon($this->uploadDummyIcon('email.png'));
        $sendEmail->setTitle('Send personal emails (when necessary)');
        $sendEmail->setDateDeadline(new DateTime('yesterday noon'));
        $sendEmail->setState('In progress');
        $sendEmail->setOwner($this->getReference('student'));

        $manager->persist($sendEmail);

        $getExercises = new Task();
        $getExercises->setIcon($this->uploadDummyIcon('star.png'));
        $getExercises->setTitle('Get some exercises');
        $getExercises->setDateDeadline(new DateTime('first day of January 2020'));
        $getExercises->setState('In progress');
        $getExercises->setOwner($this->getReference('student'));

        $manager->persist($getExercises);

        $winTheMillion = new Task();
        $winTheMillion->setIcon($this->uploadDummyIcon('trophy.png'));
        $winTheMillion->setTitle('Win the million');
        $winTheMillion->setDateDeadline(new DateTime('last sat of next month'));
        $winTheMillion->setState('In progress');
        $winTheMillion->setOwner($this->getReference('student'));

        $manager->persist($winTheMillion);

        $prepareGoals = new Task();
        $prepareGoals->setIcon($this->uploadDummyIcon('check.png'));
        $prepareGoals->setTitle('Prepare goals for the next day');
        $prepareGoals->setDateDeadline(new DateTime('Monday next week'));
        $prepareGoals->setState('In progress');
        $prepareGoals->setOwner($this->getReference('student'));

        $manager->persist($prepareGoals);

        $prioritizeTasks = new Task();
        $prioritizeTasks->setIcon($this->uploadDummyIcon('barchart.png'));
        $prioritizeTasks->setTitle('Prioritize your tasks');
        $prioritizeTasks->setDateDeadline(new DateTime('first day of this month'));
        $prioritizeTasks->setState('In progress');
        $prioritizeTasks->setOwner($this->getReference('student'));

        $manager->persist($prioritizeTasks);

        $prioritizeEmails = new Task();
        $prioritizeEmails->setIcon($this->uploadDummyIcon('pin.png'));
        $prioritizeEmails->setTitle('Prioritize your emails');
        $prioritizeEmails->setDateDeadline(new DateTime('last day of +1 month'));
        $prioritizeEmails->setState('In progress');
        $prioritizeEmails->setOwner($this->getReference('student'));

        $manager->persist($prioritizeEmails);

        $reviewIdeas = new Task();
        $reviewIdeas->setIcon($this->uploadDummyIcon('lightbulb.png'));
        $reviewIdeas->setTitle('Review all your ideas');
        $reviewIdeas->setDateDeadline(new DateTime('tomorrow'));
        $reviewIdeas->setState('In progress');
        $reviewIdeas->setOwner($this->getReference('student'));

        $manager->persist($reviewIdeas);

        $readTheNews = new Task();
        $readTheNews->setIcon($this->uploadDummyIcon('news.png'));
        $readTheNews->setTitle('Read the news');
        $readTheNews->setDateDeadline(new DateTime('+3 days'));
        $readTheNews->setState('In progress');
        $readTheNews->setOwner($this->getReference('student'));

        $manager->persist($readTheNews);

        $writeArticle = new Task();
        $writeArticle->setIcon($this->uploadDummyIcon('compose.png'));
        $writeArticle->setTitle('Write the article');
        $writeArticle->setDateDeadline(new DateTime('+3 months'));
        $writeArticle->setState('In progress');
        $writeArticle->setOwner($this->getReference('student'));

        $manager->persist($writeArticle);


        $manager->flush();
    }


    /**
     * @return int
     */
    final public function getOrder(): int
    {
        return 2;
    }
}

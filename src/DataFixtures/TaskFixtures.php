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

        $cleanTheDesktop = new Task();
        $cleanTheDesktop->setIcon($this->uploadDummyIcon('recycle.png'));
        $cleanTheDesktop->setTitle('Clean up the desktop');
        $cleanTheDesktop->setDateDeadline(new DateTime('tuesday next week'));
        $cleanTheDesktop->setState('In progress');
        $cleanTheDesktop->setOwner($this->getReference('manager'));

        $manager->persist($cleanTheDesktop);

        // Manager tasks
        $checkCalendar = new Task();
        $checkCalendar->setIcon($this->uploadDummyIcon('calendar.png'));
        $checkCalendar->setTitle('Check the calendar');
        $checkCalendar->setDateDeadline((new DateTime('now'))->modify('+1 day'));
        $checkCalendar->setState('In progress');
        $checkCalendar->setOwner($this->getReference('manager'));

        $manager->persist($checkCalendar);

        $makeCalls = new Task();
        $makeCalls->setIcon($this->uploadDummyIcon('phone.png'));
        $makeCalls->setTitle('Make personal calls');
        $makeCalls->setDateDeadline((new DateTime('now'))->modify('+1 month'));
        $makeCalls->setState('Done');
        $makeCalls->setOwner($this->getReference('manager'));

        $manager->persist($makeCalls);

        $prioritizeTasks = new Task();
        $prioritizeTasks->setIcon($this->uploadDummyIcon('barchart.png'));
        $prioritizeTasks->setTitle('Prioritize your tasks');
        $prioritizeTasks->setDateDeadline(new DateTime('first day of this month'));
        $prioritizeTasks->setState('In progress');
        $prioritizeTasks->setOwner($this->getReference('manager'));

        $manager->persist($prioritizeTasks);

        $prioritizeEmails = new Task();
        $prioritizeEmails->setIcon($this->uploadDummyIcon('pin.png'));
        $prioritizeEmails->setTitle('Prioritize your emails');
        $prioritizeEmails->setDateDeadline(new DateTime('last day of +1 month'));
        $prioritizeEmails->setState('In progress');
        $prioritizeEmails->setOwner($this->getReference('manager'));

        $manager->persist($prioritizeEmails);

        $makeDonation = new Task();
        $makeDonation->setIcon($this->uploadDummyIcon('support.png'));
        $makeDonation->setTitle('Make a small donation');
        $makeDonation->setDateDeadline(new DateTime('last day of +1 month'));
        $makeDonation->setState('In progress');
        $makeDonation->setOwner($this->getReference('manager'));

        $manager->persist($makeDonation);

        $doMarketing = new Task();
        $doMarketing->setIcon($this->uploadDummyIcon('trends.png'));
        $doMarketing->setTitle('Do one or two small marketing actions');
        $doMarketing->setDateDeadline(new DateTime('last day of +3 month'));
        $doMarketing->setState('In progress');
        $doMarketing->setOwner($this->getReference('manager'));

        $manager->persist($doMarketing);

        $sendEmail = new Task();
        $sendEmail->setIcon($this->uploadDummyIcon('email.png'));
        $sendEmail->setTitle('Send personal emails (when necessary)');
        $sendEmail->setDateDeadline(new DateTime('yesterday noon'));
        $sendEmail->setState('In progress');
        $sendEmail->setOwner($this->getReference('manager'));

        $manager->persist($sendEmail);

        $prepareGoals = new Task();
        $prepareGoals->setIcon($this->uploadDummyIcon('check.png'));
        $prepareGoals->setTitle('Prepare goals for the next day');
        $prepareGoals->setDateDeadline(new DateTime('Monday next week'));
        $prepareGoals->setState('Done');
        $prepareGoals->setOwner($this->getReference('manager'));

        $manager->persist($prepareGoals);

        $trainGut = new Task();
        $trainGut->setIcon($this->uploadDummyIcon('bulb.png'));
        $trainGut->setTitle('Train my gut sense');
        $trainGut->setDateDeadline(new DateTime('Monday next week'));
        $trainGut->setState('In progress');
        $trainGut->setOwner($this->getReference('manager'));

        $manager->persist($trainGut);

        $reviewIdeas = new Task();
        $reviewIdeas->setIcon($this->uploadDummyIcon('lightbulb.png'));
        $reviewIdeas->setTitle('Review all your ideas');
        $reviewIdeas->setDateDeadline(new DateTime('tomorrow'));
        $reviewIdeas->setState('In progress');
        $reviewIdeas->setOwner($this->getReference('manager'));

        $manager->persist($reviewIdeas);

        $readTheNews = new Task();
        $readTheNews->setIcon($this->uploadDummyIcon('news.png'));
        $readTheNews->setTitle('Read the news');
        $readTheNews->setDateDeadline(new DateTime('+3 days'));
        $readTheNews->setState('Done');
        $readTheNews->setOwner($this->getReference('manager'));

        $manager->persist($readTheNews);


        // Housewife tasks
        $makeBed = new Task();
        $makeBed->setIcon($this->uploadDummyIcon('bed.png'));
        $makeBed->setTitle('Make bed');
        $makeBed->setDateDeadline((new DateTime('now'))->modify('-5 hour'));
        $makeBed->setState('In progress');
        $makeBed->setOwner($this->getReference('housewife'));

        $manager->persist($makeBed);

        $welcomeDay = new Task();
        $welcomeDay->setIcon($this->uploadDummyIcon('colorwheel.png'));
        $welcomeDay->setTitle('Open the curtains and welcome the day');
        $welcomeDay->setDateDeadline((new DateTime('now'))->modify('-5 hour'));
        $welcomeDay->setState('In progress');
        $welcomeDay->setOwner($this->getReference('housewife'));

        $manager->persist($welcomeDay);

        $careOfPlants = new Task();
        $careOfPlants->setIcon($this->uploadDummyIcon('flower.png'));
        $careOfPlants->setTitle('Take care of plants');
        $careOfPlants->setDateDeadline((new DateTime('now'))->modify('+3 hour'));
        $careOfPlants->setState('In progress');
        $careOfPlants->setOwner($this->getReference('housewife'));

        $manager->persist($careOfPlants);

        $cleanTheRoom = new Task();
        $cleanTheRoom->setIcon($this->uploadDummyIcon('room.png'));
        $cleanTheRoom->setTitle('Clean the room');
        $cleanTheRoom->setDateDeadline((new DateTime('now'))->modify('-1 day'));
        $cleanTheRoom->setState('In progress');
        $cleanTheRoom->setOwner($this->getReference('housewife'));

        $manager->persist($cleanTheRoom);

        $appreciate = new Task();
        $appreciate->setIcon($this->uploadDummyIcon('home.png'));
        $appreciate->setTitle('Appreciate something in your home');
        $appreciate->setDateDeadline((new DateTime('now'))->modify('-1 week'));
        $appreciate->setState('In progress');
        $appreciate->setOwner($this->getReference('housewife'));

        $manager->persist($appreciate);

        $goShopping = new Task();
        $goShopping->setIcon($this->uploadDummyIcon('cart.png'));
        $goShopping->setTitle('Go shopping');
        $goShopping->setDateDeadline((new DateTime('now'))->modify('+1 hour'));
        $goShopping->setState('Done');
        $goShopping->setOwner($this->getReference('housewife'));

        $manager->persist($goShopping);

        $meditate = new Task();
        $meditate->setIcon($this->uploadDummyIcon('rainbow.png'));
        $meditate->setTitle('Meditate');
        $meditate->setDateDeadline((new DateTime('now'))->modify('+1 day'));
        $meditate->setState('In progress');
        $meditate->setOwner($this->getReference('housewife'));

        $manager->persist($meditate);

        $doKindness = new Task();
        $doKindness->setIcon($this->uploadDummyIcon('heart.png'));
        $doKindness->setTitle('Do an act of kindness');
        $doKindness->setDateDeadline((new DateTime('now'))->modify('+3 days'));
        $doKindness->setState('In progress');
        $doKindness->setOwner($this->getReference('housewife'));

        $manager->persist($doKindness);

        $readGoodNews = new Task();
        $readGoodNews->setIcon($this->uploadDummyIcon('focus.png'));
        $readGoodNews->setTitle('Read good news');
        $readGoodNews->setDateDeadline((new DateTime('now'))->modify('+4 days'));
        $readGoodNews->setState('In progress');
        $readGoodNews->setOwner($this->getReference('housewife'));

        $manager->persist($readGoodNews);

        $doHobby = new Task();
        $doHobby->setIcon($this->uploadDummyIcon('brush-pencil.png'));
        $doHobby->setTitle('Spend time on a hobby');
        $doHobby->setDateDeadline((new DateTime('now'))->modify('-5 hour'));
        $doHobby->setState('In progress');
        $doHobby->setOwner($this->getReference('housewife'));

        $manager->persist($doHobby);

        $doWork = new Task();
        $doWork->setIcon($this->uploadDummyIcon('art.png'));
        $doWork->setTitle('Do creative work');
        $doWork->setDateDeadline((new DateTime('now'))->modify('+8 days'));
        $doWork->setState('In progress');
        $doWork->setOwner($this->getReference('housewife'));

        $manager->persist($doWork);

        $keepTheJournal = new Task();
        $keepTheJournal->setIcon($this->uploadDummyIcon('magicwand.png'));
        $keepTheJournal->setTitle('Keep a dream journal');
        $keepTheJournal->setDateDeadline((new DateTime('now'))->modify('+2 weeks'));
        $keepTheJournal->setState('In progress');
        $keepTheJournal->setOwner($this->getReference('housewife'));

        $manager->persist($keepTheJournal);

        $reviewDay = new Task();
        $reviewDay->setIcon($this->uploadDummyIcon('clipboard.png'));
        $reviewDay->setTitle('Review your previous day\'s spending');
        $reviewDay->setDateDeadline((new DateTime('now'))->modify('now'));
        $reviewDay->setState('In progress');
        $reviewDay->setOwner($this->getReference('housewife'));

        $manager->persist($reviewDay);

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
        $readTheBook->setDateDeadline((new DateTime('now'))->modify('-2 hour'));
        $readTheBook->setState('Done');
        $readTheBook->setOwner($this->getReference('student'));

        $manager->persist($readTheBook);

        $takeAtalk = new Task();
        $takeAtalk->setIcon($this->uploadDummyIcon('video.png'));
        $takeAtalk->setTitle('Take in a TED talk');
        $takeAtalk->setDateDeadline((new DateTime('now'))->modify('+5 hour'));
        $takeAtalk->setState('In progress');
        $takeAtalk->setOwner($this->getReference('student'));

        $manager->persist($takeAtalk);

        $listenPodcast = new Task();
        $listenPodcast->setIcon($this->uploadDummyIcon('headphones.png'));
        $listenPodcast->setTitle('Listen to a podcast');
        $listenPodcast->setDateDeadline((new DateTime('now'))->modify('-1 week'));
        $listenPodcast->setState('Done');
        $listenPodcast->setOwner($this->getReference('student'));

        $manager->persist($listenPodcast);

        $readTheArticle = new Task();
        $readTheArticle->setIcon($this->uploadDummyIcon('browser.png'));
        $readTheArticle->setTitle('Read the article');
        $readTheArticle->setDateDeadline((new DateTime('now'))->modify('+5 hour'));
        $readTheArticle->setState('In progress');
        $readTheArticle->setOwner($this->getReference('student'));

        $manager->persist($readTheArticle);

        $writeBlogpost = new Task();
        $writeBlogpost->setIcon($this->uploadDummyIcon('pencil.png'));
        $writeBlogpost->setTitle('Write a blog post');
        $writeBlogpost->setDateDeadline((new DateTime('now'))->modify('+3 days'));
        $writeBlogpost->setState('In progress');
        $writeBlogpost->setOwner($this->getReference('student'));

        $manager->persist($writeBlogpost);

        $listenToMusic = new Task();
        $listenToMusic->setIcon($this->uploadDummyIcon('radio.png'));
        $listenToMusic->setTitle('Listen to music');
        $listenToMusic->setDateDeadline((new DateTime('now'))->modify('+3 days'));
        $listenToMusic->setState('In progress');
        $listenToMusic->setOwner($this->getReference('student'));

        $manager->persist($listenToMusic);

        $writeIdeas = new Task();
        $writeIdeas->setIcon($this->uploadDummyIcon('genius.png'));
        $writeIdeas->setTitle('Write down 10 new ideas');
        $writeIdeas->setDateDeadline((new DateTime('now'))->modify('+1 week'));
        $writeIdeas->setState('Done');
        $writeIdeas->setOwner($this->getReference('student'));

        $manager->persist($writeIdeas);

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
        $winTheMillion->setState('Done');
        $winTheMillion->setOwner($this->getReference('student'));

        $manager->persist($winTheMillion);

        $walkTheDog = new Task();
        $walkTheDog->setIcon($this->uploadDummyIcon('medal.png'));
        $walkTheDog->setTitle('Take your dog for a walk');
        $walkTheDog->setDateDeadline((new DateTime('now'))->modify('+1 week'));
        $walkTheDog->setState('Done');
        $walkTheDog->setOwner($this->getReference('student'));

        $manager->persist($walkTheDog);

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

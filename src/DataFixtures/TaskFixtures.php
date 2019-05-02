<?php

namespace App\DataFixtures;

use DateTime;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Task;
use Exception;

/**
 * Class TaskFixtures
 * @package App\DataFixtures
 */
class TaskFixtures extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{
    /**
     * Loads Task Fixtures into Database
     *
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        // Admin Tasks
        $firstAdminTask = new Task();
        $firstAdminTask->setTitle('First Admin task');
        $firstAdminTask->setDateDeadline(new DateTime('2000-09-30 20:00:00'));
        $firstAdminTask->setState('In progress');
        $firstAdminTask->setOwner($this->getReference('admin'));

        $manager->persist($firstAdminTask);

        $secondAdminTask = new Task();
        $secondAdminTask->setTitle('Second Admin task');
        $secondAdminTask->setDateDeadline(new DateTime('2000-10-30 20:00:00'));
        $secondAdminTask->setState('In progress');
        $secondAdminTask->setOwner($this->getReference('admin'));

        $manager->persist($secondAdminTask);


        // User Tasks
        $firstUserTask = new Task();
        $firstUserTask->setTitle('First User task');
        $firstUserTask->setDateDeadline(new DateTime('2001-09-30 20:00:00'));
        $firstUserTask->setState('In progress');
        $firstUserTask->setOwner($this->getReference('user'));

        $manager->persist($firstUserTask);

        $secondUserTask = new Task();
        $secondUserTask->setTitle('Second User task');
        $secondUserTask->setDateDeadline(new DateTime('2001-10-30 20:00:00'));
        $secondUserTask->setState('In progress');
        $secondUserTask->setOwner($this->getReference('user'));

        $manager->persist($secondUserTask);

        $thirdUserTask = new Task();
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

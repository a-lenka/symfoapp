<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;


    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * Loads User Fixtures into Database
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $root = new User();
        $root->setEmail('root@mail.ru');
        $root->setRoles(['ROLE_ROOT']);
        $root->setPassword($this->passwordEncoder->encodePassword(
            $root, 'kitten'
        ));

        $manager->persist($root);

        $admin = new User();
        $admin->setEmail('admin@mail.ru');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin, 'kitten'
        ));

        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@mail.ru');
        $user->setRoles([]);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user, 'kitten'
        ));

        $manager->persist($user);

        $somebody = new User();
        $somebody->setEmail('somebody@mail.ru');
        $somebody->setRoles([]);
        $somebody->setPassword($this->passwordEncoder->encodePassword(
            $user, 'kitten'
        ));

        $manager->persist($somebody);

        $manager->flush();
    }
}

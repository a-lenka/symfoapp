<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
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
        // Root
        $root = new User();
        $root->setAvatar('root.png');
        $root->setEmail('root@mail.ru');
        $root->setRoles(['ROLE_ROOT']);
        $root->setPassword($this->passwordEncoder->encodePassword(
            $root, 'kitten'
        ));

        $manager->persist($root);

        // Admin
        $admin = new User();
        $admin->setAvatar('admin.png');
        $admin->setEmail('admin@mail.ru');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin, 'kitten'
        ));

        $manager->persist($admin);

        // User
        $user = new User();
        $user->setAvatar('user.png');
        $user->setEmail('user@mail.ru');
        $user->setRoles([]);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user, 'kitten'
        ));

        $manager->persist($user);

        // Anonymous
        $anonymous = new User();
        $anonymous->setAvatar('anonymous.png');
        $anonymous->setEmail('anonymous@mail.ru');
        $anonymous->setRoles([]);
        $anonymous->setPassword($this->passwordEncoder->encodePassword(
            $anonymous, 'kitten'
        ));

        $manager->persist($anonymous);

        $manager->flush();

        $this->addReference('root', $root);
        $this->addReference('admin', $admin);
        $this->addReference('user', $user);
        $this->addReference('anonymous', $anonymous);
    }


    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 1;
    }
}

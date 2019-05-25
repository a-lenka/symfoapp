<?php

namespace App\DataFixtures;

use App\Service\FileUploader;
use App\Service\PathKeeper;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
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

    /** @var FileUploader */
    private $fileUploader;

    /** @var PathKeeper */
    private $pathKeeper;

    /** @const string AVATARS_DIR - Avatars fixtures directory */
    private const FIXTURE_AVATARS_DIR = 'images/avatars';


    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FileUploader                 $uploader
     * @param PathKeeper                   $pathKeeper
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        FileUploader                 $uploader,
        PathKeeper                   $pathKeeper
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->fileUploader    = $uploader;
        $this->pathKeeper      = $pathKeeper;
    }


    /**
     * @param string $filename
     * @return string
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    private function uploadDummyAvatars(string $filename): string
    {
        $fs = new Filesystem();

        $sourceFile = __DIR__.'/'.self::FIXTURE_AVATARS_DIR.'/'.$filename;
        $targetFile = sys_get_temp_dir().'/'.$filename;

        $fs->copy($sourceFile, $targetFile, true);

        return $this->fileUploader->uploadEntityIcon(
            PathKeeper::UPLOADED_AVATARS_DIR,
            new File($targetFile),
            null
        );
    }


    /**
     * Loads User Fixtures into Database
     *
     * @param ObjectManager $manager
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function load(ObjectManager $manager): void
    {
        $path = $this->pathKeeper->getPublicUploadsSystemPath().'/avatars';
        $this->fileUploader->clearDir($path);

        // Root
        $root = new User();
        $root->setAvatar($this->uploadDummyAvatars('root.png'));
        $root->setEmail('root@mail.ru');
        $root->setTheme('red lighten-2');
        $root->setRoles(['ROLE_ROOT']);
        $root->setPassword($this->passwordEncoder->encodePassword(
            $root, 'kitten'
        ));

        $manager->persist($root);

        // Admin
        $admin = new User();
        $admin->setAvatar($this->uploadDummyAvatars('admin.png'));
        $admin->setEmail('admin@mail.ru');
        $admin->setTheme('red lighten-2');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin, 'kitten'
        ));

        $manager->persist($admin);

        // User
        $user = new User();
        $user->setAvatar($this->uploadDummyAvatars('user.png'));
        $user->setEmail('user@mail.ru');
        $user->setTheme('red lighten-2');
        $user->setRoles([]);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user, 'kitten'
        ));

        $manager->persist($user);

        // Anonymous
        $anonymous = new User();
        $anonymous->setAvatar($this->uploadDummyAvatars('anonymous.png'));
        $anonymous->setEmail('anonymous@mail.ru');
        $anonymous->setTheme('red lighten-2');
        $anonymous->setRoles([]);
        $anonymous->setPassword($this->passwordEncoder->encodePassword(
            $anonymous, 'kitten'
        ));

        $manager->persist($anonymous);

        // Housewife
        $housewife = new User();
        $housewife->setAvatar($this->uploadDummyAvatars('housewife.jpeg'));
        $housewife->setEmail('housewife@mail.ru');
        $housewife->setTheme('red lighten-2');
        $housewife->setRoles([]);
        $housewife->setPassword($this->passwordEncoder->encodePassword(
            $housewife, 'kitten'
        ));

        $manager->persist($housewife);

        // Student
        $student = new User();
        $student->setAvatar($this->uploadDummyAvatars('student.jpeg'));
        $student->setEmail('student@mail.ru');
        $student->setTheme('red lighten-2');
        $student->setRoles([]);
        $student->setPassword($this->passwordEncoder->encodePassword(
            $student, 'kitten'
        ));

        $manager->persist($student);

        $manager->flush();

        $this->addReference('root', $root);
        $this->addReference('admin', $admin);
        $this->addReference('user', $user);
        $this->addReference('anonymous', $anonymous);
        $this->addReference('housewife', $housewife);
        $this->addReference('student', $student);
    }


    /**
     * @return int
     */
    final public function getOrder(): int
    {
        return 1;
    }
}

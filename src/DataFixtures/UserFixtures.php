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
     * @param ObjectManager $objectManager
     *
     * @throws FileExistsException
     * @throws FileNotFoundException
     */
    final public function load(ObjectManager $objectManager): void
    {
        $path = $this->pathKeeper->getPublicUploadsSystemPath().'/avatars';
        $this->fileUploader->clearDir($path);

        // Root
        $root = new User();
        $root->setNickname('Root');
        $root->setAvatar($this->uploadDummyAvatars('root.png'));
        $root->setEmail('root@mail.ru');
        $root->setTheme(User::THEMES['Indigo']);
        $root->setRoles(['ROLE_ROOT']);
        $root->setPassword($this->passwordEncoder->encodePassword(
            $root, 'kitten'
        ));

        $objectManager->persist($root);

        // Manager
        $manager = new User();
        $manager->setNickname('Manager');
        $manager->setAvatar($this->uploadDummyAvatars('manager.png'));
        $manager->setEmail('manager@mail.ru');
        $manager->setTheme(User::THEMES['Black']);
        $manager->setRoles([]);
        $manager->setPassword($this->passwordEncoder->encodePassword(
            $manager, 'kitten'
        ));

        $objectManager->persist($manager);

        // Housewife
        $housewife = new User();
        $housewife->setNickname('Housewife');
        $housewife->setAvatar($this->uploadDummyAvatars('housewife.jpeg'));
        $housewife->setEmail('housewife@mail.ru');
        $housewife->setTheme(User::THEMES['Purple']);
        $housewife->setRoles([]);
        $housewife->setPassword($this->passwordEncoder->encodePassword(
            $housewife, 'kitten'
        ));

        $objectManager->persist($housewife);

        // Student
        $student = new User();
        $student->setNickname('Student');
        $student->setAvatar($this->uploadDummyAvatars('student.jpeg'));
        $student->setEmail('student@mail.ru');
        $student->setTheme(User::THEMES['Purple']);
        $student->setRoles([]);
        $student->setPassword($this->passwordEncoder->encodePassword(
            $student, 'kitten'
        ));

        $objectManager->persist($student);

        $objectManager->flush();

        $this->addReference('manager', $manager);
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

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
use Symfony\Component\HttpKernel\KernelInterface;
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

    /** @const string AVATARS_DIR - Avatars fixtures directory */
    private const AVATARS_DIR = '/images/avatars/';

    /** KernelInterface $appKernel */
    private $appKernel;


    /**
     * @param KernelInterface              $appKernel
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FileUploader                 $uploader
     */
    public function __construct(
        KernelInterface $appKernel,
        UserPasswordEncoderInterface $passwordEncoder,
        FileUploader                 $uploader
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->fileUploader    = $uploader;
        $this->appKernel       = $appKernel;
    }


    /**
     * Clear directory before fixtures loading
     */
    private function clearDir(): void
    {
        $files = glob($this->appKernel->getProjectDir().'/public/uploads/avatars/*');

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
    private function uploadDummyAvatars(string $filename): string
    {
        $fs = new Filesystem();

        $sourceFile = __DIR__.self::AVATARS_DIR.$filename;
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
        $this->clearDir();

        // Root
        $root = new User();
        $root->setAvatar($this->uploadDummyAvatars('root.png'));
        $root->setEmail('root@mail.ru');
        $root->setRoles(['ROLE_ROOT']);
        $root->setPassword($this->passwordEncoder->encodePassword(
            $root, 'kitten'
        ));

        $manager->persist($root);

        // Admin
        $admin = new User();
        $admin->setAvatar($this->uploadDummyAvatars('admin.png'));
        $admin->setEmail('admin@mail.ru');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin, 'kitten'
        ));

        $manager->persist($admin);

        // User
        $user = new User();
        $user->setAvatar($this->uploadDummyAvatars('user.png'));
        $user->setEmail('user@mail.ru');
        $user->setRoles([]);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user, 'kitten'
        ));

        $manager->persist($user);

        // Anonymous
        $anonymous = new User();
        $anonymous->setAvatar($this->uploadDummyAvatars('anonymous.png'));
        $anonymous->setEmail('anonymous@mail.ru');
        $anonymous->setRoles([]);
        $anonymous->setPassword($this->passwordEncoder->encodePassword(
            $anonymous, 'kitten'
        ));

        $manager->persist($anonymous);

        // Housewife
        $housewife = new User();
        $housewife->setAvatar($this->uploadDummyAvatars('housewife.jpeg'));
        $housewife->setEmail('housewife@mail.ru');
        $housewife->setRoles([]);
        $housewife->setPassword($this->passwordEncoder->encodePassword(
            $housewife, 'kitten'
        ));

        $manager->persist($housewife);

        // Student
        $student = new User();
        $student->setAvatar($this->uploadDummyAvatars('student.jpeg'));
        $student->setEmail('student@mail.ru');
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
    }


    /**
     * @return int
     */
    final public function getOrder(): int
    {
        return 1;
    }
}

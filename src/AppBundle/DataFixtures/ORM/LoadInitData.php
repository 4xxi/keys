<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Group;
use AppBundle\Entity\Password;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadInitData extends AbstractFixture implements ContainerAwareInterface
{
    const USERS = ['admin', 'user', 'stranger'];

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        /** @var UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');

        foreach (self::USERS as $username) {
            $user = $userManager->createUser();
            $user->setUsername($username);
            $userManager->updateUser($user, false);
            $user->setEmail($user->getUsernameCanonical().'@4xxi.com');
            $user->setPlainPassword($user->getUsernameCanonical());
            $user->setSalt(uniqid($user->getUsernameCanonical()));
            $user->setEnabled(true);
            $user->addRole($username === 'admin' ? 'ROLE_ADMIN' : 'ROLE_USER');

            $userManager->updateUser($user, true);

            $this->addReference('user_'.$username, $user);

            $password = new Password();
            $password->setTitle('Secret Password of '.$user->getUsernameCanonical());
            $password->setPassword('secret_password_'.$user->getUsernameCanonical());
            $password->setTags('secret, private');
            $password->setOwnerGroup($user->getPrivateGroup());
            $password->addGroup($user->getPrivateGroup());
            $manager->persist($password);

            $publicGroup = new Group('Public Group of '.$user->getUsernameCanonical());
            $publicGroup->setOwner($user);
            $publicGroup->addUser($user);
            $user->addGroup($publicGroup);
            $manager->persist($publicGroup);

            $publicPassword = new Password();
            $publicPassword->setTitle('Public Password of '.$user->getUsernameCanonical());
            $publicPassword->setPassword('public_password_'.$user->getUsernameCanonical());
            $publicPassword->setTags('public');
            $publicPassword->setOwnerGroup($user->getPrivateGroup());
            $publicPassword->addGroup($publicGroup);
            $manager->persist($publicPassword);
        }

        $commonGroup = new Group('Common Group');
        $commonGroup->setOwner($this->getReference('user_admin'));
        foreach (self::USERS as $username) {
            $commonGroup->addUser($this->getReference('user_'.$username));
            $this->getReference('user_'.$username)->addGroup($commonGroup);
        }
        $manager->persist($commonGroup);

        $commonPassword = new Password();
        $commonPassword->setTitle('Common Password');
        $commonPassword->setPassword('common_password');
        $commonPassword->setTags('public, common');
        $commonPassword->setOwnerGroup($this->getReference('user_admin')->getPrivateGroup());
        $commonPassword->addGroup($commonGroup);
        $manager->persist($commonPassword);

        $manager->flush();
    }
}

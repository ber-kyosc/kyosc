<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('user@kyosc.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'userpassword'
        ));
        $user->setFirstName('Christophe');
        $user->setLastName('Bertrand');
        $user->setAddress('182 allée de la rose');
        $user->setPostalCode('73100');
        $user->setCity('Chambéry');
        $user->setPseudo('Christophe');
        $user->setPoints(3);
        $user->setTestimony('Un moment incroyable que je souhaite réiterer !');
        $user->setUpdatedAt(new DateTimeImmutable('now'));
        $manager->persist($user);
        $this->addReference('user', $user);

        $admin = new User();
        $admin->setEmail('admin@kyosc.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));
        $admin->setFirstName('Barbara');
        $admin->setLastName('Dupont');
        $admin->setAddress('57 chemin du bourg');
        $admin->setPostalCode('75006');
        $admin->setCity('Paris');
        $admin->setPseudo('Barbara');
        $admin->setPoints(15);
        $admin->setUpdatedAt(new DateTimeImmutable('now'));
        $admin->setTestimony(
            'Super expérience que de participer à un challenge Kyosc, je le recommande à tous mes amis!'
        );
        $manager->persist($admin);

        $sudo = new User();
        $sudo->setEmail('bertrand@kyosc.com');
        $sudo->setRoles(['ROLE_SUPER_ADMIN']);
        $sudo->setPassword($this->passwordEncoder->encodePassword(
            $sudo,
            'adminpassword'
        ));
        $sudo->setFirstName('Bertrand');
        $sudo->setLastName('Ollagnon');
        $sudo->setAddress('3 rue Stalingrad');
        $sudo->setPostalCode('69006');
        $sudo->setCity('Lyon');
        $sudo->setPseudo('Bertrand');
        $sudo->setPoints(155);
        $sudo->setUpdatedAt(new DateTimeImmutable('now'));
        $sudo->setTestimony('De belles rencontres et un super défi sportif !');
        $manager->persist($sudo);

        $manager->flush();
    }
}

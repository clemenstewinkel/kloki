<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $dummy_users = [
            'admin@test.de'        => ['name' => 'Super Admin',    'password' => 'admin', 'roles' => ['ROLE_ADMIN']],
            'helfer1@hilfreich.de' => ['name' => 'Helfer Eins',    'password' => 'test',  'roles' => ['ROLE_HELPER']],
            'helfer2@hilfreich.de' => ['name' => 'Helfer Zwei',    'password' => 'test',  'roles' => ['ROLE_HELPER']],
            'helfer3@hilfreich.de' => ['name' => 'Helfer Drei',    'password' => 'test',  'roles' => ['ROLE_HELPER']],
            'helfer4@hilfreich.de' => ['name' => 'Helfer Vier',    'password' => 'test',  'roles' => ['ROLE_HELPER']],
            'helfer5@hilfreich.de' => ['name' => 'Helfer Fünf',    'password' => 'test',  'roles' => ['ROLE_HELPER']],
            'vermieter1@miet.de'   => ['name' => 'Vermieter Eins', 'password' => 'test',  'roles' => ['ROLE_LANDLORD']],
            'vermieter2@miet.de'   => ['name' => 'Vermieter Zwei', 'password' => 'test',  'roles' => ['ROLE_LANDLORD']],
            'schaenke1@lecker.de'  => ['name' => 'Schänke Eins',   'password' => 'test',  'roles' => ['ROLE_FOOD']],
            'schaenke2@lecker.de'  => ['name' => 'Schänke Zwei',   'password' => 'test',  'roles' => ['ROLE_FOOD']],
            'technik1@expose.de'   => ['name' => 'Technik Eins',   'password' => 'test',  'roles' => ['ROLE_TECH']],
            'technik2@expose.de'   => ['name' => 'Technik Zwei',   'password' => 'test',  'roles' => ['ROLE_TECH']],
        ];
        foreach($dummy_users as $mail => $val)
        {
            $user = new User();
            $user->setEmail($mail);
            $user->setRoles($val['roles']);
            $user->setName($val['name']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $val['password']));
            $manager->persist($user);
        }

        $manager->flush();
    }
}

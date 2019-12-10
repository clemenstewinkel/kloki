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
            'admin@test.de'         => [ 'name' => 'Super Admin', 'password' => 'admin', 'roles' => ['ROLE_ADMIN']],

            'Birgid@hilfreich.de'   => [ 'name' => 'Birgid'     , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Birgit@hilfreich.de'   => [ 'name' => 'Birgit'     , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Birgit.S@hilfreich.de' => [ 'name' => 'Birgit S.'  , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Cornelia@hilfreich.de' => [ 'name' => 'Cornelia'   , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Felix@hilfreich.de'    => [ 'name' => 'Felix'      , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Gabi@hilfreich.de'     => [ 'name' => 'Gabi'       , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Gudrun@hilfreich.de'   => [ 'name' => 'Gudrun'     , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Gunnar@hilfreich.de'   => [ 'name' => 'Gunnar'     , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Monika@hilfreich.de'   => [ 'name' => 'Monika'     , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Peter@hilfreich.de'    => [ 'name' => 'Peter'      , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Peter.S@hilfreich.de'  => [ 'name' => 'Peter S.'   , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Sabine@hilfreich.de'   => [ 'name' => 'Sabine'     , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Udo@hilfreich.de'      => [ 'name' => 'Udo'        , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
            'Uli@hilfreich.de'      => [ 'name' => 'Uli'        , 'password' => 'test', 'roles' => ['ROLE_HELPER']],
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

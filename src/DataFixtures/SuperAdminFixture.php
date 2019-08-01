<?php

namespace App\DataFixtures;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class SuperAdminFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setUsername('admin');
        $password = $this->encoder->encodePassword($user, 'passer');
        $user->setPassword($password);
        $user->setRoles(['ROLE_SUPER']);
        $user->setNom('biteye');
        $user->setPrenom('bassirou');
        $user->setEtat('actif');
        $user->setTelephone(771523139);
        $user->setPhoto(null);
        $user->setCompte(null);
        $user->setPartenaire(null);

        $manager->persist($user);

        $manager->flush();
    }
}

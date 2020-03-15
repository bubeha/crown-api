<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    private $encoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();

        $password = $this->encoder->encodePassword($user, '12345678');

        $user
            ->setEmail('admin@example.com')
            ->setPassword($password)
            ->setRoles(['ROLE_USER']);

        $manager->persist($user);
        $manager->flush();
    }
}

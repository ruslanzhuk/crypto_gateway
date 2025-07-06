<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixture extends Fixture
{

    public const ROLES = [
        ['name' => 'ROLE_SUPERADMIN', 'access' => ['full']],
        ['name' => 'ROLE_ADMIN', 'access' => ['administrator']],
    ];

    public function load(ObjectManager $manager): void
    {
       foreach (self::ROLES as $roleData) {
           $role = new Role();
           $role->setName($roleData['name']);
           $role->setAccess($roleData['access']);
           $manager->persist($role);

           $this->addReference($roleData['name'], $role);
       }

        $manager->flush();
    }
}

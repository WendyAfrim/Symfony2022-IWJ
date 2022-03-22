<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $object = (new Customer())
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}

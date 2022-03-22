<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Car;
use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $brands = $manager->getRepository(Brand::class)->findAll();
        $customers = $manager->getRepository(Customer::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $object = (new Car())
                ->setHorsePower($faker->numberBetween(100, 400))
                ->setMatriculation($faker->randomLetter . $faker->randomLetter . '-' . $faker->randomLetter . $faker->randomLetter . '-' . $faker->numberBetween(50, 999))
                ->setMatriculationDate($faker->dateTime)
                ->setModel($faker->word)
                ->setBrand($faker->randomElement($brands))
            ;
            for ($y = 0; $y < $faker->numberBetween(0,5); $y++) {
                $object->addOwner($faker->randomElement($customers));
            }
            $manager->persist($object);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            BrandFixtures::class,
            CustomerFixtures::class
        ];
    }
}

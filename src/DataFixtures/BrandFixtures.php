<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brands = ['Volkswagen', 'Ford', 'Renaud', 'Audi', 'Porsche', 'Dacia'];

        foreach ($brands as $brand) {
            $object = (new Brand())
                ->setName($brand)
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}

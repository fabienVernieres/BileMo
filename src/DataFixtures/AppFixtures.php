<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Faker\Factory;
use App\Entity\Product;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $brand[$i] = new Brand();
            $brand[$i]->setName($faker->company());

            $manager->persist($brand[$i]);
        }

        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->text(20));
            $product->setDescription($faker->text(200));
            $product->setPrice($faker->randomFloat(2, 99.98, 2000));
            $product->setStock($faker->randomNumber(3));
            $product->setBrand($brand[$faker->numberBetween(0, 9)]);
            $product->setEan(intval($faker->ean13()));
            $product->setCreationDate(new DateTime());

            $manager->persist($product);
        }

        $manager->flush();
    }
}
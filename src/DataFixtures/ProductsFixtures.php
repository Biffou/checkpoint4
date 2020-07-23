<?php

namespace App\DataFixtures;

use App\Entity\Products;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;


class ProductsFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 50; $i++) {
            $products = new Products();
            $products->setName($faker->name);
            $products->setDescription($faker->text);
            $products->setPhoto('https://www.francetvinfo.fr/image/75ee3u3x1-e4c3/580/326/13319635.jpg');
            $products->setCategory($this->getReference('category_'.random_int(0, 4)));
            $manager->persist($products);
        }
        $manager->flush();
    }
}
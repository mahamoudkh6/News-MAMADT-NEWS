<?php

namespace App\DataFixtures;

use Faker;
use DateTime;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Validator\Constraints\DateTime as ValidatorDateTime;


class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créez une instance de Faker
        $faker = Faker\Factory::create('fr_FR');


        for ($i = 0; $i < 150; $i++) {
        $category = $this->getReference('categoryShop.' . $faker->numberBetween(1, 5));

        $product = new Product();
        $product->setTitle($faker->sentence);
        $product->setSlug($faker->slug);
        $product->setContent($faker->text);
        $product->setOnline(true);
        $product->setCreatedAt(new DateTime('now'));
        $product->setSlug($faker->slug);
        $product->setOrigine($faker->text(50));
        $product->setAttachment($faker->imageUrl(650, 400, 'Poissons', true));
        $product->setPrice($faker->randomFloat(2));
        $product->setCategory($category);
        $manager->persist($product);
        }

        $manager->flush();
    }

}


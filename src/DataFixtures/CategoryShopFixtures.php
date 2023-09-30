<?php

// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\CategoryShop;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;



class CategoryShopFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $c = [
            1 => [
                'name' => 'Fruits et Légumes',
                'slug' => 'Fruits-et-Legumes',
            ],
            2 => [
                'name' => 'Farines',
                'slug' => 'Farines',
            ],
            3 => [
                'name' => 'Poissons',
                'slug' => 'Poissons',
            ],
            4 => [
                'name' => 'Epiceries',
                'slug' => 'Epiceries',
            ],
            5 => [
                'name' => 'Accessoires',
                'slug' => 'Accessoires',
            ],
            6 => [
                'name' => 'Boissons',
                'slug' => 'Boissons',
            ],
        ];

        foreach ($c as $k => $value) {
            $categoryShop = new CategoryShop();
            $categoryShop->setName($value['name']);
            $categoryShop->setSlug($value['slug']);
            $manager->persist($categoryShop);
            $this->addReference('categoryShop.'. $k,  $categoryShop);

        }

        $manager->flush();
    }
}

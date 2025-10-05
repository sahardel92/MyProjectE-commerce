<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    // constants dyal smiyat dyal categories
    // bach nst3mloha f SubCategoriesFixtures.php
    // haka kayji shl man kola mara nktbohom o nsd9o nasyin chi 7arf
    public const ELECTRONICS = 'Electronics';
    public const BOOKS = 'Books';
    public const CLOTHING = 'Clothing';
    public const HOME_KITCHEN = 'Home & Kitchen';
    public const SPORTS = 'Sports';
    public const TOYS = 'Toys';
    public const BEAUTY = 'Beauty';

    // array dyal ga3 l categories
    // bach nst3mloha f loop o n9do njibo ga3 l categories name
    public const CATEGORIES = [
        self::ELECTRONICS,
        self::BOOKS,
        self::CLOTHING,
        self::HOME_KITCHEN,
        self::SPORTS,
        self::TOYS,
        self::BEAUTY,
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            // n7taw reference bach n9dro nst3mloha f SubCategoriesFixtures.php
            // o n9dro njibo ga3 l categories entities li drna hna
            // o n3tawhom subcategories m3ahom
            $this->addReference($categoryName, $category);
            $manager->persist($category);
        }
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\SubCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SubCategoriesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // drn 2d array dyal subcategories
        // key howa smiya dyal category
        // value howa array dyal subcategories li kat3tamd 3la dik category
        $subCategories = [
            CategoriesFixtures::ELECTRONICS => ['Mobile Phones', 'Laptops', 'Cameras', 'Televisions', 'Audio & Video'],
            CategoriesFixtures::BOOKS => ['Fiction', 'Non-Fiction', 'Comics', 'Biographies', 'Children'],
            CategoriesFixtures::CLOTHING => ['Men', 'Women', 'Kids', 'Accessories', 'Footwear'],
            CategoriesFixtures::HOME_KITCHEN => ['Furniture', 'Kitchen Appliances', 'Decor', 'Bedding', 'Cookware'],
            CategoriesFixtures::SPORTS => ['Fitness', 'Outdoor', 'Team Sports', 'Cycling', 'Swimming'],
            CategoriesFixtures::TOYS => ['Action Figures', 'Dolls', 'Puzzles', 'Board Games', 'Educational'],
            CategoriesFixtures::BEAUTY => ['Skincare', 'Makeup', 'Haircare', 'Fragrances', 'Nail Care'],
        ];

        //loop lwl 3la ga3 l categories
        foreach ($subCategories as $categoryName => $subCategoryNames) {
            // jbna refrence dyal dik category li drna f CategoriesFixtures.php
            // o katrj3 lina dik l entity kamla
            $category = $this->getReference($categoryName, Category::class);
            // loop tani 3la ga3 l subcategories dyal dik category
            // li deja drna f dak tableau dyalna $subCategories
            foreach ($subCategoryNames as $subCategoryName) {

                $subCategory = new SubCategory();
                $subCategory->setName($subCategoryName);
                $subCategory->setCategory($category);

                // glna l doctrine tsjl had subcat 3ndha
                $manager->persist($subCategory);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoriesFixtures::class,
        ];
    }
}

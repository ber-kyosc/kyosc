<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const CATEGORY = [
        [
            'name' => 'Aquatique',
            'logo' => 'kaquatique.png',
            'picto' => 'kaquatique.png',
            'color' => '#009eb5',
            'goutte' => 'kaquatique.png',
            'carousel' => 'aquatique.jpeg',
        ],
        [
            'name' => 'Campagne',
            'logo' => 'kampagne.png',
            'picto' => 'kampagne.png',
            'color' => '#94c022',
            'goutte' => 'kampagne.png',
            'carousel' => 'campagne.jpeg',
        ],
        [
            'name' => 'Montagne',
            'logo' => 'kmontagne.png',
            'picto' => 'kmontagne.png',
            'color' => '#e71374',
            'goutte' => 'kmontagne.png',
            'carousel' => 'montagne.jpeg',
        ],
        [
            'name' => 'Urbain',
            'logo' => 'kurbain.png',
            'picto' => 'kurbain.png',
            'color' => '#f29100',
            'goutte' => 'kurbain.png',
            'carousel' => 'urbain.jpeg',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORY as $index => $category) {
            $cat = new Category();
            $cat
                ->setName($category['name'])
                ->setLogo($category['logo'])
                ->setColor($category['color'])
                ->setPicto($category['picto'])
                ->setGoutte($category['goutte'])
                ->setCarousel($category['carousel'])
            ;
            $manager->persist($cat);
            $this->addReference('category_' . $index, $cat);
        }
        $manager->flush();
    }
}

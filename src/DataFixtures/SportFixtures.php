<?php

namespace App\DataFixtures;

use App\Entity\Sport;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SportFixtures extends Fixture
{
    private const SPORTS = [
        [
            'name' => 'Canoé',
            'logo' => 'k-canoe.png',
            'picto' => 'canoe.png',
            'color' => '#009eb5',
            'goutte' => 'Kanoeaquatique.png',
            'category' => 'category_0'
        ],
        [
            'name' => 'Paddle',
            'logo' => 'k-paddle.png',
            'picto' => 'paddle.png',
            'color' => '#009eb5',
            'goutte' => 'kpadlaquatique.png',
            'category' => 'category_0'
        ],
        [
            'name' => 'Voile',
            'logo' => 'k-voile.png',
            'picto' => 'voile.png',
            'color' => '#009eb5',
            'goutte' => 'kplancheavoileaquatique.png',
            'category' => 'category_0'
        ],
        [
            'name' => 'Pirogue',
            'logo' => 'kpirogueaquatique.png',
            'picto' => 'pirogue.png',
            'color' => '#009eb5',
            'goutte' => 'kpirogueaquatique.png',
            'category' => 'category_0'
        ],
        [
            'name' => 'Nage',
            'logo' => 'knagekyosc.png',
            'picto' => 'knageseul.png',
            'color' => '#009eb5',
            'goutte' => 'knage.png',
            'category' => 'category_0'
        ],
        [
            'name' => 'Trail',
            'logo' => 'k-trail.png',
            'picto' => 'trail.png',
            'color' => '#94c022',
            'goutte' => 'ktrailcampagne.png',
            'category' => 'category_1'
        ],
        [
            'name' => 'Randonnée',
            'logo' => 'k-randonnee.png',
            'picto' => 'randonnee.png',
            'color' => '#94c022',
            'goutte' => 'krandonneecampagne.png',
            'category' => 'category_1'
        ],
        [
            'name' => 'VTT',
            'logo' => 'k-vtt.png',
            'picto' => 'vtt.png',
            'color' => '#94c022',
            'goutte' => 'kvttcampagne.png',
            'category' => 'category_1'
        ],
        [
            'name' => 'Cyclotourisme',
            'logo' => 'k-velo-de-course.png',
            'picto' => 'cyclotourisme.png',
            'color' => '#94c022',
            'goutte' => 'kvelodecoursecampagne.png',
            'category' => 'category_1'
        ],
        [
            'name' => 'Marche nordique',
            'logo' => 'k-marche-nordique.png',
            'picto' => 'marche-nordique.png',
            'color' => '#94c022',
            'goutte' => 'kmarchenordiquecampagne.png',
            'category' => 'category_1'
        ],
        [
            'name' => 'VTT de montagne',
            'logo' => 'kvttmontagne.png',
            'picto' => 'vtt-montagne.png',
            'color' => '#e71374',
            'goutte' => 'kvttmontagne.png',
            'category' => 'category_2'
        ],
        [
            'name' => 'Raquettes à neige',
            'logo' => 'kraquettemontagne.png',
            'picto' => 'raquette-montagne.png',
            'color' => '#e71374',
            'goutte' => 'kraquette.png',
            'category' => 'category_2'
        ],
        [
            'name' => 'Ski de fond',
            'logo' => 'k-ski-de-fond.png',
            'picto' => 'ski-de-fond.png',
            'color' => '#e71374',
            'goutte' => 'kskidefondmontagne.png',
            'category' => 'category_2'
        ],
        [
            'name' => 'Ski de randonnée',
            'logo' => 'k-ski-de-rando.png',
            'picto' => 'ski-de-rando.png',
            'color' => '#e71374',
            'goutte' => 'kskiderandomontagne.png',
            'category' => 'category_2'
        ],
        [
            'name' => 'Trail de montagne',
            'logo' => 'ktrailmontagne.png',
            'picto' => 'trail-montagne.png',
            'color' => '#e71374',
            'goutte' => 'krunningmontagne.png',
            'category' => 'category_2'
        ],
        [
            'name' => 'Randonnée de montagne',
            'logo' => 'krandomontagne.png',
            'picto' => 'randonee-montagne.png',
            'color' => '#e71374',
            'goutte' => 'krandonneemontagne.png',
            'category' => 'category_2'
        ],
        [
            'name' => 'Roller',
            'logo' => 'k-roller.png',
            'picto' => 'roller.png',
            'color' => '#f29100',
            'goutte' => 'krollerurbain.png',
            'category' => 'category_3'
        ],
        [
            'name' => 'Marche',
            'logo' => 'marcheurbain.png',
            'picto' => 'marche.png',
            'color' => '#f29100',
            'goutte' => 'kmarcheurbain.png',
            'category' => 'category_3'
        ],
        [
            'name' => 'Running',
            'logo' => 'k-running.png',
            'picto' => 'running.png',
            'color' => '#f29100',
            'goutte' => 'krunningurbain.png',
            'category' => 'category_3'
        ],
        [
            'name' => 'Vélo de route',
            'logo' => 'kvelodecourseurbain.png',
            'picto' => 'velo-de-route.png',
            'color' => '#f29100',
            'goutte' => 'kvelodecourseurbain.png',
            'category' => 'category_3'
        ],
        [
            'name' => 'Marche nordique urbaine',
            'logo' => 'kmarchenordiqueurbain.png',
            'picto' => 'marche-nordique-urbain.png',
            'color' => '#f29100',
            'goutte' => 'marchenordiqueurbain.png',
            'category' => 'category_3'
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::SPORTS as $index => $sportCategory) {
            $sport = new Sport();
            $sport
                ->setName($sportCategory['name'])
                ->setLogo($sportCategory['logo'])
                ->setPicto($sportCategory['picto'])
                ->setColor($sportCategory['color'])
                ->setGoutte($sportCategory['goutte'])
                ->setCategory($this->getReference($sportCategory['category']))
            ;
            $manager->persist($sport);
            $this->setReference(Sport::class . '_' . $index, $sport);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
}

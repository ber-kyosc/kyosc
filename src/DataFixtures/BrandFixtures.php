<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BrandFixtures extends Fixture
{
    private const BRANDS = ['Arc\'Teryx', 'Petzl', 'Therm a rest', 'Salomon', 'MSR', 'Osprey', 'Sea to Summit',
        'Deuter', 'Patagonia', 'Lowe Alpine', 'La sportiva', 'Lowa', 'Meindl', 'Hoka', 'Opinel', 'Au vieux campeur',
        'Expe', 'Snowleader', 'Ekosport', 'Alltricks', 'Blissports', 'Vertical', 'Raidlight', 'Scott', 'Lapierre',
        'Haibike', 'BMC', 'Cannondale', 'Hummel', 'Mizuno', 'New balance', 'Oakley', 'Colmar', 'Napapijri', 'Dynastar',
        'Atomic', 'Head', 'Burton', 'Vuarnet', 'Bollé', 'Picture organic clothing', 'Sunvalley', 'Cairn', 'K2',
        'Skidress', 'Mammut', 'Peak Performance', 'Ortovox', 'Salewa', 'Sidas', 'Suunto', 'Garmin', 'Fibit', 'Polar',
        'Garmont', 'Eider', 'Fusalp', 'Colombia', 'Cébé', 'Julbo', 'Nosc', 'Lagoped', 'Caur', 'Circle', 'Craft',
        'Gayaskin', 'Made Nature', 'Ogarum', 'Noret', 'Dimasport', 'Veets', 'TSL Outdoor', 'Zag', 'Blackcrow'
    ];

    public function load(ObjectManager $manager): void
    {
        $tab = self::BRANDS;
        sort($tab);
        foreach ($tab as $brandName) {
            $brand = new Brand();
            $brand->setName($brandName);
            $manager->persist($brand);
        }
        $manager->flush();
    }
}

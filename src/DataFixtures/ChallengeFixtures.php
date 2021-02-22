<?php

namespace App\DataFixtures;

use App\Entity\Challenge;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DateTime;

class ChallengeFixtures extends BaseFixtures implements DependentFixtureInterface
{
    private const SPORT_IMAGES = [
        'Canoé' => 'canoe.jpeg',
        'Paddle' => 'paddle.png',
        'Voile' => 'voile.jpeg',
        'Pirogue' => 'pirogue.jpg',
        'Nage' => 'nage.jpeg',
        'Trail' => 'trailcampagne.jpg',
        'Randonnée' => 'randonnee.png',
        'VTT' => 'vtt.jpeg',
        'Cyclotourisme' => 'cyclotourisme.jpeg',
        'Marche nordique' => 'marchenordiquemontagne.jpeg',
        'VTT de montagne' => 'vttmontagne.jpeg',
        'Raquettes à neige' => 'raquette.jpeg',
        'Ski de fond' => 'skidefond.jpeg',
        'Ski de randonnée' => 'skiderandonnee.jpg',
        'Trail de montagne' => 'trail.jpg',
        'Randonnée de montagne' => 'randomontagne.jpeg',
        'Roller' => 'roller.jpeg',
        'Marche' => 'marche.jpg',
        'Running' => 'running.jpeg',
        'Vélo de route' => 'veloroute.jpg',
        'Marche nordique urbaine' => 'marchenordiqueurbaine.jpg',
    ];
    public function load(ObjectManager $manager): void
    {
        parent::load($manager);
        $regex = '/Sport_\d+/';
        $refChallenge = 0;

        foreach (array_keys($this->referenceRepository->getReferences()) as $ref) {
            if (!preg_match($regex, $ref)) {
                continue;
            }
            $randNbChallenge = rand(17, 20);
            $this->createMany(Challenge::class, $randNbChallenge, function (Challenge $challenge) use ($ref) {
                $dateCreation = (new DateTime())->modify('- ' . rand(0, 30) . 'days');
                $dateStart = (new DateTime())->modify('+ ' . rand(0, 30) . 'days');
                $sport = $this->getReference($ref);
                $image = $this::SPORT_IMAGES[$sport->getName()];
                $challenge
                    ->setTitle($this->faker->sentence)
                    ->setQuotation($this->faker->sentence)
                    ->setLocation($this->faker->city)
                    ->setLocationEnd($this->faker->city)
                    ->setDistance($this->faker->randomDigitNotNull)
                    ->setInformation(join(' ', $this->faker->paragraphs(3)))
                    ->setJourney(join(' ', $this->faker->paragraphs(3)))
                    ->setCreatedAt($dateCreation)
                    ->setUpdatedAt($dateCreation)
                    ->setDateStart($dateStart)
                    ->setDescription(join(' ', $this->faker->paragraphs(3)))
                    ->setIsPublic('true')
                    ->addSport($sport)
                    ->setChallengePhoto($image)
                    ->setCreator($this->getReference('user'))
                ;
            }, $refChallenge);
            $refChallenge++;
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [SportFixtures::class];
    }
}

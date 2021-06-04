<?php

namespace App\Controller;

use App\Repository\ChallengeRepository;
use App\Repository\ClanRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ChallengeRepository $challRepo
     * @param UserRepository $userRepo
     * @param ClanRepository $clanRepo
     * @return Response
     */
    public function index(ChallengeRepository $challRepo, UserRepository $userRepo, ClanRepository $clanRepo): Response
    {
        $totalChallengesWanted = 6;
        $featuredChallenges = $challRepo->findBy(['isFeatured' => true]);
        shuffle($featuredChallenges);
        if (count($featuredChallenges) > $totalChallengesWanted) {
            $featuredChallenges = array_slice($featuredChallenges, 0, $totalChallengesWanted);
        } elseif (!$featuredChallenges) {
            $featuredChallenges = $challRepo->findAll();
            shuffle($featuredChallenges);
            if (count($featuredChallenges) > $totalChallengesWanted) {
                $featuredChallenges = array_slice($featuredChallenges, 0, $totalChallengesWanted);
            }
        }

        if (count($featuredChallenges) < $totalChallengesWanted) {
            $loops = $totalChallengesWanted - count($featuredChallenges);
            for ($i = 0; $i < $loops; $i++) {
                $randomImage = 'build/images/background/bgimg' . rand(1, 11) . '.jpeg';
                $featuredChallenges[] = [
                    'id' => false,
                    'title' => '',
                    'challengePhoto' => $randomImage,
                ];
            }
        }

        $featuredChallenges[] = [
            'id' => 'allChallenges',
        ];
        $featuredChallenges[] = [
            'id' => 'challengeNew',
        ];
        shuffle($featuredChallenges);

        $totalClansWanted = 6;
        $clans = $clanRepo->findBy(['isPublic' => true]);
        shuffle($clans);
        if (count($clans) > $totalClansWanted) {
            $clans = array_slice($clans, 0, $totalClansWanted);
        } elseif (!$clans) {
            $clans = $challRepo->findAll();
            shuffle($clans);
            if (count($clans) > $totalClansWanted) {
                $clans = array_slice($clans, 0, $totalClansWanted);
            }
        }

        if (count($clans) < $totalClansWanted) {
            $loops = $totalClansWanted - count($clans);
            for ($i = 0; $i < $loops; $i++) {
                $randomImage = 'build/images/background/bgimg' . rand(1, 11) . '.jpeg';
                $clans[] = [
                    'id' => false,
                    'name' => '',
                    'banner' => $randomImage,
                ];
            }
        }

        $clans[] = [
            'id' => 'allClans',
        ];
        $clans[] = [
            'id' => 'clanNew',
        ];
        shuffle($clans);


        $usersTestimonials = $userRepo->findAllNotNullTestimony();
        shuffle($usersTestimonials);
        if (count($usersTestimonials) > 3) {
            $usersTestimonials = array_slice($usersTestimonials, 0, 3);
        }
        $imagesCarousel = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('default/index.html.twig', [
            'featuredChallenges' => $featuredChallenges,
            'usersTestimonials' => $usersTestimonials,
            'imagesCarousel' => $imagesCarousel,
            'clans' => $clans,
        ]);
    }

    /**
     * @Route("/a-propos", name="about_us")
     */
    public function aboutUs(): Response
    {
//        return $this->render('about/aboutUs.html.twig');
        return $this->render('about/workInProgress.html.twig');
    }

    /**
     * @Route("/kyosc", name="about_kyosc")
     */
    public function aboutKyosc(): Response
    {
//        return $this->render('about/aboutKyosc.html.twig');
        return $this->render('about/workInProgress.html.twig');
    }

    /**
     * @Route("/aide-challenge", name="about_challenge")
     */
    public function aboutChallenge(): Response
    {
//        return $this->render('about/aboutChallenge.html.twig');
        return $this->render('about/workInProgress.html.twig');
    }

    /**
     * @Route("/experience", name="gamification")
     */
    public function aboutExperience(): Response
    {
//        return $this->render('about/gamification.html.twig');
        return $this->render('about/workInProgress.html.twig');
    }

    /**
     * @Route("/cgu", name="cgu")
     */
    public function cgu(): Response
    {
        return $this->render('about/cgu.html.twig');
    }

    /**
     * @Route("/aide", name="faq")
     */
    public function faq(): Response
    {
        return $this->render('about/faq.html.twig');
    }

    /**
     * @Route("/bien-commencer", name="get-started")
     */
    public function getStarted(): Response
    {
        return $this->render('about/getStarted.html.twig');
    }
}

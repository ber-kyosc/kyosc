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
        $featuredChallenges = $challRepo->findBy(['isFeatured' => true]);
        shuffle($featuredChallenges);
        if (count($featuredChallenges) > 4) {
            $featuredChallenges = array_slice($featuredChallenges, 0, 4);
        }
        if ($featuredChallenges) {
            $topChallenge = $featuredChallenges[array_rand($featuredChallenges)];
        } else {
            $topChallenges = $challRepo->findAll();
            krsort($topChallenges);
            $topChallenge = $topChallenges ? $topChallenges[0] : [];
        }

        $clans = $clanRepo->findBy(['isPublic' => true]);
        shuffle($clans);
        if (count($clans) > 4) {
            $clans = array_slice($clans, 0, 4);
        }
        if ($clans) {
            $topClan = $clans[array_rand($clans)];
        } else {
            $topClan = $challRepo->findAll();
            krsort($topClan);
            $topClan = $topClan ? $topClan[0] : [];
        }

        $usersTestimonials = $userRepo->findAllNotNullTestimony();
        shuffle($usersTestimonials);
        if (count($usersTestimonials) > 3) {
            $usersTestimonials = array_slice($usersTestimonials, 0, 3);
        }
        $imagesCarousel = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('default/index.html.twig', [
            'featuredChallenges' => $featuredChallenges,
            'topChallenge' => $topChallenge,
            'usersTestimonials' => $usersTestimonials,
            'imagesCarousel' => $imagesCarousel,
            'clans' => $clans,
            'topClan' => $topClan,
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
     * @Route("/faq", name="faq")
     */
    public function faq(): Response
    {
//        return $this->render('about/faq.html.twig');
        return $this->render('about/workInProgress.html.twig');
    }
}

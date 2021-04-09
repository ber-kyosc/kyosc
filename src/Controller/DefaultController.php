<?php

namespace App\Controller;

use App\Repository\ChallengeRepository;
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
     * @return Response
     */
    public function index(ChallengeRepository $challRepo, UserRepository $userRepo): Response
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
        ]);
    }

    /**
     * @Route("/a-propos", name="about_us")
     */
    public function aboutUs(): Response
    {
        return $this->render('default/aboutUs.html.twig');
    }
}

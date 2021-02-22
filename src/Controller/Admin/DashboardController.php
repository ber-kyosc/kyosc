<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Challenge;
use App\Entity\Sport;
use App\Entity\User;
use App\Entity\Brand;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ChallengeRepository;
use App\Repository\SportRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private CategoryRepository $categoryRepository;
    private ChallengeRepository $challengeRepository;
    private SportRepository $sportRepository;
    private UserRepository $userRepository;
    private BrandRepository $brandRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        ChallengeRepository $challengeRepository,
        SportRepository $sportRepository,
        UserRepository $userRepository,
        BrandRepository $brandRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->challengeRepository = $challengeRepository;
        $this->sportRepository = $sportRepository;
        $this->userRepository = $userRepository;
        $this->brandRepository = $brandRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundle/welcome.html.twig', [
            'totalChallenges' => $this->challengeRepository->findAll(),
            'totalCategories' => $this->categoryRepository->findAll(),
            'totalUsers' => $this->userRepository->findAll(),
            'totalSports' => $this->sportRepository->findAll(),
            'challengeBySports' => $this->challengeRepository->getChallengeBySports(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<a href="/">KYOSC <img src="/build/images/favicon.51a420e4.png" style="width: 40px"> </a>')
            ->setFaviconPath('/build/images/favicon.51a420e4.png');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),
            MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class),
            MenuItem::linkToCrud('Challenges', 'fas fa-trophy', Challenge::class),
            MenuItem::linkToCrud('Sports', 'fas fa-quidditch', Sport::class),
            MenuItem::linkToCrud('Catégories', 'fas fa-book', Category::class),
            MenuItem::linkToCrud('Marques', 'fas fa-copyright', Brand::class)
        ];
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}

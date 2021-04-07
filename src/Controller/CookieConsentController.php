<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace App\Controller;

use ConnectHolland\CookieConsentBundle\Cookie\CookieChecker;
use App\Form\CookieConsentType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use ConnectHolland\CookieConsentBundle\Controller\CookieConsentController as BaseController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CookieConsentController extends BaseController
{
    /**
     * @var Environment
     */
    protected Environment $twigEnvironment;

    /**
     * @var FormFactoryInterface
     */
    protected FormFactoryInterface $formFactory;

    /**
     * @var CookieChecker
     */
    protected CookieChecker $cookieChecker;

    /**
     * @var string
     */
    protected string $cookConsTheme = 'dark';

    /**
     * @var string
     */
    protected string $cookConsPosition = 'bottom';

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var bool
     */
    protected bool $cookConsSimplified = true;

    public function __construct(
        Environment $twigEnvironment,
        FormFactoryInterface $formFactory,
        CookieChecker $cookieChecker,
        TranslatorInterface $translator
    ) {
        $this->twigEnvironment         = $twigEnvironment;
        $this->formFactory             = $formFactory;
        $this->cookieChecker           = $cookieChecker;
        $this->translator              = $translator;
    }

    /**
     * Show cookie consent.
     *
     * @Route("/cookie_consent", name="ch_cookie_consent.show")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Request $request): Response
    {
        $this->setLocale($request);

        return new Response(
            $this->twigEnvironment->render('bundles/CHCookieConsentBundle/cookie_consent.html.twig', [
                'form'       => $this->createCookieConsentForm()->createView(),
                'theme'      => $this->cookConsTheme,
                'position'   => $this->cookConsPosition,
                'simplified' => $this->cookConsSimplified,
            ])
        );
    }

    /**
     * Show cookie consent.
     *
     * @Route("/cookie_consent_alt", name="ch_cookie_consent.show_if_cookie_consent_not_set")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showIfCookieConsentNotSet(Request $request): Response
    {
        if ($this->cookieChecker->isCookieConsentSavedByUser() === false) {
            return $this->show($request);
        }

        return new Response();
    }

    /**
     * Create cookie consent form.
     */
    protected function createCookieConsentForm(): FormInterface
    {
        return $this->formFactory->create(CookieConsentType::class);
    }

    /**
     * Set locale if available as GET parameter.
     * @param Request $request
     */
    protected function setLocale(Request $request): void
    {
        $locale = $request->get('locale');
        if (empty($locale) === false) {
            /* @phpstan-ignore-next-line */
            $this->translator->setLocale($locale);
            $request->setLocale($locale);
        }
    }

    /**
     * @Route ("/cookies", name="about_cookies")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function about(Request $request): Response
    {
        $this->setLocale($request);

        return new Response(
            $this->twigEnvironment->render('cookies/about_cookies.html.twig', [
                'form'       => $this->createCookieConsentForm()->createView(),
                'theme'      => $this->cookConsTheme,
                'position'   => $this->cookConsPosition,
                'simplified' => $this->cookConsSimplified,
            ])
        );
    }
}

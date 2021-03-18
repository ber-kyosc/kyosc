<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace App\Controller;

use ConnectHolland\CookieConsentBundle\Cookie\CookieChecker;
use ConnectHolland\CookieConsentBundle\Form\CookieConsentType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CookieConsentController
{
    /**
     * @var Environment
     */
    private Environment $twigEnvironment;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var CookieChecker
     */
    private CookieChecker $cookieChecker;

    /**
     * @var string
     */
    private string $cookieTheme;

    /**
     * @var string
     */
    private string $cookiePosition;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var bool
     */
    private bool $cookieSimplified = false;

    public function __construct(
        Environment $twigEnvironment,
        FormFactoryInterface $formFactory,
        CookieChecker $cookieChecker,
        TranslatorInterface $translator,
        bool $cookieSimplified,
        string $cookieTheme = "dark",
        string $cookiePosition = "bottom"
    ) {
        $this->twigEnvironment         = $twigEnvironment;
        $this->formFactory             = $formFactory;
        $this->cookieChecker           = $cookieChecker;
        $this->cookieTheme      = $cookieTheme;
        $this->cookiePosition   = $cookiePosition;
        $this->translator              = $translator;
        $this->cookieSimplified = $cookieSimplified;
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
            $this->twigEnvironment->render('@CHCookieConsent/cookie_consent.html.twig', [
                'form'       => $this->createCookieConsentForm()->createView(),
                'theme'      => $this->cookieTheme,
                'position'   => $this->cookiePosition,
                'simplified' => $this->cookieSimplified,
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
            $this->translator->trans('', [], null, $locale);
            $request->setLocale($locale);
        }
    }
}

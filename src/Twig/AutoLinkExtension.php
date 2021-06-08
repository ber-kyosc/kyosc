<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class AutoLinkExtension
 *
 * @package App\Twig
 */
class AutoLinkExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('auto_link', [$this, 'autoLink'], [
            'pre_escape' => 'html',
            'is_safe' => ['html']])];
    }

    public static function autoLink(string $string): ?string
    {
        $pattern = "/http[s]?:\/\/[a-zA-Z0-9.\-\/?#=&]+/";
        $replacement = "<a href=\"$0\" target=\"_blank\">$0</a>";
        $string = preg_replace($pattern, $replacement, $string);
        return $string;
    }
}

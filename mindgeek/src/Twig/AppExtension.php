<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('reverseCamelCase', [$this, 'reverseCamelCase'])
        ];
    }

    public function reverseCamelCase($camelCaseString)
    {
        $pattern = '/(([A-Z]{1}))/';
        return preg_replace_callback(
            $pattern,
            function ($matches) {
                return " " . $matches[0];
            },
            $camelCaseString
        );
    }
}

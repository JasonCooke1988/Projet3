<?php
namespace App\Controller\Extension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
/**
 * Class PhpMvcTwigExtension
 * Adds features to Twig Views
 * @package Pam\View
 */
class PhpMvcExtension extends AbstractExtension
{
    /**
     * Adds functions to Twig Views
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('url', array($this, 'url'))
        );
    }
    /**
     * Returns the Page URL
     */
    public function url(string $page, array $params = [])
    {
        $params['access'] = $page;
        return 'index.php?' . http_build_query($params);
    }
}
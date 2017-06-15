<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 15.06.2017
 * Time: 13:22
 */

namespace Pluetzner\BlockBundle\Twig;

use Twig_Extension;
use Twig_SimpleFilter;
/**
 * IconExtension
 *
 *
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @author     Sven Joder <sj@pmdevelopment.de>
 *
 * @copyright  2012-2013 Florian Eckerstorfer
 * @copyright  2013-2017 Sven Joder
 *
 * @license    http://opensource.org/licenses/MIT The MIT License
 *
 * @link       http://bootstrap.braincrafted.com Bootstrap for Symfony2
 * @link       http://www.pmdevelopment.de
 */
class IconExtension extends Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter(
                'parse_icons',
                [
                    $this,
                    'parseIconsFilter',
                ],
                [
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            new Twig_SimpleFilter(
                'icon',
                [
                    $this,
                    'iconFilter',
                ],
                [
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
            new Twig_SimpleFilter(
                'icon_custom',
                [
                    $this,
                    'iconCustomFilter',
                ],
                [
                    'is_safe' => [
                        'html',
                    ],
                ]
            ),
        ];
    }
    /**
     * Parses the given string and replaces all occurrences of .icon-[name] with the corresponding icon.
     *
     * @param string $text  The text to parse
     * @param string $color The color of the icon; can be 'black' or 'white'; defaults to 'black'
     *
     * @return string The HTML code with the icons
     */
    public function parseIconsFilter($text, $color = 'black')
    {
        $that = $this;
        return preg_replace_callback(
            '/\.icon-([a-z0-9-]+)/',
            function ($matches) use ($color, $that) {
                return $that->iconFilter($matches[1], $color);
            },
            $text
        );
    }
    /**
     * Returns the HTML code for the given icon.
     *
     * @param string $icon  The name of the icon
     * @param string $color The color of the icon; can be 'black' or 'white'; defaults to 'black'
     *
     * @return string The HTML code for the icon
     */
    public function iconFilter($icon, $color = 'black')
    {
        return sprintf('<i class="fa %sfa-%s" aria-hidden="true"></i>', $color == 'white' ? 'icon-white ' : '', $icon);
    }
    /**
     * Returns the HTML code for the given icon using a custom prefix
     *
     * @param string $icon
     * @param string $prefix
     *
     * @return string
     */
    public function iconCustomFilter($icon, $prefix = 'icon-')
    {
        return sprintf('<i class="%s%s" aria-hidden="true"></i>', $prefix, $icon);
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'twig.icon';
    }
}
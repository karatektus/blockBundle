<?php

namespace Pluetzner\BlockBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConfigureAdminMenuEvent
 * @package Pluetzner\BlockBundle\Event
 */
class ConfigureAdminMenuEvent extends Event
{
    const CONFIGURE = 'pluetzner.adminmenu_configure';

    private $factory;
    private $menu;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
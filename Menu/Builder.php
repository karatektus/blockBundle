<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 13.06.2016
 * Time: 15:51
 */

namespace Pluetzner\BlockBundle\Menu;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Knp\Menu\FactoryInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Builder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage, Registry $doctrine)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->doctrine = $doctrine;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return AuthorizationChecker
     */
    public function getAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }

    /**
     * @return TokenStorage
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @return Registry
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }


    /**
     * @param MatcherInterface $matcher
     * @return \Knp\Menu\ItemInterface
     */
    public function navMenu(MatcherInterface $matcher)
    {
        $username = $this->getTokenStorage()->getToken()->getUser()->getUsername();
        $menu = $this->getFactory()->createItem('root');
        $usermenu = $menu->addChild($username)
            ->setAttributes([
                'class' => 'dropdown dropdown-dark dropdown-user'
            ])
            ->setChildrenAttributes([
                'class' => 'dropdown-menu-default',
            ]);
        $menu[$username]->setLinkAttribute('data-hover', 'dropdown')->setLinkAttribute('data-toggle', 'dropdown')->setLinkAttribute('data-close-others', 'true');
        $usermenu->addChild('Profil', array('route' => 'pluetzner_block_user_show_1'));
        $usermenu->addChild('Logout', array('route' => 'fos_user_security_logout'));
        if ($this->getAuthorizationChecker()->isGranted("ROLE_ADMIN")) {
            $usermenu->addChild('Benutzerübersicht', array('route'=> 'pluetzner_block_user_index'));
        }
        return $menu;
    }

    /**
     * @param MatcherInterface $matcher
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(MatcherInterface $matcher)
    {
        $menu = $this->getFactory()->createItem('root');
        //$mainMenu = $menu->addChild("Projekte", array('route' => 'pm_core_project_index'));

        //$categoryMenu = $menu->addChild("Kategorien", array('route' => 'pm_core_category_index'));
        if ($this->getAuthorizationChecker()->isGranted("ROLE_ADMIN_DEVELOPER")) {
            $textBlockMenu = $menu->addChild("Textblöcke", ['route' => 'pluetzner_block_textblock_index']);
            $imageBlockMenu = $menu->addChild("Bildblöcke", ['route' => 'pluetzner_block_imageblock_index']);
        }
        return $menu;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: pluetzner
 * Date: 12.06.2017
 * Time: 16:43
 */

namespace Pluetzner\BlockBundle\Controller;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package Pluetzner\BlockBundle\Controller
 *
 * @Route("/admin/")
 */
class DefaultController extends Controller {

    /**
     * @Route("")
     *
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }
}
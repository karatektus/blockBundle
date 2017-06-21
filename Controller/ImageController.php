<?php

namespace Pluetzner\BlockBundle\Controller;

use Pluetzner\BlockBundle\Entity\ImageBlock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    /**
     * @Route(
     *     "/images/{slug}/{height}x{width}.{_type}",
     *     defaults={"_format": "jpg", "height": 0, "width": 0},
     *     )
     *
     * @param string $slug
     * @param int $height
     * @param int $width
     *
     * @return Response
     */
    public function showAction($slug, $height = 0, $width = 0, $_type)
    {
        //just needed the route :o
        throw $this->createNotFoundException();
    }
}

<?php

namespace Pluetzner\BlockBundle\Controller;

use Pluetzner\BlockBundle\Entity\ImageBlock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @param int    $height
     * @param int    $width
     *
     */
    public function showAction(Request $request, $slug, $height = 0, $width = 0, $_type)
    {
        $imageBlock = $this->getDoctrine()->getRepository('PluetznerBlockBundle:ImageBlock')->findOneBy(['slug' => $slug]);
        if (null === $imageBlock) {
            throw $this->createNotFoundException();
        }

        $imagePath = sprintf('%s/../web%s', $this->getParameter('kernel.root_dir'), $request->getRequestUri());
        if (false === file_exists($imagePath)) {
            if ('image/svg+xml' === $imageBlock->getMimeType()) {
                if (!file_exists(dirname($imagePath))) {
                    mkdir(dirname($imagePath), 0755, true);
                }
                file_put_contents($imagePath, base64_decode($imageBlock->getImage()));
            } else {
                $this->get('pluetzner_block.services.image_service')->resizeImage(imagecreatefromstring(base64_decode($imageBlock->getImage())), $imageBlock->getMimeType(), $width, $height, $imagePath);
            }
        }

        return $this->redirect($request->getRequestUri());
    }
}

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
     *     "/images/{height}/{width}/{slug}.{_type}",
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
        $imageBlock = $this->getDoctrine()->getRepository(ImageBlock::class)->findOneBy(['slug' => $slug, 'deleted' => false]);

        if(null === $imageBlock){
            throw $this->createNotFoundException();
        }
        // replace this example code with whatever you need

        $data = base64_decode($imageBlock->getImage());
        $response = new Response();

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', $imageBlock->getMimeType() );
        $response->headers->set('Content-Disposition', 'inline; filename="' . $imageBlock->getSlug() . $_type .'";');
        $response->headers->set('Content-length',  strlen($data));
        $response->sendHeaders();

        $response->setContent($data);

        return $response;
    }
}

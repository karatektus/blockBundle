<?php

namespace Pluetzner\BlockBundle\Controller;

use Pluetzner\BlockBundle\Entity\ImageBlock;
use Pluetzner\BlockBundle\Form\ImageBlockFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ImageBlockController
 * @package Pluetzner\BlockBundle\Controller
 *
 * @Route("/admin/imageblocks")
 */
class ImageBlockController extends Controller
{
    /**
     * @Route("")
     *
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $imageblocks = $this->getDoctrine()->getRepository(ImageBlock::class)->findBy(['deleted' => false]);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $imageblocks,
            $request->get('page', 1),
            30
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/create")
     * @Route("/{id}/edit")
     * @Template()
     *
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id = 0)
    {
        if (0 < $id) {
            $imageblock = $this->getDoctrine()->getRepository(ImageBlock::class)->find(intval($id));
            if (null === $imageblock) {
                throw $this->createNotFoundException();
            }
        } else {
            $imageblock = new ImageBlock();
        }

        $form = $this->createForm(ImageBlockFormType::class, $imageblock);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($imageblock);
            $manager->flush();
            $this->get('session')->getFlashBag()->add('success', 'Imageblock erfolgreich gespeichert');

            return $this->redirect($this->generateUrl('pluetzner_block_imageblock_index'));
        }
        return [
            'form' => $form->createView(),
            'imageblock' => $imageblock,
        ];
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/{id}/editAjax")
     * @Template()
     *
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function editAjaxAction(Request $request, $id)
    {
        $imageblock = $this->getDoctrine()->getRepository(ImageBlock::class)->find(intval($id));
        if (null === $imageblock) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ImageBlockFormType::class, $imageblock, ['save_button' => false]);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {

            /** @var UploadedFile $fileRef */
            $fileRef = $imageblock->getUploadedFile();
            $file = file_get_contents($fileRef->getPath() . "/" . $fileRef->getFilename());
            $file = base64_encode($file);

            $imageblock
                ->setMimeType($fileRef->getMimeType())
                ->setImage($file);


            $manager = $this->getDoctrine()->getManager();
            $manager->persist($imageblock);
            $manager->flush();

            $guesser = new MimeTypeExtensionGuesser();
            $imageRoute = $this->get('router')->generate('pluetzner_block_image_show', [
                'slug' => $imageblock->getSlug(),
                'height' => 0,
                'width' => 0,
                '_type' => $guesser->guess($imageblock->getMimeType())

            ]);

            $dir = dirname(sprintf('%s/../web%s', $this->get('kernel')->getRootDir(), $imageRoute));
            $files = scandir($dir);
            foreach($files as $file){
                $fp = sprintf('%s/%s', $dir, $file);
                if(is_file($fp)) {
                    $res = unlink($fp); //delete file
                }
            }

            return new Response("");
        }



        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param int $id
     *
     * @Route("/{id}/delete")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        $imageblock = $this->getDoctrine()->getRepository(ImageBlock::class)->find(intval($id));
        if (null === $imageblock || true === $imageblock->isDeleted()) {
            throw $this->createNotFoundException();
        }

        $imageblock->setDeleted(true);

        $manager = $this->getDoctrine()->getManager();

        $manager->persist($imageblock);
        $manager->flush();

        $this->get('session')->getFlashBag()->add('success', 'Imageblock erfolgreich gelöscht');

        return $this->redirect($this->generateUrl('pluetzner_block_imageblock_index'));
    }
}

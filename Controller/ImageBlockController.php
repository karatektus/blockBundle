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
        $imageblocks = $this->getDoctrine()->getRepository(ImageBlock::class)->findBy(['deleted' => false, 'entityBlock' => null]);

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

            $imageService = $this->get('pluetzner_block.services.image_service');
            $imageService->saveImage($imageblock);

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

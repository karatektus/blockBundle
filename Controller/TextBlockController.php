<?php

namespace Pluetzner\BlockBundle\Controller;

use Pluetzner\BlockBundle\Entity\TextBlock;
use Pluetzner\BlockBundle\Form\TextBlockFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TextBlockController
 * @package Pluetzner\BlockBundle\Controller
 *
 * @Route("/admin/textblocks")
 */
class TextBlockController extends Controller
{
    /**
     * @Route("")
     *
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $textblocks = $this->getDoctrine()->getRepository(TextBlock::class)->findBy(['deleted' => false, 'entityBlock' => null]);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $textblocks,
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
            $textblock = $this->getDoctrine()->getRepository(TextBlock::class)->find(intval($id));
            if (null === $textblock) {
                throw $this->createNotFoundException();
            }
        } else {
            $textblock = new TextBlock();
        }

        $form = $this->createForm(TextBlockFormType::class, $textblock);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($textblock);
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Textblock erfolgreich gespeichert.');

            return $this->redirect($this->generateUrl('pluetzner_block_textblock_index'));
        }
        return [
            'form' => $form->createView(),
            'textblock' => $textblock,
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
        $textblock = $this->getDoctrine()->getRepository(TextBlock::class)->find(intval($id));
        if (null === $textblock) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(TextBlockFormType::class, $textblock, ['save_button' => false]);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($textblock);
            $manager->flush();

            return new Response($textblock->getText());
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
        $textblock = $this->getDoctrine()->getRepository(TextBlock::class)->find(intval($id));
        if (null === $textblock || true === $textblock->isDeleted()) {
            throw $this->createNotFoundException();
        }

        $textblock->setDeleted(true);

        $manager = $this->getDoctrine()->getManager();

        $manager->persist($textblock);
        $manager->flush();

        $this->get('session')->getFlashBag()->add('success', 'Textblock erfolgreich gelöscht.');

        return $this->redirect($this->generateUrl('pluetzner_block_textblock_index'));
    }
}

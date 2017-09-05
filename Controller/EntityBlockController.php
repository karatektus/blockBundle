<?php

namespace Pluetzner\BlockBundle\Controller;

use Pluetzner\BlockBundle\Entity\EntityBlock;
use Pluetzner\BlockBundle\Entity\EntityBlockType;
use Pluetzner\BlockBundle\Entity\TextBlock;
use Pluetzner\BlockBundle\Form\EntityBlockFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EntityBlockController
 * @package Pluetzner\BlockBundle\Controller
 *
 * @Route("/admin/entityblocks/{type}")
 */
class EntityBlockController extends Controller
{

    /**
     * @param Request $request
     * @param string  $type
     *
     * @return array
     *
     * @Route("")
     * @Template()
     */
    public function indexAction(Request $request, $type)
    {
        $entityType = $this->getDoctrine()->getRepository(EntityBlockType::class)->findOneBy(['slug' => $type]);

        if (null === $entityType) {
            throw $this->createNotFoundException();
        }

        $blocks = $entityType->getEntityBlocks();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $blocks,
            $request->get('page', 1),
            30
        );

        return [
            'type' => $entityType,
            'blocks' => $pagination,
        ];
    }

    /**
     * @param Request $request
     * @param string  $type
     * @param int     $id
     *
     * @return array|Response
     *
     * @Route("/{id}/editAjax")
     *
     * @Template()
     */
    public function editAjaxAction(Request $request, $type, $id = 0)
    {
        if (0 === intval($id)) {
            $entityType = $this->getDoctrine()->getRepository(EntityBlockType::class)->findOneBy(['slug' => $type]);

            if (null === $entityType) {
                throw $this->createNotFoundException();
            }

            $entityBlock = new EntityBlock();
            $entityBlock->setEntityBlockType($entityType);
        } else {
            $entityBlock = $this->getDoctrine()->getRepository(EntityBlock::class)->createQueryBuilder('e')
                ->where('e.id = :id')
                ->andWhere('e.slug = :slug')
                ->setParameters([
                    'id' => $id,
                    'slug' => $type,
                ])
                ->getQuery()
                ->getSingleResult();

        }

        if (null === $entityBlock) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(EntityBlockFormType::class, $entityBlock, ['save_button' => false]);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($entityBlock);
            $manager->flush();

            return new Response("");
        }

        return [
            'form' => $form->createView(),
            'locales' => $this->getParameter('pl__block.configuration.locales_available')
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

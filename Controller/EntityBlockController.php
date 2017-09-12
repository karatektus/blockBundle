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

        $blocks = $this->getDoctrine()->getRepository(EntityBlock::class)->findAllUndeleted($entityType);

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

            $count = $this
                ->getDoctrine()
                ->getRepository(EntityBlock::class)
                ->createQueryBuilder('eb')
                ->select('COUNT(eb)')
                ->join('eb.entityBlockType', 'type')
                ->where('type.slug = :type')
                ->setParameter('type', $type)
                ->andWhere('eb.deleted = 0')
                ->getQuery()
                ->getSingleScalarResult();

            $entityBlock = new EntityBlock();
            $entityBlock
                ->setEntityBlockType($entityType)
                ->setCount($count + 1);

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
     * @param string  $type
     * @param Request $request
     *
     * @Route("/updateOrder")
     *
     * @return Response
     */
    public function updateOrderAction(Request $request, $type)
    {
        $entityType = $this->getDoctrine()->getRepository(EntityBlockType::class)->findOneBy(['slug' => $type]);

        $orderData = $request->get('orderData');
        $orderData = array_flip($orderData);
        if (null === $entityType) {
            throw $this->createNotFoundException();
        }

        $entityBlocks = $this->getDoctrine()->getRepository(EntityBlock::class)->findAllUndeleted($entityType);

        foreach ($entityBlocks as $entityBlock) {
            $entityBlock->setOrderId($orderData[$entityBlock->getSlug()]);
            $this->getDoctrine()->getManager()->persist($entityBlock);
        }
        $this->getDoctrine()->getManager()->flush();

        return new Response(implode(' & ', $orderData), 200);
    }

    /**
     * @param int $id
     *
     * @Route("/{id}/delete")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($type, $id = 0)
    {
        $entityBlock = $this->getDoctrine()->getRepository(EntityBlock::class)->find(intval($id));
        if (null === $entityBlock || true === $entityBlock->isDeleted()) {
            throw $this->createNotFoundException();
        }

        $entityBlock->setDeleted(true);

        $manager = $this->getDoctrine()->getManager();

        $manager->persist($entityBlock);
        $manager->flush();

        $this->get('session')->getFlashBag()->add('success', 'EntityBlock successfully deleted.');

        return $this->redirect($this->generateUrl('pluetzner_block_entityblock_index', ['type' => $type]));
    }
}

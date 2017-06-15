<?php

namespace Pluetzner\BlockBundle\Controller;

use Pluetzner\BlockBundle\Entity\User;
use Pluetzner\BlockBundle\Form\PasswordFormType;
use Pluetzner\BlockBundle\Form\UserFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package Pluetzner\BlockBundle\Controller
 *
 * @Route("/admin/user")
 */
class UserController extends Controller
{
    /**
     * Index - List all Users
     *
     * @param Request $request
     * @return array
     * @Route("")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $checker = $this->get('security.authorization_checker');
        $qb = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder("u");
        $qb
            ->where("u.enabled = 1");

        $users = $qb->getQuery()->getResult();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $request->get('page', 1),
            10
        ); //findBy([], ["name" => "ASC"]);

        return ["pagination" => $pagination];
    }

    /**
     * Detail
     *
     * @Route("/{id}/show")
     * @Route("/my-account")
     *
     * @Template()
     *
     * @param int $id
     * @return array
     */
    public function showAction($id = 0)
    {
        if (0 === $id) {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getRepository("PMCoreBundle:User")->find(intval($id));
        }

        return [
            "user" => $user,
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
     * @return array
     */
    public function editAction(Request $request, $id = 0)
    {
        $checker = $this->get('security.authorization_checker');
        if (0 < $id) {
            $user = $this->getDoctrine()->getRepository("PMCoreBundle:User")->find(intval($id));
            if (null === $user) {
                throw $this->createNotFoundException();
            }
        } else {
            $user = new User();
        }

        if (false === $checker->isGranted('ROLE_ADMIN')) {
            $user->setCompany($this->getUser()->getCompany());
        }

        $form = $this->createForm(UserFormType::class, $user, ['user' => $this->getUser(), 'checker' => $checker]);
        $form->handleRequest($request);

        if (true === $form->isValid()) {
            $check = null;
            if ($user->getId() === null) {
                $check = $this->getDoctrine()->getRepository("PMCoreBundle:User")->findOneBy(['usernameCanonical' => $user->getUsernameCanonical()]);
            }
            if (null === $check) {
                $manager = $this->getDoctrine()->getManager();

                $manager->persist($user);
                $manager->flush();

                return $this->saved("admin_user_index");
            }

            $form->addError(new FormError("Der Benutzername existiert bereits"));
        }

        return [
            "form" => $form->createView(),
            "user" => $user
        ];
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/change-password")
     * @Template()
     *
     * @return array
     */
    public function editPasswordAction(Request $request)
    {

        $checker = $this->get('security.authorization_checker');
        $user = $this->getDoctrine()->getRepository(User::class)->find(intval($this->getUser()->getId()));

        if (null === $user || false === $user->isEnabled()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PasswordFormType::class, $user);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $check = null;
            if ($user->getId() === null) {
                $check = $this->getDoctrine()->getRepository("PMCoreBundle:User")->findOneBy(['usernameCanonical' => $user->getUsernameCanonical()]);
            }
            if (null === $check) {
                $manager = $this->getDoctrine()->getManager();

                $manager->persist($user);
                $manager->flush();

                return $this->saved("admin_user_show_1");
            }

            $form->addError(new FormError("Der Benutzername existiert bereits"));
        }

        return [
            "form" => $form->createView(),
            "user" => $user
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
        $user = $this->getDoctrine()->getRepository("PMCoreBundle:User")->find(intval($id));
        if (null === $user || true === $user->isExpired()) {
            throw $this->createNotFoundException();
        }

        $user->setExpired(true);

        $manager = $this->getDoctrine()->getManager();

        $manager->persist($user);
        $manager->flush();

        return $this->saved("pm_core_default_index");
    }

}
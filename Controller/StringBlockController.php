<?php

namespace Pluetzner\BlockBundle\Controller;

use Gedmo\Translatable\Entity\Translation;
use Pluetzner\BlockBundle\Entity\Export;
use Pluetzner\BlockBundle\Entity\Import;
use Pluetzner\BlockBundle\Entity\StringBlock;
use Pluetzner\BlockBundle\Entity\TextBlock;
use Pluetzner\BlockBundle\Form\ImportFormType;
use Pluetzner\BlockBundle\Form\ImportLanguageSelectFormType;
use Pluetzner\BlockBundle\Form\StringBlockFormType;
use Pluetzner\BlockBundle\Form\TextBlockFormType;
use Pluetzner\BlockBundle\Model\ImportLanguageSelectModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class TextBlockController
 * @package Pluetzner\BlockBundle\Controller
 *
 * @Route("/admin/stringblocks")
 */
class StringBlockController extends Controller
{
    /**
     * @Route("")
     *
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $textblocks = $this->getDoctrine()->getRepository(StringBlock::class)->findBy(['deleted' => false, 'entityBlock' => null]);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $textblocks,
            $request->get('page', 1),
            30
        );

        $import = new Import();
        $form = $this->createForm(ImportFormType::class, $import, [
            'action' => $this->generateUrl('pluetzner_block_stringblock_import'),
        ]);

        $result = [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ];

        $result['export'] = key_exists('LiuggioExcelBundle', $this->getParameter('kernel.bundles'));

        return $result;
    }

    /**
     * @Route("/export")
     */
    public function exportAction()
    {
        if (false === key_exists('LiuggioExcelBundle', $this->getParameter('kernel.bundles'))) {
            throw $this->createNotFoundException();
        }

        $stringBlocks = $this->getDoctrine()->getRepository(StringBlock::class)->findBy(['deleted' => false]);

        $localeEntities = $this->getDoctrine()->getRepository(Translation::class)->createQueryBuilder('t')->groupBy('t.locale')->getQuery()->getResult();
        $locales = [];

        foreach ($localeEntities as $localeEntity) {
            $locales[] = $localeEntity->getLocale();
        }
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $phpExcelObject->getProperties()->setCreator("PluetznerBlockBundle")
            ->setLastModifiedBy("")
            ->setTitle("EXPORT")
            ->setSubject("EXPORT")
            ->setDescription("Export of StringBlocks");
        $sheet = $phpExcelObject->setActiveSheetIndex(0);

        $sheet->setCellValueByColumnAndRow(0, 1, 'slug');

        foreach ($locales as $key => $locale) {
            $sheet->setCellValueByColumnAndRow($key + 1, 1, $locale);
        }


        $em = $this->getDoctrine()->getEntityManager();
        foreach ($stringBlocks as $rowKey => $stringBlock) {
            $sheet->setCellValueByColumnAndRow(0, $rowKey + 2, $stringBlock->getSlug());
            foreach ($locales as $key => $locale) {
                $stringBlock->setLocale($locale);
                $em->refresh($stringBlock);
                $sheet->setCellValueByColumnAndRow($key + 1, $rowKey + 2, $stringBlock->getText());
            }
        }
        // create the writer
        $file = tempnam(sys_get_temp_dir(), 'export');
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        $fileWriter = clone $writer;
        $fileWriter->save($file);

        $export = new Export();
        $fileres = fopen($file, 'rb');
        $export
            ->setUser($this->getUser()->getId())
            ->setMimetype('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setFileEnding('xlsx')
            ->setEntity(StringBlock::class)
            ->setData($fileres);

        $this->getDoctrine()->getManager()->persist($export);
        $this->getDoctrine()->getManager()->flush();
        unlink($file);

        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'stringBlockExport.xlsx'
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/import")
     *
     * @Template()
     */
    public function importAction(Request $request)
    {
        $import = new Import();
        $form = $this->createForm(ImportFormType::class, $import);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();


            $fileRef = $import->getUploadedFile();
            if (null !== $fileRef) {
                $filePath = $fileRef->getPath() . "/" . $fileRef->getFilename() . $fileRef->getExtension();

                try {
                    $testObject = $this->get('phpexcel')->createPHPExcelObject($filePath);
                } catch (\Exception $e) {
                    $form->addError(new FormError('Please upload an Excel File.'));
                    return [
                        'form' => $form->createView(),
                    ];
                }

                $import->setData(base64_encode(file_get_contents($filePath)));

                $import
                    ->setEntity(StringBlock::class)
                    ->setUser($this->getUser()->getId())
                    ->setMimeType($fileRef->getMimeType())
                    ->setFileEnding($fileRef->getClientOriginalExtension())
                    ->setName(pathinfo($fileRef->getClientOriginalName(), PATHINFO_FILENAME));
            }

            $this->getDoctrine()->getConnection()->getConfiguration()->setSQLLogger(null);

            $manager->persist($import);
            $manager->flush();

            $manager->clear();

            return $this->redirectToRoute('pluetzner_block_stringblock_importlanguageselect', ['id' => $import->getId()]);
        }
        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/import/{id}/languages")
     *
     * @Template()
     */
    public function importLanguageSelectAction(Request $request, $id = 0)
    {
        $import = $this->getDoctrine()->getRepository(Import::class)->find(intval($id));

        if (null === $import || $import->getEntity() != StringBlock::class) {
            throw $this->createNotFoundException();
        }

        $file = tempnam(sys_get_temp_dir(), 'foo');
        $tmpFile = fopen($file, 'w');
        fwrite($tmpFile, base64_decode($import->getData()));
        fseek($tmpFile, 0);
        fclose($tmpFile);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($file);

        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $cell = 1;
        $locales = [];
        while ($sheet->getCellByColumnAndRow($cell, 1)->getValue()) {
            $locales[$sheet->getCellByColumnAndRow($cell, 1)->getValue()] = $sheet->getCellByColumnAndRow($cell, 1)->getValue();
            $cell++;
        }

        $languageSelectModel = new ImportLanguageSelectModel();

        $form = $this->createForm(ImportLanguageSelectFormType::class, $languageSelectModel, ['locales' => $locales]);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $changedLocales = [];
            foreach ($languageSelectModel->getLocales() as $locale) {
                $cell = 1;
                while ($sheet->getCellByColumnAndRow($cell, 1)->getValue()) {
                    if ($sheet->getCellByColumnAndRow($cell, 1)->getValue() === $locale) {
                        $changedLocales[] = $locale;
                        $row = 2;
                        while ($sheet->getCellByColumnAndRow($cell, $row)) {
                            if ($sheet->getCellByColumnAndRow($cell, $row)->getValue() === null) {
                                break;
                            }

                            $block = $this->getDoctrine()->getRepository(StringBlock::class)->findOneBy(['slug' => $sheet->getCellByColumnAndRow(0, $row)->getValue()]);
                            if (null === $block) {
                                continue;
                            }

                            $block->setText($sheet->getCellByColumnAndRow($cell, $row)->getValue());
                            $block->setLocale($locale);

                            $this->getDoctrine()->getManager()->persist($block);

                            $row++;
                        }
                        $this->getDoctrine()->getManager()->flush();
                    }
                    $cell++;
                }
            }
            if (count($changedLocales) === 0) {
                $message = 'No locale has been changed';
            } elseif (count($changedLocales) === 1) {
                $message = sprintf('The locale %s has been changed.', $changedLocales[0]);
            } else {
                $message = sprintf('The locales [%s] have been updated.', implode(', ', $changedLocales));
            }
            $this->addFlash('success', $message);
            return $this->redirectToRoute('pluetzner_block_stringblock_index');
        }

        unlink($file);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param int     $id
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
     * @param int     $id
     *
     * @Route("/{id}/editAjax")
     * @Template()
     *
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function editAjaxAction(Request $request, $id)
    {
        $stringBlock = $this->getDoctrine()->getRepository(StringBlock::class)->find(intval($id));
        if (null === $stringBlock) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(StringBlockFormType::class, $stringBlock, ['save_button' => false]);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($stringBlock);
            $manager->flush();

            return new Response($stringBlock->getText());
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
        $textblock = $this->getDoctrine()->getRepository(StringBlock::class)->find(intval($id));
        if (null === $textblock || true === $textblock->isDeleted()) {
            throw $this->createNotFoundException();
        }

        $textblock->setDeleted(true);

        $manager = $this->getDoctrine()->getManager();

        $manager->persist($textblock);
        $manager->flush();

        $this->get('session')->getFlashBag()->add('success', 'Stringblock erfolgreich gelöscht.');

        return $this->redirect($this->generateUrl('pluetzner_block_stringblock_index'));
    }
}

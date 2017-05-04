<?php

namespace CmsBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WH\BackendBundle\Controller\Backend\BaseController;
use WH\LibBundle\Entity\Status;

/**
 * Class PageController
 *
 * @package CmsBundle\Controller\Frontend
 */
class PageController extends BaseController
{

    /**
     * @param         $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id, Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $pageRepository = $em->getRepository('CmsBundle:Page');

        $page = $pageRepository->get(
            'one',
            [
                'conditions' => [
                    'page.id'     => $id,
                    'page.status' => Status::$STATUS_PUBLISHED,
                ],
            ]
        );
        if (!$page) {
            throw new NotFoundHttpException('Page introuvable');
        }

        $pageTemplate = $page->getPageTemplateSlug();
        if (!$pageTemplate) {
            throw new NotFoundHttpException('Aucun template de page n\'est associé à cette page');
        }
        $pageTemplates = $this->getParameter('wh_cms_templates');
        if (!isset($pageTemplates[$pageTemplate])) {
            if (!$pageTemplate) {
                throw new NotFoundHttpException('Le template de page associé à cette page n\'existe pas');
            }
        }
        $pageTemplate = $pageTemplates[$pageTemplate];

        $view = 'CmsBundle:Frontend/Page:view.html.twig';

        if (!empty($pageTemplate['frontController'])) {
            return $this->forward(
                $pageTemplate['frontController'],
                [
                    'id'      => $id,
                    'request' => $request,
                ]
            );
        }

        if (!empty($pageTemplate['frontView'])) {
            $view = $pageTemplate['frontView'];
        }

        $breadcrumb = $pageRepository->getPath($page);

        return $this->render(
            $view,
            [
                'page'       => $page,
                'breadcrumb' => $breadcrumb,
            ]
        );
    }

}

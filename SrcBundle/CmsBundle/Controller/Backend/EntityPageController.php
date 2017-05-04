<?php

namespace CmsBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use WH\BackendBundle\Controller\Backend\BaseController;

/**
 * Class EntityPageController
 *
 * @package CmsBundle\Controller\Backend
 */
class EntityPageController extends BaseController
{

    public $bundlePrefix = '';
    public $bundle = 'CmsBundle';
    public $entity = 'Page';

    /**
     * @param         $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction($id, Request $request)
    {
        $updateController = $this->get('bk.wh.back.update_controller');

        $entityPathConfig = $this->getEntityPathConfig();

        $entityPathConfigPage = [
            'bundlePrefix' => '',
            'bundle'       => 'CmsBundle',
            'entity'       => 'Page',
            'type'         => 'Backend',
        ];

        $updateController->entityPathConfig = $entityPathConfig;
        $updateController->request = $request;

        $em = $this->get('doctrine')->getManager();

        $data = $em->getRepository($updateController->getRepositoryName($entityPathConfig))->get(
            'one',
            [
                'conditions' => [
                    'page.id' => $id,
                ],
            ]
        );

        if (!$data) {
            $entityClass = new \ReflectionClass($this->getEntityPath($entityPathConfig));
            $entity = $entityClass->newInstanceArgs();

            $page = $em->getRepository($this->getRepositoryName($entityPathConfigPage))->get(
                'one',
                [
                    'conditions' => [
                        'page.id' => $id,
                    ],
                ]
            );
            $entity->setPage($page);

            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);

            $data = $entity;
        }

        $updateController->data = $data;

        $updateController->config = $updateController->getConfig($entityPathConfig, 'update');
        $updateController->globalConfig = $updateController->getGlobalConfig($entityPathConfig);

        $updateController->renderVars['globalConfig'] = $updateController->globalConfig;

        $updateController->createUpdateForm();
        if ($updateController->request->getMethod() == 'POST') {
            return $updateController->handleUpdateFormSubmission();
        }
        $updateController->renderUpdateForm();

        $updateController->getFormProperties();

        $updateController->renderVars['title'] = $updateController->config['title'];

        if (!$updateController->modal) {
            $updateController->renderVars['breadcrumb'] = $updateController->getBreadcrumb(
                $updateController->config['breadcrumb'],
                $entityPathConfigPage,
                $data->getPage()
            );
        }

        $view = '@WHBackendTemplate/BackendTemplate/View/update.html.twig';
        if ($updateController->modal) {
            $view = '@WHBackendTemplate/BackendTemplate/View/modal.html.twig';
            if (isset($updateController->config['view'])) {
                $view = $updateController->config['view'];
            }
        }

        $updateController->renderVars = $updateController->translateRenderVars(
            $entityPathConfig,
            $updateController->renderVars
        );

        return $this->container->get('templating')->renderResponse(
            $view,
            $updateController->renderVars
        );
    }

}

<?php

namespace CmsBundle\Controller\Backend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use WH\BackendBundle\Controller\Backend\BaseController;
use WH\LibBundle\Utils\Inflector;

/**
 * @Route("/admin/pages")
 *
 * Class PageController
 *
 * @package CmsBundle\Controller\Backend
 */
class PageController extends BaseController
{

    public $bundlePrefix = '';
    public $bundle = 'CmsBundle';
    public $entity = 'Page';

    /**
     * @Route("/index/{parentId}", name="bk_cms_page_index", requirements={"parentId": ".*"}, defaults={"parentId": null})
     *
     * @param         $parentId
     * @param Request $request
     *
     * @return string
     */
    public function indexAction($parentId = null, Request $request)
    {
        $arguments = [
            'parent.id' => $parentId,
        ];

        $indexController = $this->get('bk.wh.back.index_controller');

        return $indexController->index($this->getEntityPathConfig(), $request, $arguments);
    }

    /**
     * @Route("/create/", name="bk_cms_page_create")
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function createAction(Request $request)
    {
        $createController = $this->get('bk.wh.back.create_controller');

        return $createController->create($this->getEntityPathConfig(), $request);
    }

    /**
     * @Route("/update/{id}", name="bk_cms_page_update")
     *
     * @param         $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction($id, Request $request)
    {
        $updateController = $this->get('bk.wh.back.update_controller');

        $entityPathConfig = $this->getEntityPathConfig();

        $updateController->entityPathConfig = $entityPathConfig;
        $updateController->request = $request;

        $em = $this->get('doctrine')->getManager();

        $data = $em->getRepository($updateController->getRepositoryName($entityPathConfig))->get(
            'one',
            [
                'conditions' => [
                    Inflector::camelize($entityPathConfig['entity']) . '.id' => $id,
                ],
            ]
        );

        if (!$data) {
            return $updateController->redirect($updateController->getActionUrl($entityPathConfig, 'index'));
        }

        $pageTemplate = '';
        if ($data->getPageTemplateSlug()) {
            $pageTemplate = $this->getParameter('wh_cms_templates')[$data->getPageTemplateSlug()];
        }

        if (!empty($pageTemplate['backendController'])) {
            return $this->forward(
                $pageTemplate['backendController'] . ':update',
                [
                    'id'      => $id,
                    'request' => $request,
                ]
            );
        }

        $updateController->data = $data;

        if (!empty($pageTemplate['updateConfig'])) {
            $updateController->config = $updateController->getConfig($entityPathConfig, $pageTemplate['updateConfig']);
        } else {
            $updateController->config = $updateController->getConfig($entityPathConfig, 'update');
        }
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
                $entityPathConfig,
                $data
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

    /**
     * @Route("/delete/{id}", name="bk_cms_page_delete")
     *
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        $deleteController = $this->get('bk.wh.back.delete_controller');

        return $deleteController->delete($this->getEntityPathConfig(), $id);
    }

    /**
     * @Route("/order/", name="bk_cms_page_order")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderAction(Request $request)
    {
        $orderController = $this->get('bk.wh.back.order_controller');

        return $orderController->order($this->getEntityPathConfig(), $request);
    }

}

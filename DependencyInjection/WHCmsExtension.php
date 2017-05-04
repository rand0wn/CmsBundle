<?php

namespace WH\CmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class WHCmsExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $templates = $config['templates'];
        $container->setParameter('wh_cms_templates', $templates);

        $templatesChoices = [];
        foreach ($templates as $templateSlug => $template) {
            $templatesChoices[$templateSlug] = $template['name'];
        }
        $container->setParameter('wh_cms_templates_choices', $templatesChoices);

        $menusChoices = [];
        foreach ($config['menus'] as $menuSlug => $menuName) {
            $menusChoices[$menuSlug] = $menuName;
        }
        $container->setParameter('wh_cms_menus_choices', $menusChoices);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}

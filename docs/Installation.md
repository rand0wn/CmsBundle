# Installation
## Composer
`composer require whatson-web/cms-bundle dev-master`

## Déclaration du bundle dans le AppKernel

```php
new WH\CmsBundle\WHCmsBundle(),
```
## Ajouter les routes
```yaml
bk_wh_cms:
    resource: "@WHCmsBundle/Controller/Backend/"
    type:     annotation
```

## Base configuration
```yaml
wh_cms:
    templates:
        home:
            name: 'Accueil'
            frontView: 'WHCmsBundle:FrontEnd/Page:home.html.twig'
        page:
            name: 'Page normale'
        contact:
            name: 'Page contact'
            frontView: 'WHCmsBundle:FrontEnd/Page:contact.html.twig'
```

## Base configuration SEO
```yaml
wh_seo:
    entities:
        WH\CmsBundle\Entity\Page:
            urlFields:
                - {type: 'tree', entity: 'WH\CmsBundle\Entity\Page', field: 'parent'}
                - {type: 'field', field: 'slug'}
            defaultMetasFields:
                title: 'name'
                description: 'resume'
```

## Installation des entités
Copier les fichiers suivants dans `/src/WHEntities/CmsBundle` :

- [Page](https://github.com/whatson-web/CmsBundle/tree/master/docs/installation/WHEntities/Page.php)

Déclarer les dans `config.yml` :

```yaml
doctrine:
    orm:
        mappings:
            WHEntitiesCms:
                type: annotation
                is_bundle: false
                dir: '%kernel.root_dir%/../src/WHEntities/CmsBundle'
                prefix: WH\CmsBundle\Entity
                alias: WHCmsBundle
```

Déclarer les manuellement dans `/app/autoload.php` :

```php
AnnotationRegistry::registerFile(__DIR__ . '/../src/WHEntities/CmsBundle/Page.php');
```
##créer le menu
recréer les onglets du menu 
exemple: ajouter les pages
ajouter dans le `BackendBundle/Menu/menu.php`

	$menu->addChild(
				'pages',
				array(
					'label'  => $this->getLabel('sitemap', 'Pages'),
					'route'  => 'bk_wh_cms_page_index',
					'extras' => array(
						'safe_label' => true,
					),
				)
			);
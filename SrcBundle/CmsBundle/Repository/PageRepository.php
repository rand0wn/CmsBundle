<?php

namespace CmsBundle\Repository;

use WH\LibBundle\Repository\BaseTreeRepository;

/**
 * Class PageRepository
 *
 * @package CmsBundle\Repository
 */
class PageRepository extends BaseTreeRepository
{
    public $entityName = 'page';

    public $joins = [
        'url'    => '',
        'metas'  => '',
        'parent' => '',
    ];

    public $baseJoins = [
        'url',
        'metas',
        'parent',
    ];
    public $baseOrders = [
        'page.lft' => 'ASC',
    ];
}

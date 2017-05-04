<?php

namespace WH\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use WH\LibBundle\Entity\Content;
use WH\LibBundle\Entity\LogDate;
use WH\LibBundle\Entity\Status;
use WH\LibBundle\Entity\Tree;

/**
 * Class Page
 *
 * @ORM\MappedSuperclass
 *
 * @Gedmo\Tree(type="nested")
 *
 * @package WH\CmsBundle\Entity
 */
abstract class Page
{

    use Content, LogDate, Tree;
    use Status {
        Status::__construct as protected __statusConstruct;
    }

    /**
     * Page constructor.
     */
    public function __construct()
    {
        $this->__statusConstruct();
        $this->children = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="CmsBundle\Entity\Page")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="CmsBundle\Entity\Page", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="CmsBundle\Entity\Page", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\OneToOne(targetEntity="WH\SeoBundle\Entity\Url", cascade={"persist", "remove"})
     */
    protected $url;

    /**
     * @ORM\OneToOne(targetEntity="WH\SeoBundle\Entity\Metas", cascade={"persist", "remove"})
     */
    protected $metas;

    /**
     * @var string
     *
     * @ORM\Column(name="pageTemplateSlug", type="string", length=255)
     */
    protected $pageTemplateSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="menus", type="text", nullable=true)
     */
    protected $menus;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set root
     *
     * @param \CmsBundle\Entity\Page $root
     *
     * @return Page
     */
    public function setRoot(\CmsBundle\Entity\Page $root = null)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return \CmsBundle\Entity\Page
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param \CmsBundle\Entity\Page $parent
     *
     * @return Page
     */
    public function setParent(\CmsBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CmsBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \CmsBundle\Entity\Page $child
     *
     * @return Page
     */
    public function addChild(\CmsBundle\Entity\Page $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \CmsBundle\Entity\Page $child
     */
    public function removeChild(\CmsBundle\Entity\Page $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get name indented
     *
     * @return string
     */
    public function getIndentedName()
    {

        return str_repeat(" > ", $this->lvl) . $this->name;
    }

    /**
     * Set url
     *
     * @param \WH\SeoBundle\Entity\Url $url
     *
     * @return Page
     */
    public function setUrl(\WH\SeoBundle\Entity\Url $url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return \WH\SeoBundle\Entity\Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set metas
     *
     * @param \WH\SeoBundle\Entity\Metas $metas
     *
     * @return Page
     */
    public function setMetas(\WH\SeoBundle\Entity\Metas $metas = null)
    {
        $this->metas = $metas;

        return $this;
    }

    /**
     * Get metas
     *
     * @return \WH\SeoBundle\Entity\Metas
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * Set pageTemplateSlug
     *
     * @param string $pageTemplateSlug
     *
     * @return Page
     */
    public function setPageTemplateSlug($pageTemplateSlug)
    {
        $this->pageTemplateSlug = $pageTemplateSlug;

        return $this;
    }

    /**
     * Get pageTemplateSlug
     *
     * @return string
     */
    public function getPageTemplateSlug()
    {
        return $this->pageTemplateSlug;
    }

    /**
     * Set menus
     *
     * @param string $menus
     *
     * @return Page
     */
    public function setMenus($menus)
    {
        $menus = json_encode($menus);
        $this->menus = $menus;

        return $this;
    }

    /**
     * Get menus
     *
     * @return string
     */
    public function getMenus()
    {
        return json_decode($this->menus, true);
    }
}

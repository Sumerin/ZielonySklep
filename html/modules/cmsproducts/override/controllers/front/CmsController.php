<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2017 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class CmsController extends CmsControllerCore
{
    public function init()
    {
        if ($id_cms = (int)Tools::getValue('id_cms'))
        {
            $this->cms = new CMS($id_cms, $this->context->language->id, $this->context->shop->id);
        }
        elseif ($id_cms_category = (int)Tools::getValue('id_cms_category'))
        {
            $this->cms_category = new CMSCategory($id_cms_category, $this->context->language->id, $this->context->shop->id);
        }
        if (Configuration::get('PS_SSL_ENABLED') && Tools::getValue('content_only') && $id_cms && Validate::isLoadedObject($this->cms) && in_array($id_cms, array(
                (int)Configuration::get('PS_CONDITIONS_CMS_ID'),
                (int)Configuration::get('LEGAL_CMS_ID_REVOCATION')
            ))
        )
        {
            $this->ssl = true;
        }
        parent::init();
        $this->canonicalRedirection();
        if (Validate::isLoadedObject($this->cms))
        {
            $adtoken = Tools::getAdminToken('AdminCmsContent' . (int)Tab::getIdFromClassName('AdminCmsContent') . (int)Tools::getValue('id_employee'));
            if (!$this->cms->isAssociatedToShop() || !$this->cms->active && Tools::getValue('adtoken') != $adtoken)
            {
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
            }
            else
            {
                $this->assignCase = 1;
            }
        }
        elseif (Validate::isLoadedObject($this->cms_category) && $this->cms_category->active)
        {
            $this->assignCase = 2;
        }
        else
        {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        if ($this->assignCase == 1)
        {
            $this->addJS(_THEME_JS_DIR_ . 'cms.js');
        }
        $this->addCSS(_THEME_CSS_DIR_ . 'product_list.css');
        $this->addCSS(_THEME_CSS_DIR_ . 'cms.css');
        $this->addCSS(_PS_MODULE_DIR_ . 'cmsproducts/cmsproducts.css');
    }

    public function initContent()
    {
        parent::initContent();
        $parent_cat = new CMSCategory(1, $this->context->language->id);
        $this->context->smarty->assign('id_current_lang', $this->context->language->id);
        $this->context->smarty->assign('home_title', $parent_cat->name);
        $this->context->smarty->assign('cgv_id', Configuration::get('PS_CONDITIONS_CMS_ID'));
        if ($this->assignCase == 1)
        {
            if (isset($this->cms->id_cms_category) && $this->cms->id_cms_category)
            {
                $path = Tools::getFullPath($this->cms->id_cms_category, $this->cms->meta_title, 'CMS');
            }
            elseif (isset($this->cms_category->meta_title))
            {
                $path = Tools::getFullPath(1, $this->cms_category->meta_title, 'CMS');
            }


            $this->cms->content = $this->returnContent($this->cms->content);

            $this->context->smarty->assign(array(
                'cms' => $this->cms,
                'content_only' => (int)Tools::getValue('content_only'),
                'path' => $path,
                'body_classes' => array(
                    $this->php_self . '-' . $this->cms->id,
                    $this->php_self . '-' . $this->cms->link_rewrite
                )
            ));
            if ($this->cms->indexation == 0)
            {
                $this->context->smarty->assign('nobots', true);
            }
        }
        elseif ($this->assignCase == 2)
        {
            $this->context->smarty->assign(array(
                'category' => $this->cms_category,
                //for backward compatibility
                'cms_category' => $this->cms_category,
                'sub_category' => $this->cms_category->getSubCategories($this->context->language->id),
                'cms_pages' => CMS::getCMSPages($this->context->language->id, (int)$this->cms_category->id, true, (int)$this->context->shop->id),
                'path' => ($this->cms_category->id !== 1) ? Tools::getPath($this->cms_category->id, $this->cms_category->name, false, 'CMS') : '',
                'body_classes' => array(
                    $this->php_self . '-' . $this->cms_category->id,
                    $this->php_self . '-' . $this->cms_category->link_rewrite
                )
            ));
        }
        $this->setTemplate(_PS_THEME_DIR_ . 'cms.tpl');
    }

    public static function getImagesByID($id_product, $limit = 0)
    {
        $id_image = Db::getInstance()->ExecuteS('SELECT `id_image` FROM `' . _DB_PREFIX_ . 'image` WHERE cover=1 AND `id_product` = ' . (int)$id_product . ' ORDER BY position ASC LIMIT 0, ' . (int)$limit);
        $toReturn = array();
        if (!$id_image)
        {
            return null;
        }
        else
        {
            foreach ($id_image as $image)
            {
                $toReturn[] = $id_product . '-' . $image['id_image'];
            }
        }
        return $toReturn;
    }

    public function returnProduct($id_product)
    {
        $explode[] = $id_product;
        foreach ($explode as $tproduct)
        {
            if ($tproduct != '')
            {
                $x = (array)new Product($tproduct, true, $this->context->language->id);
                $productss[$tproduct] = $x;
                $productss[$tproduct]['id_product'] = $tproduct;
                $image = self::getImagesByID($tproduct, 1);
                $picture = explode('-', $image[0]);
                $productss[$tproduct]['id_image'] = $picture[1];

            }
        }
        $products = Product::getProductsProperties($this->context->language->id, $productss);
        $this->context->smarty->assign('products', $products);
        $this->context->smarty->assign('feedtype', "cmsSingleProductFeed");
        $contents = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'cmsproducts/products.tpl');
        return $contents;
    }

    public function returnProducts($id_product)
    {
        $explode_products = explode(",", $id_product);
        foreach ($explode_products AS $idp)
        {
            $explode[] = $idp;
            foreach ($explode as $tproduct)
            {
                if ($tproduct != '')
                {
                    $x = (array)new Product($tproduct, true, $this->context->language->id);
                    $productss[$tproduct] = $x;
                    $productss[$tproduct]['id_product'] = $tproduct;
                    $image = self::getImagesByID($tproduct, 1);
                    $picture = explode('-', $image[0]);
                    $productss[$tproduct]['id_image'] = $picture[1];

                }
            }
        }
        $products = Product::getProductsProperties($this->context->language->id, $productss);
        $this->context->smarty->assign('products', $products);
        $this->context->smarty->assign('feedtype', "cmsProductsFeed");
        $contents = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'cmsproducts/products.tpl');
        return $contents;
    }

    public function returnProductsHpp($block)
    {
        if (class_exists("Hpp"))
        {
            $hpp = new Hpp();
            if (method_exists($hpp, 'returnProducts'))
            {
                return $this->displayHpp($hpp->returnProducts($block));
            }
            else
            {
                return $this->noModuleMessage("Homepage Products Pro");
            }
        }
        else
        {
            return $this->noModuleMessage("Homepage Products Pro");
        }
    }

    public function returnProductsRpp($block)
    {
        if (class_exists("Ppb"))
        {
            $rpp = new Ppb();
            if (method_exists($rpp, 'returnProducts'))
            {
                return $this->displayRpp($rpp->returnProducts($block));
            }
            else
            {
                return $this->noModuleMessage("Related Products Pro");
            }
        }
        else
        {
            return $this->noModuleMessage("Related Products Pro");
        }
    }

    public function displayRpp($products)
    {
        if (count($products) <= 0)
        {
            $this->context->smarty->assign('feedtype', "noProducts");
        }
        else
        {
            $this->context->smarty->assign('products', $products);
            $this->context->smarty->assign('feedtype', "rppfeed");
        }
        $contents = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'cmsproducts/products.tpl');
        return $contents;
    }

    public function displayHpp($products)
    {
        if (count($products) <= 0)
        {
            $this->context->smarty->assign('feedtype', "noProducts");
        }
        else
        {
            $this->context->smarty->assign('products', $products);
            $this->context->smarty->assign('feedtype', "hppfeed");
        }
        $contents = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'cmsproducts/products.tpl');
        return $contents;
    }

    public function noModuleMessage($module)
    {
        $this->context->smarty->assign('products', $products);
        $this->context->smarty->assign('module', $module);
        $this->context->smarty->assign('feedtype', "error");
        $contents = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'cmsproducts/products.tpl');
        return $contents;
    }

    public function returnlastReviews()
    {
        if (Module::isInstalled('lastreviews') && Module::isEnabled('lastreviews'))
        {
            $module = Module::getInstanceByName('lastreviews');
            if (method_exists($module, 'showOnCmsPage'))
            {
                return $module->showOnCmsPage();
            }
        }
        return $this->noModuleMessage("<a href=\"https://mypresta.eu/modules/front-office-features/last-product-reviews.html\">[Last Product Reviews by Mypresta]</a>");
    }

    public function returnContent($contents)
    {
        preg_match_all('/\{products\:[(0-9\,)]+\}/i', $contents, $matches);
        foreach ($matches[0] as $index => $match)
        {
            $explode = explode(":", $match);
            $contents = str_replace($match, $this->returnProducts(str_replace("}", "", $explode[1])), $contents);
        }

        preg_match_all('/\{product\:[(0-9\,)]+\}/i', $contents, $matches);
        foreach ($matches[0] as $index => $match)
        {
            $explode = explode(":", $match);
            $contents = str_replace($match, $this->returnProduct(str_replace("}", "", $explode[1])), $contents);
        }

        preg_match_all('/\{hpp\:[(0-9)]+\}/i', $contents, $matches);
        foreach ($matches[0] as $index => $match)
        {
            $explode = explode(":", $match);
            $contents = str_replace($match, $this->returnProductsHpp(str_replace("}", "", $explode[1])), $contents);
        }

        preg_match_all('/\{rpp\:[(0-9)]+\}/i', $contents, $matches);
        foreach ($matches[0] as $index => $match)
        {
            $explode = explode(":", $match);
            $contents = str_replace($match, $this->returnProductsRpp(str_replace("}", "", $explode[1])), $contents);
        }

        preg_match_all('/\{lastreviews\}/i', $contents, $matches);
        foreach ($matches[0] as $index => $match)
        {
            $contents = str_replace($match, $this->returnlastReviews(str_replace("}", "")), $contents);
        }
        return $contents;
    }
}
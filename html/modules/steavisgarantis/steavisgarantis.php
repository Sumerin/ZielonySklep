<?php
/**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author    Société des Avis Garantis <contact@societe-des-avis-garantis.fr>
*  @copyright 2013-2017 Société des Avis Garantis
*  @license   LICENSE.txt
*/

if (!defined('_PS_VERSION_')) {
    exit;
}


if (!defined('_AGDIR_')) {
    define('_AGDIR_', dirname(__FILE__));
}

define("SAGAPIENDPOINT", "wp-content/plugins/ag-core/api/");

class STEAVISGARANTIS extends Module
{
    public function __construct()
    {
        //if (Module::isInstalled('sag')) {
        //    Module::disableByName('sag');
        //    $this->warning = $this->l('Two versions of Guaranteed Reviews Company\'s module are installed. Please uninstall oldest.');
        //}
        
        $this->bootstrap = true;
        $this->name = 'steavisgarantis';
        $this->tab = 'advertising_marketing';
        $this->version = '5.0.9';
        $this->author = 'Société des Avis Garantis';
        $this->need_instance = 0;
        $this->module_key = '7925df33d223a2b4c7f1786e1efb51f7';
        parent::__construct();
        $this->displayName = $this->l('Guaranteed Reviews Company');
        $this->description = $this->l('Collect, certify and publish your cutomer reviews. Increase your sales fastly and easily.');
        $this->initContext();
    }
 
    public function installDatabase()
    {
        $query=array();
        $query[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'steavisgarantis_average_rating;';
        $query[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'steavisgarantis_reviews;';
        $query[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'steavisgarantis_reviews (
                      `id` bigint(20) AUTO_INCREMENT,
                      `id_product_avisg` varchar(38) NOT NULL,
                      `product_id` varchar(30) NOT NULL,
                      `ag_reviewer_name` varchar(35) NOT NULL,
                      `rate` varchar(4) NOT NULL,
                      `review` text NOT NULL,
                      `date_time` text NOT NULL,
                      `answer_text` text DEFAULT NULL,
                      `answer_date_time` DATETIME DEFAULT NULL,
                      `order_date` DATETIME DEFAULT NULL,
                      `id_lang` varchar(11) NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
        $query[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'steavisgarantis_average_rating` (
                      `id`bigint(20) AUTO_INCREMENT,
                      `id_product_avisg` varchar(38) NOT NULL,
                      `product_id` varchar(30) NOT NULL,
                      `rate` varchar(4) NOT NULL,
                      `percent1` int(11) NOT NULL,
                      `percent2` int(11) NOT NULL,
                      `percent3` int(11) NOT NULL,
                      `percent4` int(11) NOT NULL,
                      `percent5` int(11) NOT NULL,
                      `nb1` int(11) NOT NULL,
                      `nb2` int(11) NOT NULL,
                      `nb3` int(11) NOT NULL,
                      `nb4` int(11) NOT NULL,
                      `nb5` int(11) NOT NULL,
                      `date_time_update` text NOT NULL,
                      `reviews_nb` int(11) NOT NULL,
                      `id_lang` varchar(11) NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

        foreach ($query as $key => $sqlQuery) {
            if (!Db::getInstance()->Execute($sqlQuery)) {
                $this->errors = $this->l('SQL database creation error');
                return false;
            }
        }

        return true;
    }
 

    public function uninstallDatabase()
    {
        $query = array();
        $query[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'steavisgarantis_average_rating';
        $query[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'steavisgarantis_reviews';


        foreach ($query as $key => $sqlQuery) {
            if (!Db::getInstance()->Execute($sqlQuery)) {
                $this->errors = $this->l('Error while deleting SQL database table');
                return false;
            }
        }

        return true;
    }
 

    public function install()
    {

        if (version_compare(_PS_VERSION_, '1.5', '<')) {    //Installation pour PrestaShop 1.4
            if (!parent::install()) {
                return false;
            }
 
            if (!$this->installDatabase()
            || !$this->registerHook('header')
            || !$this->registerHook('footer')
            || !$this->registerHook('leftColumn')
            || !$this->registerHook('productTab')
            || !$this->registerHook('productTabContent')
            || !$this->registerHook('rightColumn')
            || !$this->registerHook('productActions')) {
                return false;
            }
        } else {        //Installation pour PrestaShop 1.5, 1.6 et 1.7
            if (!$this->installDatabase()
            || !parent::install()
            || !$this->registerHook('displayRightColumnProduct')
            || !$this->registerHook('displayLeftColumn')
            || !$this->registerHook('displayRightColumn')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayFooter')
            || !$this->registerHook('displayProductTab')
            || !$this->registerHook('displayProductButtons')
            || !$this->registerHook('displayProductExtraContent')
            || !$this->registerHook('displayProductTabContent')) {
                $this->errors = array('Erreur d\'installation du module.');
                return false;
            }
        }
 
        //On configure par défaut le bloc d'avis iFrame en désactivé (si on a jamais installé le module)
        if (!Configuration::get('steavisgarantis_widgetPosition')) {
            Configuration::updateValue('steavisgarantis_widgetPosition', "footer");
        }

        //On configure par défaut le widget Javascript en désactivé
        if (!Configuration::get('steavisgarantis_widgetJavascript')) {
            Configuration::updateValue('steavisgarantis_widgetJavascript', true);
        }

        //On configure par défaut le lien Footer en désactivé
        if (!Configuration::get('steavisgarantis_footerLink')) {
            Configuration::updateValue('steavisgarantis_footerLink', true);
        }
        
        //On configure par défaut les status à inclure à expedié ce jour et livré
        if (!Configuration::get('steavisgarantis_includeStatus')) {
            Configuration::updateValue('steavisgarantis_includeStatus', "4,5");
        }
        
        
        //On configure par défaut le délai s'il est vide
        if (!Configuration::get('steavisgarantis_afterDays')) {
            Configuration::updateValue('steavisgarantis_afterDays', 10);
        }
        
        //On configure par défaut le mode d'affichage en normal s'il est vide
        if (!is_numeric(!Configuration::get('steavisgarantis_normalBehaviour'))) {
            Configuration::updateValue('steavisgarantis_normalBehaviour', 1);
        }

        

        return true;
    }
 
    public function uninstall()
    {
        if (!parent::uninstall() ||  !$this->uninstallDatabase()) {
            $this->errors = $this->l('Uninstall failed');
            return false;
        }

        return true;
    }
    
    
    private function initContext()
    {
        if (class_exists('Context')) {
            $this->context = Context::getContext();
        } else {
            global $smarty, $cookie;        // Retrocompatibility 1.4
            $this->context = new StdClass();
            $this->context->smarty = $smarty;
            $this->context->cookie = $cookie;
        }
        
        /*
        //Allows very old plugin updates compatibility
        //Si on a le module SAG installé et que l'on a jamais migré
        if (!Configuration::get('steavisgarantis_alreadyMigrated') and Module::isInstalled('sag')) {
            //if we have an Api Key in old conf, we copy it in new conf
            if (Configuration::get('sag_apiKey')) {
                $languages = Language::getLanguages(true, Context::getContext()->shop->id);
                foreach ($languages as $language) {
                    Configuration::updateValue('steavisgarantis_apiKey_'.$language["id_lang"], Configuration::get('sag_apiKey'));
                }
            }
            Module::disableByName('sag');   //We disable old named SAG module
            self::sendUpdatePath();         //We have to notify Société des Avis Garantis that we have a new path
            Configuration::updateValue('steavisgarantis_alreadyMigrated', 1);   //We save migration in DB
        }
        */

        /* 1.6 bug reported by addons team, should modify this to if module is installed ONLY
        Allow plugin db compatibility
        if (!Configuration::get('steavisgarantis_updatedDb')) {
            //Si on est sur l'ancienne architecture de table on migre vers la nouvelle
            $sql = "SHOW COLUMNS FROM ". _DB_PREFIX_ . "steavisgarantis_reviews LIKE 'id'";
            $exists = Db::getInstance()->ExecuteS($sql);
            if (!$exists) {
                self::updateDataTable();
            }
        }
        Configuration::updateValue('steavisgarantis_updatedDb', 509);//Save db version anyway
        */
        
        //Sécurité (si aucun statut sélectionné on met Expedié et livré)
        if (!Configuration::get('steavisgarantis_includeStatus')) {
            Configuration::updateValue('steavisgarantis_includeStatus', "4,5");
        }

        //On configure par défaut le délai s'il est vide
        if (!Configuration::get('steavisgarantis_afterDays')) {
            Configuration::updateValue('steavisgarantis_afterDays', 10);
        }

        //On configure par défaut le mode d'affichage en normal s'il est vide
        if (!is_numeric(Configuration::get('steavisgarantis_normalBehaviour'))) {
            Configuration::updateValue('steavisgarantis_normalBehaviour', 1);
        }
    }
 
    public function displayIframeWidget()
    {
        $apiKey = Configuration::get('steavisgarantis_apiKey_' . $this->context->language->id);
        $shopID = self::getShopId($this->context->language->id);
        $url=self::getCertificateUrl($this->context->language->id);
        //Quand c'est de l'iFrame il faut mieux ne pas mentionner le protocole
        $domain = str_replace("https:", "", self::getDomainUrl($apiKey));
        $this->context->smarty->assign(array(
            'url_ag' => $url,
            'shopID' => $shopID,
            'domain' => $domain
        ));
        return $this->display(__FILE__, 'views/templates/front/displayIframeWidget.tpl');
    }
 
    public function hookdisplayLeftColumn()
    {
        if (Configuration::get('steavisgarantis_widgetPosition')=="left") {
            return $this->displayIframeWidget();
        } else {
            return false;
        }
    }
 
    public function hookdisplayFooter()
    {
        $apiKey = Configuration::get('steavisgarantis_apiKey_' . $this->context->language->id);
        //Quand c'est de l'iFrame il faut mieux ne pas mentionner le protocole
        $domain = str_replace("https:", "", self::getDomainUrl($apiKey));
        
        //On récupère les données de configuration du widget et du lien de vérification
        $widgetFooter = (Configuration::get('steavisgarantis_widgetPosition')=="footer") ? 1 : 0 ;
        $footerLink = Configuration::get('steavisgarantis_footerLink');
        $this->context->smarty->assign(array(
            'widgetFooter' => $widgetFooter,
            'footerLink' => $footerLink,
            'domain' => $domain,
        ));
        //Si on doit afficher l'un ou l'autre il faut récupérer certaines variables
        if ($widgetFooter or $footerLink) {
            $url=self::getCertificateUrl($this->context->language->id);
            $this->context->smarty->assign(array(
                'url_steavisgarantis' => $url,
            ));
            //Si on doit afficher le widget iframe dans le footer
            if ($widgetFooter) {
                $shopID = self::getShopId($this->context->language->id);
                $this->context->smarty->assign(array('shopID' => $shopID));
            }

            //Si on doit afficher le lien dans le footer
            if ($footerLink) {
                $this->context->smarty->assign(array('modules_dir' => _MODULE_DIR_));
            }
        }

        return $this->display(__FILE__, 'views/templates/front/displayFooter.tpl');
    }
 
    public function hookdisplayRightColumn()
    {
        if (Configuration::get('steavisgarantis_widgetPosition')=="right") {
            return $this->displayIframeWidget();
        } else {
            return false;
        }
    }
 
    //Widget javascript
    public function hookdisplayHeader()
    {

        $this->context->controller->addCSS(($this->_path).'views/css/style.css', 'all');
        //$this->context->controller->addjQuery('2.0.0');
        $this->context->controller->addJS(($this->_path).'views/js/steavisgarantis.js', 'all');


        $apiKey = Configuration::get('steavisgarantis_apiKey_' . $this->context->language->id);
        if (Configuration::get('steavisgarantis_widgetJavascript') and $apiKey) {
            $shopID = self::getShopId($this->context->language->id);
            $url=self::getCertificateUrl($this->context->language->id);
            $domain = self::getDomainUrl($apiKey);
            
            //WHAT IS IT?
            if (filter_var($url, FILTER_VALIDATE_URL)) {
            } else {
                $url=Tools::substr($url, 0, 10);
            }

            $this->context->smarty->assign(array(
                'url_ag' => $url,
                'shopID' => $shopID,
                'domain' => $domain,
                'displayJSWidget' => 1
            ));
        } else {
            $this->context->smarty->assign(array(
                'displayJSWidget' => 0
            ));
        }

        return (isset($output) ? $output : null) . $this->display(__FILE__, 'views/templates/front/displayHeader.tpl');
    }

    //Presta <1.5
    public function hookHeader()
    {
        return $this->hookdisplayHeader();
    }
 
    public function hookFooter()
    {
        return $this->hookdisplayFooter();
    }
 
    public function hookProductActions()
    {
        return $this->hookdisplayRightColumnProduct();
    }
 
    public function hookRightColumn()
    {
        return $this->hookdisplayRightColumn();
    }
 
    public function hookLeftColumn()
    {
        return $this->hookdisplayLeftColumn();
    }
 
    public function hookProductTab()
    {
        return $this->hookdisplayProductTab();
    }
 
    public function hookProductTabContent()
    {
        return $this->hookdisplayProductTabContent();
    }
 
    public function hookdisplayProductTab()
    {
        //Si la version est inférieure à 1.6.0 on utilise le product tab sinon non
        
        if (version_compare(_PS_VERSION_, '1.6.0', '<') == Configuration::get("steavisgarantis_normalBehaviour")) {
            $productID = (int)(Tools::getValue('id_product'));
            $id_lang = (int)$this->context->language->id;
            $sqlQuery = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='$productID' and id_lang='$id_lang'";
            $nb= Db::getInstance()->getValue($sqlQuery);
            if ($nb < 1) {
                return "";
            }
            $this->context->smarty->assign(array(
             'reviewTabStr' => $this->l('Customer reviews'),
            ));
            return $this->display(__FILE__, 'views/templates/front/displayProductTab.tpl');
        } else {
            return false;
        }
    }

    public function hookdisplayProductTabContent()
    {
        $productID = (int)(Tools::getValue('id_product'));
        $id_lang = (int)$this->context->language->id;
        //Récupération du nombre d'avis
        $sqlQuery = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='$productID' and id_lang='$id_lang'";
        $nb= Db::getInstance()->getValue($sqlQuery);
        $sqlQuery = "SELECT * FROM "._DB_PREFIX_."steavisgarantis_average_rating WHERE product_id='$productID' and id_lang='$id_lang'";
        $ratingValues= Db::getInstance()->getRow($sqlQuery);
        $rating=$ratingValues['rate'];
        if ($nb < 1) {
            return ""; //Si Aucun avis, on retourne vide
        }
 
        $nbMaxReviews=10;
        $sqlQuery = "SELECT * FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='$productID' and id_lang='$id_lang' ORDER BY date_time DESC LIMIT $nbMaxReviews";
        $reviews = Db::getInstance()->ExecuteS($sqlQuery);

        //Récupération de l'objet produit
        if (!isset($params['product'])) {
            if (!$id_product = Tools::getValue('id_product')) {
                return $this->l('Missing product object. Set new object mode from the back office Configuration');
            }
 
            //Fix context undefined bug in 1.4, maybe PS_LANG_DEFAULT works in 1.5 1.6... etc but not tested so we keep old code
            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'));
            } else {
                $product = new Product($id_product, false, $this->context->language->id);
            }

            $params['product'] = $product;
        }
        
        //On formate la date des avis
        foreach ($reviews as $key => $review) {
            $reviews[$key]["date_time"] = self::formatDate($review["date_time"], $id_lang);
            if ($reviews[$key]["order_date"] and (strtotime($reviews[$key]["order_date"])>0)) {
                $reviews[$key]["order_date"] = self::formatOrderDate($review["order_date"], $id_lang);
            }
            else {
                $reviews[$key]["order_date"] = false;
            }
        }
        
        
        $sagLogo = self::getImg($id_lang, "steavisgarantis_logo_");
        $url=self::getCertificateUrl($this->context->language->id);
        $this->context->smarty->assign(array(
            'reviews' => $reviews,
            'ratingValues' => $ratingValues,
            'nbOfReviews' => $nb,
            'reviewsAverage' => round($rating, 1),
            'certificateUrl'=> $url,
            'showStructured'=> Configuration::get('steavisgarantis_showStructured'),
            'product'=> $params['product'],
            'reviewTabStr' => $this->l('Customer reviews'),
            'sagLogo' => $sagLogo,
            'id_lang'=>$this->context->language->id
        ));
        if (version_compare(_PS_VERSION_, '1.6.0', '<') == Configuration::get("steavisgarantis_normalBehaviour")) {
            return $this->display(__FILE__, 'views/templates/front/displayProductTabContent.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/front/displayProductTabContent16.tpl');
        }
    }
 
    //New product tab and tab content for PS1.7
    public function hookDisplayProductExtraContent($params)
    {
        $productID = (int)(Tools::getValue('id_product'));
        $id_lang = (int)$this->context->language->id;
        //Récupération du nombre d'avis
        $sqlQuery = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='$productID' and id_lang='$id_lang'";
        $nb= Db::getInstance()->getValue($sqlQuery);
        $sqlQuery = "SELECT * FROM "._DB_PREFIX_."steavisgarantis_average_rating WHERE product_id='$productID' and id_lang='$id_lang'";
        $ratingValues= Db::getInstance()->getRow($sqlQuery);
        $rating=$ratingValues['rate'];
        $array = array();
        if ($nb > 0) {
            $nbMaxReviews=10;
            $sqlQuery = "SELECT * FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='$productID' and id_lang='$id_lang' ORDER BY date_time DESC LIMIT $nbMaxReviews";
            $reviews = Db::getInstance()->ExecuteS($sqlQuery);

            //Récupération de l'objet produit
            if (!isset($params['product'])) {
                if (!$id_product = Tools::getValue('id_product')) {
                    return $this->l('Missing product object. Set new object mode from the back office Configuration');
                }

                $product = new Product($id_product, false, $id_lang);
                $params['product'] = $product;
            }
            
            //On formate la date des avis
            foreach ($reviews as $key => $review) {
                $reviews[$key]["date_time"] = self::formatDate($review["date_time"], $id_lang);
                if ($reviews[$key]["order_date"] and (strtotime($reviews[$key]["order_date"])>0)) {
                    $reviews[$key]["order_date"] = self::formatOrderDate($review["order_date"], $id_lang);
                }
                else {
                    $reviews[$key]["order_date"] = false;
                }
            }

            $sagLogo = self::getImg($id_lang, "steavisgarantis_logo_");
            $url=self::getCertificateUrl($id_lang);
            $this->context->smarty->assign(array(
                'reviews' => $reviews,
                'ratingValues' => $ratingValues,
                'nbOfReviews' => $nb,
                'reviewsAverage' => round($rating, 1),
                'certificateUrl'=> $url,
                'showStructured'=> Configuration::get('steavisgarantis_showStructured'),
                'product'=> $params['product'],
                'modules_dir' => _MODULE_DIR_,
                'reviewTabStr' => $this->l('Customer reviews'),
                'sagLogo' => $sagLogo,
                'id_lang'=>$this->context->language->id
            ));
            $output = ($this->display(__FILE__, 'views/templates/front/displayProductTabContent.tpl'));
            $productExtraContent = new PrestaShop\PrestaShop\Core\Product\ProductExtraContent();
            $array[] = $productExtraContent->setTitle($this->l('Customer reviews'))->setContent($output);
        }

        return $array;
    }

    //new rightcolumnproduct for ps 1.7
    public function hookDisplayProductButtons($params)
    {
        //Seulement si on est en 1.7
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            $productID = (int)(Tools::getValue('id_product'));
            $id_lang = (int)$this->context->language->id;
            $sqlQuery = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='$productID' and id_lang='$id_lang'";
            $nb= Db::getInstance()->getValue($sqlQuery);
            $sqlQuery = "SELECT rate FROM "._DB_PREFIX_."steavisgarantis_average_rating WHERE product_id='$productID' and id_lang='$id_lang'";
            $rating= Db::getInstance()->getValue($sqlQuery);
            if ($nb < 1) {
                return ""; //Si Aucun avis, on retourne vide
            }

            $sagLogo = self::getImg($id_lang, "steavisgarantis_logo_badge_");
            
            $this->context->smarty->assign(array(
                'nbReviews' => $nb,
                'reviewRate' =>  $rating,
                'sagLogoBadge' =>  $sagLogo,
                ));
            return $this->display(__FILE__, 'views/templates/front/displayRightColumnProduct.tpl');
        }
    }
 
    public function hookdisplayRightColumnProduct()
    {
        $productID = (int)(Tools::getValue('id_product'));
        $id_lang = (int)$this->context->language->id;
        $sqlQuery = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='$productID' and id_lang='$id_lang'";
        $nb= Db::getInstance()->getValue($sqlQuery);
        $sqlQuery = "SELECT rate FROM "._DB_PREFIX_."steavisgarantis_average_rating WHERE product_id='$productID' and id_lang='$id_lang'";
        $rating= Db::getInstance()->getValue($sqlQuery);
        if ($nb < 1) {
            return ""; //Si Aucun avis, on retourne vide
        }

        $sagLogo = self::getImg($id_lang, "steavisgarantis_logo_badge_");
        $this->context->smarty->assign(array(
            'nbReviews' => $nb,
            'reviewRate' =>  $rating,
            'sagLogoBadge' =>  $sagLogo,
            ));
        return $this->display(__FILE__, 'views/templates/front/displayRightColumnProduct.tpl');
    }
 
    public function getContent()
    {
        $output = null;

        //Si on a soumis le formulaire de création de certificat
        if (Tools::getValue('createCertificate')) {
            if (!Tools::getValue('cgv_1')) { //Si on a pas validé les CGV on renvoie une erreur
                $output .= $this->displayError($this->l('You must accept our terms and conditions to continue'));
            } elseif (!(filter_var(Tools::getValue('steavisgarantis_accountMail'), FILTER_VALIDATE_EMAIL))) {
                $output .= $this->displayError($this->l('You must enter a valid email address to continue'));
            } elseif (!Tools::getValue('steavisgarantis_certificate_lang')) {
                $output .= $this->displayError($this->l('You must choose a language'));
            } else {
                //Define on which domain we have to create certificate
                $certifLang = Tools::getValue('steavisgarantis_certificate_lang');
                $domain = self::getDomainUrlFromLang($certifLang);
                
                $datas = self::createCertificate(
                    $domain,
                    Tools::getValue('api_siteName'),
                    Tools::getValue('steavisgarantis_accountAddress'),
                    Tools::getValue('steavisgarantis_accountAddress2'),
                    Tools::getValue('steavisgarantis_accountCP'),
                    Tools::getValue('steavisgarantis_accountCity'),
                    Tools::getValue('steavisgarantis_accountMail'),
                    Configuration::get('PS_LOGO')
                );
                if ($datas["apiKey"]) { //Si on a une réponse contenant une clé d'api
                
                    //On met à jour la clé d'api pour toutes les langues
                    $languages = Language::getLanguages(true, Context::getContext()->shop->id);
                    foreach ($languages as $language) {
                        Configuration::updateValue('steavisgarantis_apiKey_'.$language["id_lang"], $datas["apiKey"]);    //On enregistre l'apiKey
                    }
                    Configuration::updateValue('steavisgarantis_accountMail', Tools::getValue('steavisgarantis_accountMail')); //On enregistre le mail du compte
                    Configuration::updateValue('steavisgarantis_password', $datas["password"]); //Et le mot de passe
                    Configuration::updateValue('steavisgarantis_apiKeyFromApi', $datas["apiKey"]); //Et la clé générée depuis l'api

                    //Et on active tous les widgets
                    Configuration::updateValue('steavisgarantis_widgetPosition', "left");
                    Configuration::updateValue('steavisgarantis_widgetJavascript', true);
                    Configuration::updateValue('steavisgarantis_footerLink', true);
                    //On regénère l'url du certificat + sauvegarde
                    $urlCertificat = self::setCertificateUrlFromAPI();
                    $output .= $this->displayConfirmation($this->l('Certification successfully created : ') . $urlCertificat);
                } else {
                    //Si on a déjà un certificat
                    if ($datas["error"] == "Domain already registered") {
                        $getApiKeyUrl = $domain . 'configuration/prestashop/';
                        $output .= $this->displayConfirmation($this->l('This website is already registered, to get your Api Key, clic here : ') . $getApiKeyUrl);
                    }

                    //Si l'email existe déjà
                    elseif (array_key_exists("existing_user_login", $datas["error"]) or array_key_exists("existing_user_email", $datas["error"])) {
                        $output .= $this->displayError($this->l('Given email address already used with another account. Thanks for entering another address.'));
                    }

                    //Sinon on a une erreur mais on ne sait pas pourquoi
                    else {
                        //var_dump ($datas);
                        $registerUrl = $domain . 'wp-login.php?action=register';
                        $output .= $this->displayConfirmation($this->l('Thanks to follow this url to finish your registration : ') . $registerUrl);
                        Tools::redirect($registerUrl);
                        exit();
                    }
                }
            }
        } elseif (Tools::getValue('mainConfig')) { //Sinon si on a soumis le formulaire de configuration principal

            //On récupère les données passéees
            $steavisgarantis_afterDays = Tools::getValue('steavisgarantis_afterDays');
            if ($steavisgarantis_afterDays<1) {
                $steavisgarantis_afterDays = 1;
                $output .= $this->displayError($this->l('Email sending delay mustn\'t be less than 1 day.'));
            }

            
            $steavisgarantis_showStructured = (Tools::getValue('steavisgarantis_showStructured'));
            $steavisgarantis_normalBehaviour = (Tools::getValue('steavisgarantis_normalBehaviour'));
            $steavisgarantis_widgetPosition = Tools::getValue('steavisgarantis_widgetPosition');
            $steavisgarantis_widgetJavascript = (Tools::getValue('steavisgarantis_widgetJavascript'));
            $steavisgarantis_footerLink = (Tools::getValue('steavisgarantis_footerLink'));
            $steavisgarantis_includeStatus = (Tools::getValue('steavisgarantis_includeStatus'));
            $steavisgarantis_includeStatusString = "";
            if ($steavisgarantis_includeStatus) {
                $steavisgarantis_includeStatusString = implode(",", $steavisgarantis_includeStatus);
            }
            
            //On récupère et met à jour les clé d'api pour chaque langue
            $languages = Language::getLanguages(true, Context::getContext()->shop->id);
            foreach ($languages as $language) {
                $steavisgarantis = Tools::getValue('steavisgarantis_apiKey_'.$language["id_lang"]);
                Configuration::updateValue('steavisgarantis_apiKey_'.$language["id_lang"], $steavisgarantis);
            }
            Configuration::updateValue('steavisgarantis_includeStatus', $steavisgarantis_includeStatusString);
            Configuration::updateValue('steavisgarantis_afterDays', $steavisgarantis_afterDays);
            Configuration::updateValue('steavisgarantis_widgetPosition', $steavisgarantis_widgetPosition);
            Configuration::updateValue('steavisgarantis_widgetJavascript', $steavisgarantis_widgetJavascript);
            Configuration::updateValue('steavisgarantis_footerLink', $steavisgarantis_footerLink);
            Configuration::updateValue('steavisgarantis_showStructured', $steavisgarantis_showStructured);
            Configuration::updateValue('steavisgarantis_normalBehaviour', $steavisgarantis_normalBehaviour);
            //On regénère l'url du certificat + sauvegarde
            self::setCertificateUrlFromAPI();
            $output .= $this->displayConfirmation($this->l('Successfully updated'));
        } elseif (Tools::getValue('alreadyUser')) { //Si on a soumis le formulaire "déjà inscrit"
            $steavisgarantis = Tools::getValue('steavisgarantis_apiKey');
            //On vérifie qu'on a une api KEY
            if (!$steavisgarantis) {
                $output .= $this->displayError($this->l('You must enter an Api Key to continue'));
            } else { //Si c'est bon, on active tous les widgets
                $steavisgarantis_widgetPosition ="left";
                $steavisgarantis_footerLink = true;
                $steavisgarantis_widgetJavascript = true;
                $output .= $this->displayConfirmation($this->l('Module successfully configured, widgets activated'));
                
                //On a entré une clé, on la met pour toutes les langues
                $languages = Language::getLanguages(true, Context::getContext()->shop->id);
                foreach ($languages as $language) {
                    Configuration::updateValue('steavisgarantis_apiKey_'.$language["id_lang"], $steavisgarantis);
                }
                Configuration::updateValue('steavisgarantis_widgetPosition', $steavisgarantis_widgetPosition);
                Configuration::updateValue('steavisgarantis_widgetJavascript', $steavisgarantis_widgetJavascript);
                Configuration::updateValue('steavisgarantis_footerLink', $steavisgarantis_footerLink);
                Configuration::updateValue('steavisgarantis_accountMail', ""); //On efface le mail du compte
                Configuration::updateValue('steavisgarantis_password', ""); //Et le mot de passe
                //On regénère l'url du certificat + sauvegarde
                self::setCertificateUrlFromAPI();
            }
        }
        
        switch ($this->context->language->iso_code) {
            case "fr" : $cgvUrl = "https://www.societe-des-avis-garantis.fr/cgv/"; break;
            default   : $cgvUrl = "https://www.guaranteed-reviews-company.com/terms/"; break;
        }
        $this->context->smarty->assign(array(
            'cgvUrl' => $cgvUrl,
        ));
        return $output . $this->displayForm() . $this->display(__FILE__, 'views/templates/front/displayConfiguration.tpl');
    }
 
    public function displayForm()
    {
        $stateList = array();
        
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        //Get order state list
        $sqlQuery = "SELECT * FROM "._DB_PREFIX_."order_state_lang where id_lang=$default_lang";
        $orderStates = Db::getInstance()->ExecuteS($sqlQuery);
        
        //Format datas
        foreach ($orderStates as $orderState) {
            $stateList[] = array("key" => $orderState['id_order_state'], "name"=>$orderState['name']);
        }
        
        $installedForm = array();
        // Init Fields form array
        $installedForm['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Included statuses'),
                    'class' => "steavisgarantisIncludeStatus",
                    'name' => 'steavisgarantis_includeStatus[]',
                    'desc' => $this->l('Select order statuses you want to send review requests
                    (Use "Ctrl" keyboard key to select many ones)'),
                    'multiple' => true,
                    'options' => array(
                        'query' => $stateList,
                        'id' => 'key',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Review request delay'),
                    'name' => 'steavisgarantis_afterDays',
                    'size' => 30,
                    'desc' => $this->l('Number of days before sending review request (after order passed to an included statuses).'),
                    'required' => false
                ),

                //Choix de l'emplacement du widget iFrame
                array(
                  'type'      => 'radio',
                  'label'     => $this->l('Widget iFrame'),
                  'desc'      => $this->l('Left / Right display only if your theme uses columns'),
                  'name'      => 'steavisgarantis_widgetPosition',
                  'required'  => true,
                'class'     => 't',
                 'is_bool'   => false,
                  'values'    => array(
                    array(
                      'id'    => 'active_on',
                      'value' => "left",
                      'label' => $this->l('Left')
                    ),
                    array(
                      'id'    => 'active_off',
                      'value' => "right",
                      'label' => $this->l('Right')
                    ),
                    array(
                      'id'    => 'active_footer',
                      'value' => "footer",
                      'label' => $this->l('Footer')
                    ),
                    array(
                      'id'    => 'active_none',
                      'value' => "none",
                      'label' => $this->l('Disable')
                    )
                  ),
                ),
                //Choix de l'emplacement du widget Javascript
                array(
                  'type'      => 'radio',
                  'label'     => $this->l('Widget Javascript'),
                  'desc'      => $this->l('To change this widget and position, go to Guaranteed Reviews Company website'),
                  'name'      => 'steavisgarantis_widgetJavascript',
                  'required'  => true,
                'class'     => 't',
                 'is_bool'   => false,
                  'values'    => array(
                    array(
                      'id'    => 'wjs_on',
                      'value' => true,
                      'label' => $this->l('Enabled')
                    ),
                    array(
                      'id'    => 'wjs_off',
                      'value' => false,
                      'label' => $this->l('Disabled')
                    )
                  ),
                ),

                //Choix de si on affiche ou pas le lien de verif du certificat dans le footer
                array(
                  'type'      => 'radio',
                  'label'     => $this->l('Checking link'),
                  'desc'      => $this->l('Display a checking link in the footer pointing to your certification page. (Important for your SEO)'),
                  'name'      => 'steavisgarantis_footerLink',
                  'required'  => true,
                'class'     => 't',
                 'is_bool'   => true,  
                  'values'    => array( 
                    array(
                      'id'    => 'showFooterLink_on',
                      'value' => 1,
                      'label' => $this->l('Enable')
                    ),
                    array(
                      'id'    => 'showFooterLink_off',
                      'value' => 0,
                      'label' => $this->l('Disable')
                    )
                  ),
                ),

                //Choix de si on déclare les données structurées "Product" et "Product name" ou pas sur les fiches produit
                array(
                  'type'      => 'radio',
                  'label'     => $this->l('Force structured datas'),
                  'desc'      => $this->l('Enable only if your theme doesn\'t implement them. Check your product pages with Google structured datas testing tool'),
                  'name'      => 'steavisgarantis_showStructured',
                  'required'  => true,
                'class'     => 't',
                 'is_bool'   => true,
                  'values'    => array( 
                    array(
                      'id'    => 'showStructured_on',
                      'value' => 1,
                      'label' => $this->l('Yes')
                    ),
                    array(
                      'id'    => 'showStructured_off',
                      'value' => 0,
                      'label' => $this->l('No')
                    )
                  ),
                ),
                
                
                //Pour les avis sur les fiches produit, fonctionnement normal ou pas
                array(
                  'type'      => 'radio',
                  'label'     => $this->l('Theme compatibility'),
                  'desc'      => $this->l('Correct potential reviews widget display conflict on product pages'),
                  'name'      => 'steavisgarantis_normalBehaviour',
                  'required'  => true,
                'class'     => 't',
                 'is_bool'   => true,
                  'values'    => array(
                    array(
                      'id'    => 'normalBehaviour_on',
                      'value' => 1,
                      'label' => $this->l('Normal')
                    ),
                    array(
                      'id'    => 'normalBehaviour_off',
                      'value' => 0,
                      'label' => $this->l('Retro')
                    )
                  ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button btn btn-default',
                'name' => 'mainConfig'
            )
        );
    
        //Init Api inputs fields
        $languages = Language::getLanguages(true, Context::getContext()->shop->id);
        foreach ($languages as $language) {
            $reviewManagement = array(
                'type' => 'text',
                'label' => $this->l('Api Key - Lang ') . $language["name"],
                'name' => 'steavisgarantis_apiKey_'.$language["id_lang"],
                'size' => 30,
                'required' => false
            );
            $currentApiKey = Configuration::get('steavisgarantis_apiKey_'.$language["id_lang"]);
            if ($currentApiKey) {
                $domainUrl = self::getDomainUrl($currentApiKey);
                $reviewManagement["desc"] = $this->l('Reviews management : ') . $domainUrl;
            }
            //Si on est sur la clé d'api générée depuis l'api
            if ($currentApiKey == Configuration::get('steavisgarantis_apiKeyFromApi') and $currentApiKey) {
                //On affiche les identifiants de connexion
                $reviewManagement["desc"] .= $this->l(' - Login : ') . Configuration::get('steavisgarantis_accountMail');
                $reviewManagement["desc"] .= $this->l(' - Password : ') . Configuration::get('steavisgarantis_password');
            }
            array_unshift($installedForm['form']['input'], $reviewManagement);
        }

        

        //Formulaire d'inscription
        $firstInstall = array();
        $firstInstall['form'] = array(
            'legend' => array('title'=>$this->l('New user ?')),
            'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Shop name'),
                        'name' => 'api_siteName',
                        'size' => 30,
                        'desc' => $this->l('Will be on your certification page'),
                        'required' => true
                    ),
                    array(
                        'type' => 'hidden',
                        'label' => $this->l('Address'),
                        'name' => 'steavisgarantis_accountAddress',
                        'size' => 30,
                        'desc' => $this->l('Will be on your certification page'),
                        'required' => false
                    ),
                    array(
                        'type' => 'hidden',
                        'label' => $this->l('Address 2'),
                        'name' => 'steavisgarantis_accountAddress2',
                        'size' => 30,
                        'desc' => $this->l('Will be on your certification page'),
                        'required' => false
                    ),
                    array(
                        'type' => 'hidden',
                        'label' => $this->l('Postal code'),
                        'name' => 'steavisgarantis_accountCP',
                        'size' => 30,
                        'desc' => $this->l('Will be on your certification page'),
                        'required' => false
                    ),
                    array(
                        'type' => 'hidden',
                        'label' => $this->l('City'),
                        'name' => 'steavisgarantis_accountCity',
                        'size' => 30,
                        'desc' => $this->l('Will be on your certification page'),
                        'required' => false
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Your email'),
                        'name' => 'steavisgarantis_accountMail',
                        'size' => 30,
                        'desc' => $this->l('Will be used as Guaranteed Reviews Company account login'),
                        'required' => true
                    ),
                    array(
                      'type' => 'select',
                      'label' => $this->l('Lang:'),
                      'name' => 'steavisgarantis_certificate_lang',
                      'required' => true,
                      'options' => array(
                        'query' => array(
                                      array(
                                        'certificate_lang_id' => "fr",
                                        'lang' => $this->l('French')
                                      ),
                                      array(
                                        'certificate_lang_id' => "en",
                                        'lang' => $this->l('English')
                                      ),
                                    ),
                        'id' => 'certificate_lang_id',
                        'name' => 'lang'
                      )
                    ),
                    array(
                      'type'    => 'checkbox',
                      'label'   => $this->l(''), 
                      'name'    => 'cgv', 
                      'values'  => array(
                        'query' => array(
                                      array(
                                        'id_option' => 1,
                                        'name' => $this->l('I accept Guaranteed Reviews Company\'s Terms and conditions : https://www.guaranteed-reviews-company.com/terms/'),
                                        'class' => 'cgv_link',
                                      ),
                                    ), 
                        'id'    => 'id_option',
                        'name'  => 'name'
                      ),
                    ),
            ),
            'submit' => array(
                'title' => $this->l('Submit'),
                'name' => "createCertificate",
                'class' => 'button btn btn-default'
            )
        );

        //Déjà inscrit ?
        $alreadyInstalled = array();
        $alreadyInstalled['form'] = array(
            'legend' => array('title'=>$this->l('Already registered ?')),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Api Key'),
                    'name' => 'steavisgarantis_apiKey',
                    'size' => 30,
                    'desc' => $this->l('Find your Api Key on the PrestaShop page of Guaranteed Reviews Company\'s website'),
                    'required' => true
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => "alreadyUser",
                'class' => 'button btn btn-default'
            )
        );
        
        //Gestion des formulaires à afficher
        $fields_form = array();
        //Si on a aucune clé d'api, on affiche "Première installation", et "Déjà inscrit?"
        $atLeastOneApiKey = 0;
        $languages = Language::getLanguages(true, Context::getContext()->shop->id);
        foreach ($languages as $language) {
            if (Configuration::get('steavisgarantis_apiKey_'.$language["id_lang"])) {
                $atLeastOneApiKey = 1;
            }
        }
        if (!$atLeastOneApiKey) {
            array_unshift($fields_form, $alreadyInstalled);
            array_unshift($fields_form, $firstInstall);
        }
        ///Sinon on affiche le formulaire classique
        else {
            array_unshift($fields_form, $installedForm);
        }

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['steavisgarantis_includeStatus[]'] = explode(",", Configuration::get('steavisgarantis_includeStatus'));
        
        //Load Api Keys for each language
        $languages = Language::getLanguages(true, Context::getContext()->shop->id);
        foreach ($languages as $language) {
            $helper->fields_value['steavisgarantis_apiKey_'.$language["id_lang"]] = Configuration::get('steavisgarantis_apiKey_'.$language["id_lang"]);
        }
        $helper->fields_value['steavisgarantis_afterDays'] = Configuration::get('steavisgarantis_afterDays');
        $helper->fields_value['steavisgarantis_widgetPosition'] = Configuration::get('steavisgarantis_widgetPosition');
        $helper->fields_value['steavisgarantis_widgetJavascript'] = Configuration::get('steavisgarantis_widgetJavascript');
        $helper->fields_value['steavisgarantis_footerLink'] = Configuration::get('steavisgarantis_footerLink');
        $helper->fields_value['steavisgarantis_showStructured'] = Configuration::get('steavisgarantis_showStructured');
        $helper->fields_value['steavisgarantis_normalBehaviour'] = Configuration::get('steavisgarantis_normalBehaviour');
        $helper->fields_value['steavisgarantis_accountMail'] = Configuration::get('PS_SHOP_EMAIL');
        $helper->fields_value['api_siteName'] = Configuration::get('PS_SHOP_NAME');
        $helper->fields_value['steavisgarantis_accountAddress'] = Configuration::get('PS_SHOP_ADDR1');
        $helper->fields_value['steavisgarantis_accountAddress2'] = Configuration::get('PS_SHOP_ADDR2');
        $helper->fields_value['steavisgarantis_certificate_lang'] = "fr";
        $helper->fields_value['steavisgarantis_accountCP'] = Configuration::get('PS_SHOP_CODE');
        $helper->fields_value['steavisgarantis_accountCity'] = Configuration::get('PS_SHOP_CITY');
        $helper->fields_value['steavisgarantis_password'] = Configuration::get('steavisgarantis_password');
        //Mandatory to avoid notice on install form
        $helper->fields_value['steavisgarantis_apiKey'] = Configuration::get('steavisgarantis_apiKey');

        return $helper->generateForm($fields_form);
    }

    
    
    //////////////////////////////////////////////////////////////////////////////////
    //                                                                              //
    //                           COMMON FUNCTIONS                                   //
    //                                                                              //
    //////////////////////////////////////////////////////////////////////////////////
    
    
    //Permet de savoir à quel domaine on doit s'adresser en fonction d'une langue : en, fr...
    public static function getDomainUrlFromLang($lang)
    {
        switch ($lang) {
            case "fr": $url = "https://www.societe-des-avis-garantis.fr/"; break;
            case "en": $url = "https://www.guaranteed-reviews-company.com/"; break;
            default: $url = "https://www.societe-des-avis-garantis.fr/"; break;
        }
        return $url;
    }
    
    
    public static function getShopId($lang_id)
    {
        $apiKey=Configuration::get('steavisgarantis_apiKey_' . $lang_id);
        return Tools::substr($apiKey, 0, strpos($apiKey, "/"));
    }

    //Fonction permettant de déduire le domaine en fonction de la clé d'Api
    public static function getDomainUrl($apiKey)
    {
        $nudeApiKey = Tools::substr($apiKey, strpos($apiKey, "/") +1);
        $lang = Tools::substr($nudeApiKey, 0, strpos($nudeApiKey, "/"));
        return self::getDomainUrlFromLang($lang);
    }
    
    //Fonction permettant de déduire le domaine en fonction de la clé d'Api
    public static function getImg($lang_id, $name)
    {
        $lang = self::getLangFromApiKey(Configuration::get('steavisgarantis_apiKey_'. $lang_id));
        return $name . $lang . ".png";
    }

    //public static function to get lang from apiKey
    public static function getLangFromApiKey($apiKey)
    {
        $nudeApiKey = Tools::substr($apiKey, strpos($apiKey, "/") +1);
        $lang = Tools::substr($nudeApiKey, 0, strpos($nudeApiKey, "/"));
        //Si on ne trouve pas la langue dans la clé c'est qu'on est sur une ancienne typo de clé
        if ($lang != "en" and $lang != "fr" and $lang != "be") {
            $lang = "fr";
        }
        return $lang;
    }
    
    
    //Format date depending on lang from apiKey
    public static function formatDate($date, $lang_id)
    {
        $lang = self::getLangFromApiKey(Configuration::get("steavisgarantis_apiKey_".$lang_id));
        switch ($lang) {
            case "fr": $dateStr = date("d/m/Y", $date) . " à " . date("H:i", $date);break;
            case "en": $dateStr = date("M d\, Y", $date) . " at " . date("h:i a", $date);break;
            default: break;
        }
        return $dateStr;
    }    
    
    //Format date depending on lang from apiKey
    public static function formatOrderDate($date, $lang_id)
    {
        $lang = self::getLangFromApiKey(Configuration::get("steavisgarantis_apiKey_".$lang_id));
        switch ($lang) {
            case "fr": $dateStr = date("d/m/Y", strtotime($date));break;
            case "en": $dateStr = date("M d\, Y", strtotime($date));break;
            default: break;
        }
        return $dateStr;
    }



    public static function getApiKeyFromLang($lang)
    {
        $languages = Language::getLanguages(true, Context::getContext()->shop->id);
        //Pour chaque langue active, on recupère la potentielle clé d'api
        foreach ($languages as $language) {
            //Si on a une clé d'api
            if ($apiKey = Configuration::get('steavisgarantis_apiKey_'.$language["id_lang"])) {
                //On en déduit la langue (en, fr..)
                $nudeApiKey = Tools::substr($apiKey, strpos($apiKey, "/") +1);
                $apiLang = Tools::substr($nudeApiKey, 0, strpos($nudeApiKey, "/"));
                //Si la langue de la clé d'api correspond à la langue demandée en paramètre
                if ($lang == $apiLang) {
                    return $apiKey;
                }
            }
        }
        
        //Si on arrive là c'est qu'on a trouvé aucune clé d'api
        foreach ($languages as $language) {
            if ($apiKey = Configuration::get('steavisgarantis_apiKey_'.$language["id_lang"])) {
                //On vérifie que la clé d'api ne renseigne pas déjà une langue
                $nudeApiKey = Tools::substr($apiKey, strpos($apiKey, "/") +1);
                $apiLang = Tools::substr($nudeApiKey, 0, strpos($nudeApiKey, "/"));
                if ($apiLang != "fr" and $apiLang != "en" and $apiLang != "be") {
                    //Très forte probabilité qu'on ai un ancien format de clé d'api
                    return $apiKey;
                }
            }
        }
    }

    public static function getLangsId($lang)
    {
        $langIds = array();
        $languages = Language::getLanguages(true, Context::getContext()->shop->id);
        //Pour chaque langue active, on recupère la potentielle clé d'api
        foreach ($languages as $language) {
            //Si on a une clé d'api
            if ($apiKey = Configuration::get('steavisgarantis_apiKey_'.$language["id_lang"])) {
                //On en déduit la langue (en, fr..)
                $nudeApiKey = Tools::substr($apiKey, strpos($apiKey, "/") +1);
                $apiLang = Tools::substr($nudeApiKey, 0, strpos($nudeApiKey, "/"));
                //Si la langue de la clé d'api correspond à la langue demandée en paramètre
                if ($lang == $apiLang) {
                    $langIds[] = $language["id_lang"];
                }
            }
        }
        
        //Si on a aucune correspondance ça veut dire qu'on a un ancien format de clé d'api ne contenant pas la langue
        if (!count($langIds)) {
            //On prend en compte toutes les langues actives qui ont une clé d'api associée
            foreach ($languages as $language) {
                if ($apiKey = Configuration::get('steavisgarantis_apiKey_'.$language["id_lang"])) {
                    //On vérifie que la clé d'api ne renseigne pas déjà une langue
                    $nudeApiKey = Tools::substr($apiKey, strpos($apiKey, "/") +1);
                    $apiLang = Tools::substr($nudeApiKey, 0, strpos($nudeApiKey, "/"));
                    if ($apiLang != "fr" and $apiLang != "en" and $apiLang != "be") {
                        //Très forte probabilité qu'on ai un ancien format de clé d'api
                        $langIds[] = $language["id_lang"];
                    }
                }
            }
        }
        return $langIds;
    }

    //Fonction permettant de regénérer l'url de certificat
    public static function setCertificateUrlFromAPI($lang_id = false)
    {
        //
        //Connexion à l'API via cURL
        //
        //Si on ne sait pas pour quelle langue on doit mettre à jour l'url du certificat
        if (!$lang_id) {
            $languages = Language::getLanguages(true, Context::getContext()->shop->id);
            foreach ($languages as $language) {
                //On met à jour de façon récursive
                $urlCertificate = self::setCertificateUrlFromAPI($language["id_lang"]);
            }
        }
        //Sinon on sait et on met à jour
        else {
            $apiKey = Configuration::get('steavisgarantis_apiKey_'. $lang_id);
            $url_ag=self::getDomainUrl($apiKey);
            $ch = curl_init();
            $timeout = 5; // set to zero for no timeout
            curl_setopt($ch, CURLOPT_URL, $url_ag."wp-content/plugins/ag-core/api/getInfos.php?method=certificateUrl&apiKey=".$apiKey);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = str_replace("\xEF\xBB\xBF", '', curl_exec($ch));
            curl_close($ch);
            $urlCertificate = $file_contents;
            Configuration::updateValue('steavisgarantis_certificateUrl_'. $lang_id, $urlCertificate);
        }
        return $urlCertificate;
    }

    //Fonction retournant l'Url de la page du certificat (page qui montre les avis)
    public static function getCertificateUrl($lang_id)
    {
        //On va chercher en base l'url du certificat
        if (Configuration::get('steavisgarantis_certificateUrl_' . $lang_id)) {
            return Configuration::get('steavisgarantis_certificateUrl_' . $lang_id);
        }
        //Si on ne la trouve pas en base, on demande à l'Api et on l'enregistre
        else {
            return self::setCertificateUrlFromAPI($lang_id);
        }
    }
     
    public static function createCertificate($domain, $siteName ="", $address1 ="", $address2 ="", $CP ="", $city ="", $email ="", $logo = "")
    {
        
        //On vérifie qu'on a bien un mail
        if (!$email) {
            return array("apiKey" => false);
        }

        //On urlencode
        $siteName = urlencode($siteName);
        $address1 = urlencode($address1);
        $address2 = urlencode($address2);
        $CP       = urlencode($CP);
        $city = urlencode($city);
        $email = urlencode($email);
        if (!$logo) {
            $logo = "logo.jpg";
        }
        $logo = urlencode($logo);
        $raison_sociale = $siteName;

        //On récupère l'url racine
        $url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__;

        $logoUrl = $url . "/img/" . ltrim($logo, '/'); //On enleve le premier caractère de $logo si c'est un slash
        $params = "cms=prestashop&email=$email&url=$url&address1=$address1&address2=$address2&CP=$CP&city=$city&logo_url=$logoUrl&raison_sociale=$raison_sociale";
        $apiUrl = $domain . SAGAPIENDPOINT . "createCertificate.php?" . $params;
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $datas = curl_exec($ch);
        $datas = json_decode($datas, true);

        return $datas;
    }

    public static function sendUpdatePath()
    {
        $apiKey = urlencode(Configuration::get('steavisgarantis_apiKey'));
        $domain = self::getDomainUrlFromLang("fr"); //Car il n'y a que dans cette langue que la version nommée "SAG" du module existait
        $params = "apiKey=". $apiKey;
        $url = $domain . SAGAPIENDPOINT . "updatePath.php?" . $params;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $datas = curl_exec($ch);
        return $datas;
    }
    
    //Permet de migrer toutes les anciennes versions sur la nouvelle structure de table
    public static function updateDataTable()
    {
        //On insert les nouvelles colonnes
        $tables = array("steavisgarantis_reviews", "steavisgarantis_average_rating");
        foreach ($tables as $table) {
            $table = pSQL($table);
            //On supprime la table qui servait à sauvegarder
            $sql = "DROP TABLE if exists ". _DB_PREFIX_ . $table ."_save";
            Db::getInstance()->execute($sql);
            
            //On sauvegarde l'ancienne table en changeant son nom
            $sql = "RENAME TABLE ". _DB_PREFIX_ . $table ." TO ". _DB_PREFIX_ . $table ."_save";
            Db::getInstance()->execute($sql);
        }

        //On installe la nouvelle
        STEAVISGARANTIS::installDatabase();

        //On met les anciennes données dans la nouvelle table steavisgarantis_reviews
        $sql = "INSERT INTO ". _DB_PREFIX_ . "steavisgarantis_reviews (id_product_avisg, product_id, ag_reviewer_name, rate, review, date_time, answer_text, answer_date_time)
        SELECT id_product_avisg, product_id, ag_reviewer_name, rate, review, date_time, answer_text, answer_date_time FROM ". _DB_PREFIX_ . "steavisgarantis_reviews_save";
        Db::getInstance()->execute($sql);

        //On met les anciennes données dans la nouvelle table steavisgarantis_average_rating
        $sql = "INSERT INTO ". _DB_PREFIX_ . "steavisgarantis_average_rating (id_product_avisg, product_id, rate, percent1, percent2, percent3, percent4, percent5, nb1, nb2, nb3, nb4, nb5, date_time_update, reviews_nb)
        SELECT id_product_avisg, product_id, rate, percent1, percent2, percent3, percent4, percent5, nb1, nb2, nb3, nb4, nb5, date_time_update, reviews_nb FROM ". _DB_PREFIX_ . "steavisgarantis_average_rating_save";
        Db::getInstance()->execute($sql);

        foreach ($tables as $table) {
            $table = pSQL($table);
            //On configure la langue par défaut de la boutique sur les avis produits existant
            $default_lang = (Configuration::get('PS_LANG_DEFAULT') ? Configuration::get('PS_LANG_DEFAULT') : 1);
            $sql = "UPDATE ". _DB_PREFIX_ . $table ." SET id_lang = '" . (int)$default_lang . "'";
            Db::getInstance()->execute($sql);

            //On supprime la table qui servait à sauvegarder
            $sql = "DROP TABLE ". _DB_PREFIX_ . $table ."_save";
            Db::getInstance()->execute($sql);
        }
    }
    
    
    //////////////////////////////////////////////////////////////////////////////////
    //                                                                              //
    //                              API FUNCTIONS                                   //
    //                                                                              //
    //////////////////////////////////////////////////////////////////////////////////

    //
    //Function to clean datas
    //
    public static function removeBOM($data)
    {
        if (0 === strpos(bin2hex($data), 'efbbbf')) {
            return Tools::substr($data, 3);
        }
    }

    
    //
    //Déclaration de la fonction de récupération des avis
    //
    public static function importProductReviews($url_ag, $apiKey, $productID, $idSAG, $from, $minDate, $maxDate, $maxResults, $token, $update)
    {

        //Préparation des paramètres à passer en variable
        $productID =  $productID ? '&productID='.$productID : '';                         //Filtre sur l'ID produit
        $idSAG =  $idSAG ? '&idSAG='.$idSAG : '';     //Filtre sur l'ID de l'avis
        $from =  $from ? '&from='.$from : '';                                             //Si from est à 1, $idSAG deviens le point de départ des avis à récupérer
        $minDate =  $minDate ? '&minDate='.$minDate : '';                                 //Filtre sur la date de l'avis
        $maxDate =  $maxDate ? '&maxDate='.$maxDate : '';                                 //Filtre sur la date de l'avis
        $maxResults = $maxResults ? '&maxR='.$maxResults : '';                             //Valeur de la clause sql LIMIT
        $token = $token ? '&token='.$token : '';                             //Valeur de la clause sql LIMIT
        $apiUrl= $url_ag."wp-content/plugins/ag-core/api/reviews.php?apiPost=1" . $productID . $idSAG . $from . $minDate . $maxDate . $maxResults . $token;

        //
        //Connexion à l'API via cURL
        //
        $ch = curl_init();
        echo $apiUrl;
        $timeout = 5; // set to zero for no timeout
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                "apiKey=".urlencode($apiKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        

        //
        //Exploitation des données récupérées
        //
        $file_contentsWithoutBom=self::removeBOM($file_contents);
        //si on peut enlever le bom on l'enlève
        if ($file_contentsWithoutBom) {
            $file_contents=$file_contentsWithoutBom;
        }

        $file_contents=json_decode($file_contents, true); //Décodage du contenu JSON récupéré

        //Si on a une erreur de decodage JSON
        if (json_last_error()) {
            var_dump(json_last_error());
            //echo "erreur JSON?";
        }

        //Pour chaque avis
        foreach ($file_contents as $val) {
            //On détermine la ou les langues concernées par la langue de l'avis
            $langsId = STEAVISGARANTIS::getLangsId($val["lang"]);
            foreach ($langsId as $langId) {
                
                $langId = (int)$langId;
                $updateAverage=0;    //Initialisation variable déterminant si on doit updater la moyenne des avis
                //echo $val["review_status"];

                //on va vérifier qu'il n'existe pas dans la base de données
                $sql = "SELECT * FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE id_product_avisg=".(int)$val["idSAG"]." and id_lang='$langId'";

                //Si l'avis a le statut 0 (en attente) ou 2 (supprimé)
                if ($val["review_status"]==0 or $val["review_status"]==2) {
                    //Et qu'il existe, il faut le supprimer
                    if (Db::getInstance()->ExecuteS($sql)) {
                        //Supprimer l'avis
                        //echo "Il faut supprimer cet avis";
                        if (version_compare(_PS_VERSION_, '1.5', '<')) {
                            $table = _DB_PREFIX_ . 'steavisgarantis_reviews';
                        } else {
                            $table = 'steavisgarantis_reviews';
                        }
                        //On delete la ligne dans la table
                        Db::getInstance()->delete($table, 'id_product_avisg='.(int)$val["idSAG"], 1);
                        //echo "Supprimé avec succès";
                        $updateAverage=1;                                //On passe updateAverage à true pour updater les moyennes des avis
                    } else {
                        //echo "RAS"; //On a rien à faire, l'avis est soit déjà supprimé soit en attente
                    }
                }
                //Sinon l'avis a le statut validé (1)
                else {
                    //Si l'avis est déjà dans la base de données
                    if (Db::getInstance()->ExecuteS($sql)) {

                        //Et que $update est à true, on update l'avis
                        if ($update) {
                            //echo "Enregistrement déjà présent, on update";
                            //Construction du nom
                            $lastName = ($val['lastname'] ? " " . Tools::strtoupper($val['lastname'][0]) . "." : "");
                            $reviewerName = Tools::ucfirst($val["reviewer_name"]) . $lastName;
                            $datas = array(
                            'id_product_avisg' => (int)$val["idSAG"],
                            'product_id' => (int)$val["idProduct"],
                            'rate' => (int)$val["review_rating"],
                            'review' => pSQL($val["review_text"]),
                            'ag_reviewer_name' => pSQL($reviewerName),
                            'date_time'=> (int)strtotime($val["date_time"]),
                            'answer_text' => pSQL($val["answer_text"]),
                            'answer_date_time' => pSQL($val["answer_date_time"]),
                            'order_date' => pSQL($val["order_date"]),
                            'id_lang' => (int)$langId,
                            );

                            if (version_compare(_PS_VERSION_, '1.5', '<')) {
                                Db::getInstance()->autoExecute(_DB_PREFIX_.'steavisgarantis_reviews', $datas, 'UPDATE', 'id_product_avisg='.(int)$val["idSAG"]);
                            } else {
                                Db::getInstance()->update('steavisgarantis_reviews', $datas, 'id_product_avisg='.(int)$val["idSAG"]);
                            }
                            //echo "Updaté avec succès";
                            $updateAverage=1;                                //On passe updateAverage à true pour updater les moyennes des avis
                        }
                        //Sinon $update est à false et on passe à l'avis suivant
                        else {
                            //echo "Enregistrement déjà présent, on update pas et on passe à la suite";
                        }
                    }
                    //Sinon l'enregistrement n'existe pas et on l'insert
                    else {
                        //echo "Enregistrement non présent, il faut l'insérer";
                        //Construction du nom à afficher
                        $lastName = ($val['lastname'] ? " " . Tools::strtoupper($val['lastname'][0]) . "." : "");
                        $reviewerName = Tools::ucfirst($val["reviewer_name"]) . $lastName;
                        $datas = array(
                            'id_product_avisg' => (int)$val["idSAG"],
                            'product_id' => (int)$val["idProduct"],
                            'rate' => (int)$val["review_rating"],
                            'review' => pSQL($val["review_text"]),
                            'ag_reviewer_name' => pSQL($reviewerName),
                            'date_time'=> (int)strtotime($val["date_time"]),
                            'answer_text' => pSQL($val["answer_text"]),
                            'answer_date_time' => pSQL($val["answer_date_time"]),
                            'order_date' => pSQL($val["order_date"]),
                            'id_lang' => (int)$langId,
                            );
                        //Presta <1.5
                        if (version_compare(_PS_VERSION_, '1.5', '<')) {
                            Db::getInstance()->autoExecute(_DB_PREFIX_.'steavisgarantis_reviews', $datas, 'INSERT');
                            //$err = Db::getInstance()->getMsgError();
                            //var_dump ($err);
                        } else {
                            Db::getInstance()->insert('steavisgarantis_reviews', $datas);
                        }
                        //echo "Ajouté avec succès";
                        $updateAverage=1;                                //On passe updateAverage à true pour updater les moyennes des avis
                    }
                }
         
                //echo "idSAG" . $val["idSAG"] . "   ";
                //echo "idProduct" . $val["idProduct"] . "   ";
                //echo "Note" . $val["review_rating"] . "   ";
                //echo "State" . $val["review_status"] . "   ";

                //Si on a fait une insertion, update, ou suppression, il faut updater la table des moyennes d'avis et la répartition des notes + pourcent
                if ($updateAverage) {
                    //On récupère le nombre d'avis pour ce produit
                    $sql = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='".(int)$val["idProduct"]."' and id_lang='$langId'";
                    $nb= Db::getInstance()->getValue($sql);

                    //On récupère la somme des notes pour ce produit
                    $sql = "SELECT SUM(rate) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='".(int)$val["idProduct"]."' and id_lang='$langId'";
                    $somme_review= Db::getInstance()->getValue($sql);

                    //On calcule la note moyenne
                    if ($nb>0) {
                        $rate=round($somme_review/$nb, 2);
                    } else {
                        $rate=0;
                    }

                    //On détermine le nombre d'avis inférieur ou égal à 1
                    $sql = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='".(int)$val["idProduct"]."' and rate <= 1 and id_lang='$langId'";
                    $nb1= Db::getInstance()->getValue($sql);

                    //On détermine le nombre d'avis à 2
                    $sql = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='".(int)$val["idProduct"]."' and rate <= 2 and rate > 1 and id_lang='$langId'";
                    $nb2= Db::getInstance()->getValue($sql);

                    //On détermine le nombre d'avis à 3
                    $sql = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='".(int)$val["idProduct"]."' and rate <= 3 and rate > 2 and id_lang='$langId'";
                    $nb3= Db::getInstance()->getValue($sql);

                    //On détermine le nombre d'avis à 4
                    $sql = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='".(int)$val["idProduct"]."' and rate <= 4 and rate > 3 and id_lang='$langId'";
                    $nb4= Db::getInstance()->getValue($sql);

                    //On détermine le nombre d'avis à 5
                    $sql = "SELECT count(*) FROM "._DB_PREFIX_."steavisgarantis_reviews WHERE product_id='".(int)$val["idProduct"]."' and rate <= 5 and rate > 4 and id_lang='$langId'";
                    $nb5= Db::getInstance()->getValue($sql);

                    //On détermine le pourcentage à 1
                    $percent1 = round($nb1/$nb, 2) * 100;
                    $percent2 = round($nb2/$nb, 2) * 100;
                    $percent3 = round($nb3/$nb, 2) * 100;
                    $percent4 = round($nb4/$nb, 2) * 100;
                    $percent5 = round($nb5/$nb, 2) * 100;

                    //On regarde si on a déjà une ligne de moyenne pour ce produit
                    $sql = "SELECT * FROM "._DB_PREFIX_."steavisgarantis_average_rating WHERE product_id='".(int)$val["idProduct"]."' and id_lang='$langId'";
                    //Si on a des résultats, on update la ligne
                    if (Db::getInstance()->ExecuteS($sql)) {
                        $datas = array(
                        'id_product_avisg' => (int)$val["idSAG"],
                        'product_id' => (int)$val["idProduct"],
                        'rate' => pSQL($rate),                  //pSQL car peut avoir une virgule
                        'reviews_nb' => (int)$nb,
                        'percent1' => pSQL($percent1),
                        'percent2' => pSQL($percent2),
                        'percent3' => pSQL($percent3),
                        'percent4' => pSQL($percent4),
                        'percent5' => pSQL($percent5),
                        'nb1'       => (int)$nb1,
                        'nb2'       => (int)$nb2,
                        'nb3'       => (int)$nb3,
                        'nb4'       => (int)$nb4,
                        'nb5'       => (int)$nb5,
                        'date_time_update'=> (int)strtotime($val["date_time"]),
                        'id_lang' => (int)$langId,
                        );

                        if (version_compare(_PS_VERSION_, '1.5', '<')) {
                            Db::getInstance()->autoExecute(_DB_PREFIX_.'steavisgarantis_average_rating', $datas, 'UPDATE', 'product_id='.(int)$val["idProduct"]);
                            //$err = Db::getInstance()->getMsgError();
                            //var_dump ($err);
                        } else {
                            //On update une ligne dans la table
                            Db::getInstance()->update('steavisgarantis_average_rating', $datas, 'product_id='.(int)$val["idProduct"]);
                        }
                        //echo "Updaté avec succès";
                    }
                    //Sinon on insert une nouvelle ligne
                    else {
                        $nb=1;
                        $datas = array(
                            'id_product_avisg' => (int)$val["idSAG"],
                            'product_id' => (int)$val["idProduct"],
                            'rate' => pSQL($rate),                      //pSQL car peut avoir une virgule
                            'reviews_nb' => (int)$nb,
                            'percent1' => pSQL($percent1),
                            'percent2' => pSQL($percent2),
                            'percent3' => pSQL($percent3),
                            'percent4' => pSQL($percent4),
                            'percent5' => pSQL($percent5),
                            'nb1'       => (int)$nb1,
                            'nb2'       => (int)$nb2,
                            'nb3'       => (int)$nb3,
                            'nb4'       => (int)$nb4,
                            'nb5'       => (int)$nb5,
                            'date_time_update'=> (int)strtotime($val["date_time"]),
                            'id_lang' => (int)$langId,
                            );
                        if (version_compare(_PS_VERSION_, '1.5', '<')) {
                            Db::getInstance()->autoExecute(_DB_PREFIX_.'steavisgarantis_average_rating', $datas, 'INSERT');
                        } else {
                            Db::getInstance()->insert('steavisgarantis_average_rating', $datas);
                        }
                        //echo "Ligne de moyenne créée avec succès";
                    }
                }
            }
        }
    }
    

    //
    //Verify a token
    //
    public static function tokenCheck($token, $lang)
    {
        $domainUrl = STEAVISGARANTIS::getDomainUrlFromLang($lang);
        $apiKey = urlencode(STEAVISGARANTIS::getApiKeyFromLang($lang));
        $url = $domainUrl . SAGAPIENDPOINT . "checkToken.php?token=" . $token . "&apiKey=" . $apiKey;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        return curl_exec($ch);
    }
    

    //
    //Securely connect and post data to API
    //
    public static function postData($data, $dest, $token, $lang)
    {
        $domainUrl = STEAVISGARANTIS::getDomainUrlFromLang($lang);
        $apiKey = urlencode(STEAVISGARANTIS::getApiKeyFromLang($lang));
        $url = $domainUrl . SAGAPIENDPOINT . $dest ."?token=$token&apiKey=" . $apiKey;
        $dataString = base64_encode(json_encode($data));    //Remote API only accepts Base64 encoded datas
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("data" => $dataString));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        return curl_exec($ch);
    }
}

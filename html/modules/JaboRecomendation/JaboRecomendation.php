<?php
if (!defined('_PS_VERSION_')) {
  exit;
}
 
class JaboRecomendation extends Module
{

	private  $_html;
  public function __construct()
  {
    $this->name = 'JaboRecomendation';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'Mikolaj Sumowski';
    $this->need_instance = 1;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
    $this->bootstrap = true;
 
    parent::__construct();
 
    $this->displayName = $this->l('JaboRecomendation');
    $this->description = $this->l('Description of my module.');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
  }

  public function install()
	{
	if(!parent::install() ||!$this->registerHook('Top') || !$this->registerHook('sumekCMS')||!$this->registerHook('Header') )
		{
		return false;
		}
	return true;
	}

public function getContent() {
                                                                                                
    if(Tools::isSubmit('submit_text')){
	Configuration::updateValue(
		$this->name.'_path_to_java',
		Tools::getValue('the_text')
		);
	}
                                                                                                
    $this->_generateForm();
    return $this->_html;
}
                                                                                                
private function _generateForm() {
                                                                                                
    $textToShow=Configuration::get($this->name.'_path_to_java');
                                                                                                
    $this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
    $this->_html .= '<label>'.$this->l('Sciezka do pliku:: ').'</label>';
    $this->_html .= '<div class="margin-form">';
    $this->_html .= '<input type="text" name="the_text" value="'.$textToShow.'" >';
    $this->_html .= '<input type="submit" name="submit_text" ';
    $this->_html .= 'value="'.$this->l('Ustaw').'" class="button" />';
    $this->_html .= '</div>';
    $this->_html .= '</form>';
}
                                                                                                
public function hookDisplayTop() {

	$output = "";
	if(Tools::isSubmit('submit_reco')){
           	$output =  exec("java -jar JavaApplication2.jar lol");//+ Tools::getValue('RecoName'));
        }
                                                                                                
    global $smarty;
    return $this->display(__FILE__, 'JaboRecomendation.tpl');
                                                                                                
}


private function getImagesByID($id_product, $limit = 0)
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
 

public function hookHeader()
{
	Tools::addCSS(_THEME_CSS_DIR_ . 'product_list.css');
        Tools::addCSS(_THEME_CSS_DIR_ . 'cms.css');
        Tools::addCSS(_PS_MODULE_DIR_ . 'JaboRecomendation/cmsproducts.css');

}

public function hooksumekCMS(){
	if (Tools::getValue('id_cms') != 6)
        return;

	$response = "";
	$script = Configuration::get($this->name.'_path_to_java');
	$param = "20,16,19,100";
	
	$command = 'java -jar ' . $script .  ' ' . $param ;
	$response = shell_exec($command);
	//exec($command,$response);// get the last line;
	
	echo "komenda: " . $command . '<br>';
	echo "Output: " . $response . '<br>';
	echo "sciezka: " . $script . '<br>';



	Tools::addCSS(_THEME_CSS_DIR_ . 'product_list.css');
	Tools::addCSS(_THEME_CSS_DIR_ . 'cms.css');
	Tools::addCSS(_PS_MODULE_DIR_ . 'JaboRecomendation/cmsproducts.css');


	$explode_products = explode(",", $response);
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

	global $smarty;        
	$smarty->assign('products', $products);
        $smarty->assign('feedtype', "cmsProductsFeed");
        return $this->display(__FILE__ , 'JaboRecomendationProducts.tpl');


/*
	$products_id = explode(" ", $response);

	$products = [];
	foreach($products_id as $product_id)
	{
		$prod = new Product($product_id,false,$this->context->language->id);
		//$image = $this->getImagesByID(20,1);
		echo $prod->name;
		$products[] = $prod;
	}

	global $smarty;
	$smarty->assign('text',$response);
	return $this->display(__FILE__, 'JaboRecomendationCMS.tpl');
*/	}

                                                    
}

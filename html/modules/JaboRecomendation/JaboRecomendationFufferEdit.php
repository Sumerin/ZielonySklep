<?php
if (!defined('_PS_VERSION_')) 
{
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
    if(!parent::install() ||!$this->registerHook('Top') || !$this->registerHook('sumekCMS')||!$this->registerHook('Header') || !$this->registerHook('actionCartSave'))
    {
      return false;
    }
    return true;
  }

  public function getContent()
  {
                                                                                                
    if(Tools::isSubmit('submit_text'))
    {
      Configuration::updateValue(
		$this->name.'_path_to_java',
		Tools::getValue('the_text')
		);
    }
                                                                                                
      $this->_generateForm();
      return $this->_html;
    }
                                                                                                
  private function _generateForm() 
  {
                                                                                               
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
                                                                                                
  public function hookDisplayTop() 
  {
    $output = "";
    if(Tools::isSubmit('submit_reco'))
    {
      $output =  exec("java -jar JavaApplication2.jar lol");//+ Tools::getValue('RecoName'));
    }
                                                                                                
    global $smarty;
    return $this->display(__FILE__, 'JaboRecomendation.tpl');                                                          }


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

  public function hooksumekCMS($params)
  {
    if (Tools::getValue('id_cms') != 6)
    return;

    $response = "";
    $script = Configuration::get($this->name.'_path_to_java');
    $param = "20,16,19,100";

    $command = 'java -jar ' . $script .  ' ' . $param ;
    $response = shell_exec($command);
    //exec($command,$response);// get the last line;

    $views = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'do_koszyka`');
///////////////////// 
//TUTAJ $row REPREZENTUJE JEDEN WPIS, CZYLI TRZEBA TO WRZUCIC DO STRUKTURY KTORA POBIERZE MAHOUT, AKTUALNIE TYLKO WYPISYWANIE
////////////////////
    $dataFile = fopen("data.csv","w");
    $dataFileText = "";    
    foreach ($views as $row)
    {
       $dataFileText = $dataFileText . $row['customer_id'] . "," . $row[product_id] . "\n";
       // echo "views: " . $row['customer_id'] . ' i ' . $row['product_id'] . '<br>';	
    }
    fwrite($dataFile,$dataFileText);
    fclose($dataFile);
    echo "komenda: " . $command . '<br>';
    echo "Output: " . $response . '<br>';
    echo "sciezka: " . $script . '<br>';

    foreach ($params as $kurwa => $mac)
    {
      if ($kurwa == "cart" &&  get_class($mac) == "Cart")
      {
        foreach ($mac->getProducts() as $key => $val)
        {
	  ///////////////
	  //TU POZNAJEMY ID UZYTKOWNIKA ZALOGOWANEGO, MAHOUT BEDZIE WIEDZIAL KOMU POLECA
          /////////////
          $id_zalogowanego_uzytkownika = $mac->id_customer;
        }
      }
    }


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

    $global $smarty;        
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
*/	
  }

  public function hookactionCartSave($params)
  {
    //Db::getInstance()->Execute('DROP TABLE ps_do_koszyka;');
    //Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS ps_do_koszyka (product_id int, customer_id int, PRIMARY KEY(product_id,customer_id) )');
    $email = $this->context->customer->email;
    if ($email == '')
    {
      return; 
    }
    foreach ($params as $kurwa => $mac)
    {	
      if ($kurwa == "cart" &&  get_class($mac) == "Cart")
      {
        foreach ($mac->getProducts() as $key => $val)
        {	
	  $dbdata = array('product_id' => $val["id_product"], 'customer_id' => $mac->id_customer);
          $row = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'do_koszyka` WHERE product_id = ' . $val["id_product"] . ' AND customer_id = ' . $mac->id_customer . ' ');
          if ($row == '')
	  {
            Db::getInstance()->insert('do_koszyka',$dbdata);
	    //echo "<script> console.log('" . $val["id_product"] . " ');</script>";
	  }
	}
      }
    }                                                    
  }

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
	if(!parent::install() ||!$this->registerHook('home') )
		{
		return false;
		}
	return true;
	}

public function getContent() {
                                                                                                
    if(Tools::isSubmit('submit_reco')){
	Configuration::updateValue(
		$this->name.'_search',
		Tools::getValue('RecoName')
		);
	}
                                                                                                
    $this->_generateForm();
    return $this->_html;
}
                                                                                                
private function _generateForm() {
                                                                                                
    $textToShow=Configuration::get($this->name.'_text_to_show');
                                                                                                
    $this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
    $this->_html .= '<label>'.$this->l('Enter your text: ').'</label>';
    $this->_html .= '<div class="margin-form">';
    $this->_html .= '<input type="text" name="the_text" value="'.$textToShow.'" >';
    $this->_html .= '<input type="submit" name="submit_text" ';
    $this->_html .= 'value="'.$this->l('Update the text').'" class="button" />';
    $this->_html .= '</div>';
    $this->_html .= '</form>';
}
                                                                                                
public function hookDisplayHome() {

	$product = "";
	if(Tools::isSubmit('submit_reco')){
              $product = Tools::getValue('RecoName');
        }
                                                                                                
    global $smarty;
    $smarty->assign('sea',$product);
    return $this->display(__FILE__, 'JaboRecomendation.tpl');
                                                                                                
}
                                                     
}

/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function(){
	$('#home-page-tabs li:first, #index .tab-content ul:first').addClass('active');
	
	$(".item-img").each(function(){
		var title = $(this).attr("title");
		var id = parseInt(title.split('-')[0]);
		var name = title.split('-')[1];
		var posIm = $($(this).parent().parent().parent().parent()).attr("id");
		GoogleAnalyticEnhancedECommerce.addPromotionImpression(id,name,title,posIm); 
	});
	
	$(document).off('click', '.item-img').on('click','.item-img', function(e){
/*		e.preventDefault();
*/		var splitTit=$(e.target).attr("title");
		var idProm = parseInt(splitTit.split('-')[0]);
		var nameProm = splitTit.split('-')[1];
		var pos = $($(e.target).parent().parent().parent().parent()).attr("id");
		GoogleAnalyticEnhancedECommerce.addPromotionClick(idProm,nameProm,splitTit,pos);
	});

});

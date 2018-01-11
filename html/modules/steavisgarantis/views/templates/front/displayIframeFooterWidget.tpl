{*
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
*
*}

{if strpos($smarty.server.REQUEST_URI, "/commande") !== false} 
{else} 
<div id="steavisgarantis" align="center" style="
    padding: 5px 0 5px 0;
    width: 100%;
    background: white;
">
<iframe width="100%" height="125" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
   src="{$domain|escape:'htmlall':'UTF-8'}wp-content/plugins/ag-core/widgetFooter.php?id={$shopID|escape:'htmlall':'UTF-8'}">
</iframe><br />
<a style="color:#333;text-decoration:underline;" href='{$url_ag|escape:'htmlall':'UTF-8'}' target="_blank">{l s='Show more reviews' mod='steavisgarantis'}</a>
</div>
{/if} 

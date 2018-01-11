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

<link rel="stylesheet" href="{$modules_dir|escape:'htmlall':'UTF-8'}steavisgarantis/views/css/style.css" type="text/css" />
<style>
    {literal} 
    #agWidgetH .animate {animation-duration: 1s;  animation-name: newWidth;  animation-iteration-count: 1;} 
    @keyframes newWidth {from {width: 0%} to {width: {/literal}{20 * $reviewRate|escape:'htmlall':'UTF-8'}{literal}%}} 
    {/literal} 
</style>

<div id="agWidgetH" class="agWidget rad" >
    <div class="inner rad">
    <img src="{$modules_dir|escape:'htmlall':'UTF-8'}steavisgarantis/views/img/{$sagLogoBadge|escape:'htmlall':'UTF-8'}" class="logoAg">
        <div class="reviewGlobal">
        <div class="steavisgarantisStar"><span></span><span class="note animate" style="width:{20 * $reviewRate|escape:'htmlall':'UTF-8'}%"></span></div>
        <p>{l s='Based on' mod='steavisgarantis'} {$nbReviews|escape:'htmlall':'UTF-8'} {if $nbReviews==1}{l s='review' mod='steavisgarantis'}{else}{l s='reviews' mod='steavisgarantis'}{/if}</p>
        <a class="agBt rad4" onclick="showReviews(); return false;" href="#ag-s">{l s='Show reviews' mod='steavisgarantis'}</a>
        </div>
    </div>
</div>

{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2016 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER
* support@mypresta.eu
*}

{if Configuration::get('cmsproducts_hide')!=1}
    {if $feedtype == 'noProducts'}
        <p class="alert alert-warning">{l s='No products available in this feed.' mod='cmsproducts'}</p>
    {else if $feedtype != 'error'}
        <div class='row cmsproducts {$feedtype}'>
            {include file="$tpl_dir./product-list.tpl" class="products" products=$products page_name='index'}
        </div>
    {else}
        <p class="alert alert-warning">{l s='Feed of products is not available.' mod='cmsproducts'} {l s='Module:'} {$module} {l s='not found or version of this module is too old' mod='cmsproducts'}</p>
    {/if}
{/if}

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

<div id="ag-s">
{if $showStructured}
<div itemscope itemtype="http://schema.org/Product">
	<span style="display:none;" itemprop="name">{$product->name|escape:'htmlall':'UTF-8'}</span>
{/if}
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <div id="agWidgetMain" class="agWidget rad" >
            <div class="topBar">{l s='Reviews about this product' mod='steavisgarantis'}</div>
            <div class="inner bgGrey1">
            <div class="logoCont"><img src="{$modules_dir|escape:'htmlall':'UTF-8'}steavisgarantis/views/img/{$sagLogo|escape:'htmlall':'UTF-8'}" width="150px" height="35px" class="logoAg">
            <a href="{$certificateUrl|escape:'htmlall':'UTF-8'}" class="agBt certificateBtn" target="_blank">{l s='Show certification' mod='steavisgarantis'}</a>
            </div><div class="statCont">
                <div class="steavisgarantisStats">
                <div class="item"><span class="stat"><div class="note bar1" style="height:{$ratingValues['percent1']|escape:'htmlall':'UTF-8'}%"><span class="value">{$ratingValues['nb1']}</span></div></span><span class="name">1&starf;</span></div>
                <div class="item"><span class="stat"><div class="note bar2" style="height:{$ratingValues['percent2']|escape:'htmlall':'UTF-8'}%"><span class="value">{$ratingValues['nb2']}</span></div></span><span class="name">2&starf;</span></div>
                <div class="item"><span class="stat"><div class="note bar3" style="height:{$ratingValues['percent3']|escape:'htmlall':'UTF-8'}%"><span class="value">{$ratingValues['nb3']}</span></div></span><span class="name">3&starf;</span></div>
                <div class="item"><span class="stat"><div class="note bar4" style="height:{$ratingValues['percent4']|escape:'htmlall':'UTF-8'}%"><span class="value">{$ratingValues['nb4']}</span></div></span><span class="name">4&starf;</span></div>
                <div class="item"><span class="stat"><div class="note bar5" style="height:{$ratingValues['percent5']|escape:'htmlall':'UTF-8'}%"><span class="value">{$ratingValues['nb5']}</span></div></span><span class="name">5&starf;</span></div>
                </div>
            </div><div class="reviewCont"> <div class="reviewGlobal">
                <div class="largeNote"><big>{2 * $reviewsAverage|escape:'htmlall':'UTF-8'}</big>/10<p><br>{l s='Based on' mod='steavisgarantis'} {$nbOfReviews|escape:'htmlall':'UTF-8'} {if $nbOfReviews==1}{l s='review' mod='steavisgarantis'}{else}{l s='reviews' mod='steavisgarantis'}{/if}</p></div>
                </div></div>
                <meta itemprop="ratingValue" content="{$reviewsAverage|escape:'htmlall':'UTF-8'}" />
                <meta itemprop="reviewCount" content="{$nbOfReviews|escape:'htmlall':'UTF-8'}" />
                <meta itemprop="bestRating" content="5" />
            </div>
            <ul class="reviewList">
            {$i=0}
            {foreach from=$reviews item=singleReview}	

            <li class="bgGrey{$i % 2|escape:'htmlall':'UTF-8'}">
            <div class="author"><img width="24px" height="24px" src="{$modules_dir|escape:'htmlall':'UTF-8'}steavisgarantis/views/img/ico_user.png" /><span>{$singleReview['ag_reviewer_name']|escape:'htmlall':'UTF-8'}</span>
            <br><span class="time"><span class="published">{l s='Published' mod='steavisgarantis'} {$singleReview['date_time']|escape:'htmlall':'UTF-8'}</span>{if $singleReview['order_date']} ({l s='Order date: ' mod='steavisgarantis'}{$singleReview['order_date']|escape:'htmlall':'UTF-8'}){/if}</span></div>
            <div class="reviewTxt">
             <div class="steavisgarantisStar"><span></span><span class="note" style="width:{20 * $singleReview['rate']|escape:'htmlall':'UTF-8'}%"></span></div>
             <span class="metaHide">{$singleReview['rate']|escape:'htmlall':'UTF-8'}</span>
            <p class="">{$singleReview['review']|escape:'htmlall':'UTF-8'}</p>
            {if $singleReview['answer_text']}
            <div class="reponse"><span><img src="{$modules_dir|escape:'htmlall':'UTF-8'}steavisgarantis/views/img/ico_pen.png" height="12">
            {l s='Merchant\'s answer' mod='steavisgarantis'}</span>
            <p>{$singleReview['answer_text']|escape:'htmlall':'UTF-8'}</p></div>
            {/if}
            </div>
            </li>
            {$i = $i + 1}
            {/foreach}
            </ul>

            <img id="chargement" src="{$modules_dir|escape:'htmlall':'UTF-8'}steavisgarantis/views/img/page.gif" style="display:none">
            {if $nbOfReviews > 10}
                <div class="inner2">
                <a class="agBt rad4 agBtBig" href="#more-reviews" id="more-reviews"  onclick="return showMoreReviews({$nbOfReviews|escape:'html':'UTF-8'}, 2, '{$modules_dir|escape:'html':'UTF-8'}','{$id_lang|escape:'html':'UTF-8'}');" rel="2">{l s='Show more reviews' mod='steavisgarantis'}</a>
                </div>
            {/if}
        </div>
    </div>
{if $showStructured}
</div>
{/if}
</div>
<style>{literal}
.bar1 {animation-duration: 1s;  animation-name: newHeight1;  animation-iteration-count: 1;} @keyframes newHeight1 { from {height: 0%} to {height: {/literal}{$ratingValues['percent1']|escape:'htmlall':'UTF-8'}{literal}%} }
.bar2 {animation-duration: 1s;  animation-name: newHeight2;  animation-iteration-count: 1;} @keyframes newHeight2 { from {height: 0%} to {height: {/literal}{$ratingValues['percent2']|escape:'htmlall':'UTF-8'}{literal}%} }
.bar3 {animation-duration: 1s;  animation-name: newHeight3;  animation-iteration-count: 1;} @keyframes newHeight3 { from {height: 0%} to {height: {/literal}{$ratingValues['percent3']|escape:'htmlall':'UTF-8'}{literal}%} }
.bar4 {animation-duration: 1s;  animation-name: newHeight4;  animation-iteration-count: 1;} @keyframes newHeight4 { from {height: 0%} to {height: {/literal}{$ratingValues['percent4']|escape:'htmlall':'UTF-8'}{literal}%} }
.bar5 {animation-duration: 1s;  animation-name: newHeight5;  animation-iteration-count: 1;} @keyframes newHeight5 { from {height: 0%} to {height: {/literal}{$ratingValues['percent5']|escape:'htmlall':'UTF-8'}{literal}%} }
{/literal}
</style>
<script type="text/javascript">
    var reviewTabStr="{$reviewTabStr|escape:'htmlall':'UTF-8'}";
</script>
<br><br>
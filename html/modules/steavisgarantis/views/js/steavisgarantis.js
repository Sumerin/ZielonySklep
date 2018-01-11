/*
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


function showReviews() {
    
    //JQUERY COMPATIBILITY
    //var tmp = $;     // jQuery's current version becomes temporary variable.
    //$ = $j200;

    //Prestashop 1.6: Si un lien contiens "Avis clients" on clic dessus (methode propre et simple)
    if ($("h3:contains("+ reviewTabStr +")").length) {
        //On clique sur l'onglet pour l'afficher
        $( "h3:contains('"+ reviewTabStr +"')").click();

        //Défilement fluide vers l'onglet d'avis
        $('html, body').animate({
            scrollTop:$( "h3:contains('"+ reviewTabStr +"')").offset().top
        }, 'slow');
    } 
    else if ($("a:contains("+ reviewTabStr +")").length) {

        //On clique sur l'onglet pour l'afficher
        $( "a:contains("+ reviewTabStr +")").click();

        //Défilement fluide vers l'onglet d'avis
        $('html, body').animate({
            scrollTop:$( "a:contains("+ reviewTabStr +")").offset().top
        }, 'slow');
    }    

    //ps1.7
    else {
        if ($(".nav-item .nav-link").length) { //Si PS 1.7
            $(".nav-item .nav-link").each(function() {
                if ($(this)[0].innerHTML.indexOf(" avis") != -1) {
                    jQuery($(this))[0].click();
                    console.log("not fired?");
                    //Défilement fluide vers l'onglet d'avis
                    $('html, body').animate({
                    scrollTop:$(this).offset().top
                    }, 'slow');
                } 
            })
        } 
        else
        {
            document.getElementById("ag-s").className = "";
            if (document.querySelector('#idTab1') !== null) { //On déselectionne le premier onglet
            document.getElementById("idTab1").className = "rte block_hidden_only_for_screen";
            } 
            document.getElementById("tab_steavisgarantis").className = "steavisgarantis_tab selected";//On sélectionne le bon onglet
            if (document.querySelector('#more_info_tab_more_info') !== null) { //On cache l'ancien contenu
            document.getElementById("more_info_tab_more_info").className = "";
            } 
            //Défilement fluide vers l'onglet d'avis
            $('html, body').animate({
            scrollTop:$( "#ag-s").offset().top
            }, 'slow');
        } 
    } 
    //$ = tmp; //restore the default version of jQuery!
} 
 
function showMoreReviews(reviewsNb, pageNb, modulesDir, langId) {
    //JQUERY COMPATIBILITY
    //var tmp = $;     // jQuery's current version becomes temporary variable.
    //$ = $j200;
    if (Math.ceil(reviewsNb / 10) == parseInt(pageNb)) {
        $('#more-reviews').hide();
    } 

    $.ajax({
        url: modulesDir + 'steavisgarantis/load_comments.php',
        type: 'POST',
        data: {currentPage : pageNb, id_lang : langId, id_product : $('input[name="id_product"]').val(), nbOfReviews : reviewsNb} 
,
        beforeSend: function() {
            $('#chargement').show();
            save = $(".reviewList").html();

        } 
,
        success: function( html) {
            $('#chargement').hide();
            $(".reviewList").append(html);
            pageNb = pageNb + 1;

            //Same things but second line seems better for PS 1.4 (old jquery)
            //$("#more-reviews").attr("onclick","return showMoreReviews(" + reviewsNb + ", " + pageNb + ", '" + modulesDir + "');");
            $("#more-reviews").each(function() { this.attributes.onclick.nodeValue =  "return showMoreReviews(" + reviewsNb + ", " + pageNb + ", '" + modulesDir + "', '" + langId + "');"; } 
)
        } 
,
    } 
);
    return false;
    console.log($.fn.jquery);
    //$ = tmp; //restore the default version of jQuery!
} 
;



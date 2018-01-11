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

<style>
.steavisgarantisIncludeStatus{
    height: 150px;
    padding: 0px 5px;
} 
</style>

<script>
    //Get cgvUrl from smarty context
    cgvUrl = "{$cgvUrl|escape:'htmlall':'UTF-8'}";

    {literal}
                        
            //Function to open CGV in new tab
            function openInNewTab(url) {
              var win = window.open(url, '_blank');
              win.focus();  
            }
            
            //Si on soumet le formulaire
            var createCertificate = document.querySelector("[name=\'createCertificate\']");
            createCertificate && createCertificate.addEventListener("click", function() {
                var span = document.createElement("span");
                    span.innerHTML = "Veuillez patienter...";
                    span.className = "wait";

                createCertificate.parentNode.insertBefore(span, createCertificate);
                //On le cache
                createCertificate.style.display = "none";
                
                //On ouvre les CGV dans un nouvel onglet
                openInNewTab(cgvUrl);
            });
            
            var cgv = document.querySelector("label[for=\'cgv_1\']");
            cgv && cgv.addEventListener("click", function(e) {
                if(e.target.tagName.toLowerCase() != "input") {
                    openInNewTab(cgvUrl);
                }
            });
    {/literal}
</script>

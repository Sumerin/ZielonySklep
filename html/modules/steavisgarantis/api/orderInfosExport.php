<?php
/**
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

//Debug mode : affiche des echos des variables clés
$debug=0;
//Importation des avis produit
//global $smarty;
require_once('../../../config/config.inc.php');
require_once('../../../init.php');
include_once('../steavisgarantis.php');

//On récupère la clé d'API en post et on vérifie que c'est la même que celle en base
$postedApiKey = Tools::getValue('apiKey');

$languages = Language::getLanguages(true, Context::getContext()->shop->id);
//Pour chaque langue active, on recupère la potentielle clé d'api
$apiKeyOk = false;
foreach ($languages as $language) {
    //Si on a une clé d'api
    if ($apiKeyTest = Configuration::get('steavisgarantis_apiKey_'.$language["id_lang"])) {
        if ($apiKeyTest == $postedApiKey) {
            $apiKeyOk= true;
            $apiKey = $postedApiKey;
        }
    }
}

if (!$apiKeyOk) {
    echo "Wrong api key";
    exit;
} else {
    $lang = STEAVISGARANTIS::getLangFromApiKey($apiKey);
    $afterDays=10;
    $tmpAfterDays=Configuration::get('steavisgarantis_afterDays');
    if (isset($tmpAfterDays)) {
        if (is_numeric($tmpAfterDays)) {
            $afterDays=$tmpAfterDays;
        }
    }

    //Permet d'éviter de sortir les commandes auxquelles on est censé avoir déjà envoyé un mail
    $dateFrom=pSQL(date("Y-m-d H:i:s", time()-($afterDays*86400 + 86400)));
    $dateTo=pSQL(date("Y-m-d H:i:s", time()-($afterDays*86400)));

    //Récupération des id states auxquels il ne faut pas envoyer le mail
    //On initialise la variable qui permettra de générer la string sql
    $includeStatusString="";
    //On met la liste des status à inclure en array
    $includeStatus=explode(",", Configuration::get('steavisgarantis_includeStatus'));

    //Si on a des statuts à inclure
    //Pour chaque champ
    foreach ($includeStatus as $value) {
        //On vérifie que le champ n'est pas vide et est un nombre
        if (!empty($value) and is_numeric($value)) {
            $value = (int)($value);
            $includeStatusString.= " OR oh.id_order_state = " . $value;
            if ($debug) {
                echo $value;
            }
        }
    }


    //Shop Condition Multiboutique (permet de récupérer seulement les commandes de la boutique appelée)
    if (version_compare(_PS_VERSION_, '1.5', '<')) {
        $shopCond = "";
    } else {
        $shopCond = "od.id_shop = " . Context::getContext()->shop->id . " and";
        //Et le multilingue
        if ($lang) {
            $langIds = STEAVISGARANTIS::getLangsId($lang);  //La on récupère les ids lang associés à la clé d'API de la langue entrée en paramètre
            if (count($langIds)) {
                $shopCond .= " ( 0 ";
                foreach ($langIds as $langId) {
                    $shopCond .= " or od.id_lang = " . $langId . " ";
                }
                $shopCond .= " ) and";
            }
        }
    }

    //Récupération des infos de la commande
    $sql = "SELECT od.* FROM "._DB_PREFIX_."orders od, 
    "._DB_PREFIX_."order_history oh
    WHERE $shopCond oh.id_order = od.id_order AND
    oh.date_add BETWEEN '$dateFrom' AND '$dateTo' AND (0 $includeStatusString)
    group by od.id_order";
    

    if ($debug) {
        echo $sql;
    }
    if ($results0 = Db::getInstance()->ExecuteS($sql)) {
        if ($debug) {
            echo "On a des commandes...";
        }
        
        $toSendTable = array();
        foreach ($results0 as $row) {
            $id_order = (int)($row['id_order']);
            $order_date = pSQL($row['date_add']);
            $reference = pSQL($row['reference']);
            $id_customer = (int)($row['id_customer']);


            //Requete pour trouver nom et prenom du client
            $sql_customer = "SELECT
            cu.firstname,
            cu.lastname,
            cu.email
            FROM
            "._DB_PREFIX_."customer as cu,
            "._DB_PREFIX_."address as ad,
            "._DB_PREFIX_."orders as od
            WHERE
            cu.id_customer=$id_customer
            AND ad.id_address=od.id_address_delivery
            AND od.id_order=$id_order";
            if ($results = Db::getInstance()->ExecuteS($sql_customer)) {
                foreach ($results as $row) {
                    $prenom = $row['firstname'];
                    $lastname = $row['lastname'];
                    $email = $row['email'];
                }
            }

            //Requete pour trouver la liste des produits commandés
            $products="";
            $sql_product = "SELECT * FROM "._DB_PREFIX_."order_detail WHERE `id_order`=$id_order";
            if ($results = Db::getInstance()->ExecuteS($sql_product)) {
                foreach ($results as $row) {
                    if (!$products) {
                        $products = $row['product_id'];
                    } else {
                        $products = $products."+".$row['product_id'];
                    }
                }
            }

            $id_site=STEAVISGARANTIS::getShopId($lang);
            $mailToSend=array("firstname" => $prenom,
                              "lastname" => $lastname,
                              "id_order" => $id_order,
                              "id_site" => $id_site,
                              "products" => $products,
                              "email" => $email,
                              "reference" => $reference,
                              "order_date" => $order_date);
            $toSendTable[]=$mailToSend;
        }

        echo json_encode($toSendTable);
    } else {
        if ($debug) {
            echo "Aucune commande à afficher.";
        }
    }
}

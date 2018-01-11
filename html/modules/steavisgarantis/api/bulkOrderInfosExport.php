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

//Fichier permettant l'export des X dernières commandes.

//Debug mode : affiche des echos des variables clés
$debug=0;
//Importation des avis produit
//global $smarty;
require_once('../../../config/config.inc.php');
require_once('../../../init.php');
include_once('../steavisgarantis.php');

//Fonctionnement
//STEAVISGARANTIS appelle le Client (bulkOrderInfosExport.php) en transmettant un token et le nombre de commande à exporter
//Le client (bulkOrderInfosExport.php) appelle STEAVISGARANTIS pour vérifier la validité du token
//Si le token est bon on va chercher la liste des commandes à exporter
//On envoie la liste des commandes à exporter à la fonction postData
//La fonction postData envoie les données en post en SSL avec la clé d'API

//On récupère les dates des dernières commandes à extraire
$fromDate = pSQL(Tools::getValue("fromDate"));
$toDate = pSQL(Tools::getValue("toDate"));
$lang = pSQL(Tools::getValue("lang"));              //not lang id, this is not an int
if (!$fromDate) {
    echo "Missing fromDate";
    exit;
}
if (!$toDate) {
    echo "Missing toDate";
    exit;
}
if (!$lang) {
    echo "Missing lang";
    exit;
}

//On récupère le token et on vérifie que l'on ai bien le droit d'envoyer les données
$token= Tools::getValue("token");
if (!$token) {
    echo "Missing token";
    exit;
}
$checkAnswer = STEAVISGARANTIS::tokenCheck($token, $lang);
if (strpos($checkAnswer, "ValidSagData")===false) {
    if ($debug) {
        echo "Wrong token: $checkAnswer ";
        var_dump($checkAnswer);
    }
    exit;
} else {
    //Debug
    if ($debug) {
        echo "On affiche les commandes du $fromDate au $toDate -";
    }

    //Récupération des id states auxquels il ne faut pas envoyer le mail
    //On initialise la variable qui permettra de générer la string sql
    $includeStatusString="";
    //On met la liste des status à inclure en array
    $includeStatus=explode(",", Configuration::get('steavisgarantis_includeStatus'));

    if ($debug) {
        echo "Statuts à inclure: ";
    }


    //Si on a des statuts à inclure
    //Pour chaque champ
    foreach ($includeStatus as $value) {
        //On vérifie que le champ n'est pas vide et est un nombre
        if (!empty($value) and is_numeric($value)) {
            $value = (int)($value);
            $includeStatusString.=" OR oh.id_order_state = " . $value;
            if ($debug) {
                echo $value;
            }
        }
    }

    //Shop Condition Multiboutique (permet de récupérer seulement les commandes de la boutique appelée)
    if (version_compare(_PS_VERSION_, '1.5', '<')) {
        $shopCond = "";
    } else {
        $shopId = (int)Context::getContext()->shop->id;
        //On gère le multiboutique
        $shopCond = "od.id_shop = " . $shopId . " and";
        //Et le multilingue
        if ($lang) {
            $langIds = STEAVISGARANTIS::getLangsId($lang);  //La on récupère les ids lang associés à la clé d'API de la langue entrée en paramètre
            if (count($langIds)) {
                $shopCond .= " ( 0 ";
                foreach ($langIds as $langId) {
                    $langId = (int)$langId;
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
    oh.date_add BETWEEN '$fromDate' AND '$toDate' AND (0 $includeStatusString)
    group by od.id_order order by `date_add` desc";
    
    
    
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
            $products=array();
            $sql_product = "SELECT * FROM "._DB_PREFIX_."order_detail WHERE `id_order`=$id_order";
            if ($results = Db::getInstance()->ExecuteS($sql_product)) {
                foreach ($results as $row) {
                    $products[] = $row;
                }
                foreach ($products as $key => $product) {
                    $products[$key]["id"] = $product["product_id"];
                    $products[$key]["name"] = $product["product_name"];
                    $products[$key]["ean13"] = $product["product_ean13"];
                    $products[$key]["upc"] = $product["product_upc"];
                    unset($products[$key]["product_id"]);
                    unset($products[$key]["product_name"]);
                    unset($products[$key]["product_ean13"]);
                    unset($products[$key]["product_upc"]);
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
        if ($debug) {
            echo "Var_dump de toSendTable: ";
            var_dump($toSendTable);
        }

        //On envoie les données en post avec cryptage SSL
        $posted = STEAVISGARANTIS::postData($toSendTable, "bulkOrderInfos.php", $token, $lang);
        if ($debug) {
            var_dump($posted);
        }
    } else {
        if ($debug) {
            echo "Aucune commande à afficher.";
        }
    }
}

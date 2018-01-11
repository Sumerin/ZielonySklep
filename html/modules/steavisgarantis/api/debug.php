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

require_once('../../../config/config.inc.php');
require_once('../../../init.php');
include_once('../steavisgarantis.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$testUrl = "https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/api/debug.php";
$ch = curl_init();
echo $testUrl;
$timeout = 5; // set to zero for no timeout
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$file_contents = curl_exec($ch);
curl_close($ch);

var_dump($file_contents);

//Says if id_lang exists
$sql = "SHOW COLUMNS FROM "._DB_PREFIX_."steavisgarantis_reviews LIKE 'id_lang'";
$exists = (Db::getInstance()->ExecuteS($sql) ? true : false);
echo "id_lang column exists in reviews table ?";
var_dump($exists);

//Says if id_lang exists in steavisgarantis_average_rating
$sql = "SHOW COLUMNS FROM "._DB_PREFIX_."steavisgarantis_average_rating LIKE 'id_lang'";
$exists = (Db::getInstance()->ExecuteS($sql) ? true : false);
echo "id_lang column exists in reviews table ?";
var_dump($exists);

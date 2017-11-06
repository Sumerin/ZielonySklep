import os
import re
import requests
import unicodedata
from urllib.request import urlopen
from urllib.request import urlretrieve
from enum import Enum

if __name__ == '__main__':

    tesco_url = {'Owoce_i_warzywa':{'warzywa':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/owoce-warzywa/warzywa/all?page=1',
                                'owoce':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/owoce-warzywa/owoce/all?page=1',
                                'grzyby':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/owoce-warzywa/grzyby/all?page=1',
                                'orzechy_i_ziarniste':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/owoce-warzywa/orzechy-i-ziarniste/all?page=1'},
             'Nabial_i_jaja':{'mleko':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/mleko/all?page=1',
                              'smietana':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/smietana/all?page=1',
                              'jaja':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/jaja/all?page=1',
                              'jogurty':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/jogurty/all?page=1',
                              'ser':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/ser/all?page=1',
                              'maslo_i_margaryna':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/maslo-i-margaryna/all?page=1'},
             'Pieczywo_i_cukiernia':{'chleb':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/pieczywo-cukiernia/chleb/all?page=1',
                                     'bulki':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/pieczywo-cukiernia/bulki/all?page=1',
                                     'cukiernia':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/pieczywo-cukiernia/cukiernia/all?page=1'},
             'Napoje':{'woda':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/napoje/woda/all?page=1',
                        'soki':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/napoje/soki-nektary-napoje-owocowe/all?page=1',
                        'napoje_gazowane':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/napoje/napoje-gazowane/all?page=1',
                        'napoje_niegazowane':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/napoje/napoje-niegazowane/all?page=1'},
             'Mrozonki':{'warzywa_i_owoce':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/mrozonki/mrozone-warzywa-i-owoce/all?page=1',
                         'lody':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/mrozonki/lody/all?page=1',
                         'pizza_i_frytki':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/mrozonki/mrozone-pizza-i-frytki/all?page=1',
                         'dania_mrozone':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/mrozonki/dania-mrozone/all?page=1'}
                        
             }

    type = 1
    sub_type = 1
    id=0
    file_index=1
    php_file = 'produkty'
    max_size = 542598
    utf_error = False
    htmlCodes = (
            ("'", '&#39;'),
            ('"', '&quot;'),
            ('>', '&gt;'),
            ('<', '&lt;'),
            ('&', '&amp;')
        )
    
    file_content="""<?php
    include(dirname(__FILE__).'/config/config.inc.php');
    include(dirname(__FILE__).'/init.php');
    $default_lang = Configuration::get(\'PS_LANG_DEFAULT\');\n
    
    function copyImg($id_entity, $id_image = null, $url, $entity = 'products')
	{
		$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
		$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

		switch ($entity)
		{
			default:
			case 'products':
				$image_obj = new Image($id_image);
				$path = $image_obj->getPathForCreation();
			break;
			case 'categories':
				$path = _PS_CAT_IMG_DIR_.(int)$id_entity;
			break;
		}

		// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
		if (!ImageManager::checkImageMemoryLimit($url))
		
			return false;

		// 'file_exists' doesn't work on distant file, and getimagesize make the import slower.
		// Just hide the warning, the traitment will be the same.
		if (@copy($url, $tmpfile))
		{
			ImageManager::resize($tmpfile, $path.'.jpg');
			$images_types = ImageType::getImagesTypes($entity);
			foreach ($images_types as $image_type)
				ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height']);

			if (in_array($image_type['id_image_type'], $watermark_types))
				Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
		}
		else
		{
			unlink($tmpfile);
			print('ssss');
			return false;
		}
		unlink($tmpfile);
		return true;
	}
    
    """
    with open(php_file+'%i.php' % file_index, 'w') as fi:
        fi.write(file_content)
        fi.close()
    product_content=''
    for kategoria, podkategoria in tesco_url.items():
        file_content=''
        file_content="""$category = new Category();
        $category->name = [$default_lang => '%s'];
$category->id_parent=Configuration::get('PS_HOME_CATEGORY');
$category->link_rewrite=[$default_lang => '%s'];
$category->add();
        """ % (kategoria,kategoria)
        with open(php_file+'%i.php' % file_index, 'a') as fi:
            fi.write(file_content)
            fi.close()
        for nazwa, strona_z_produktami in podkategoria.items():
                page_number = 1
                file_content=''
                file_content="""$subcategory = new Category();
        $subcategory->name = [$default_lang => '%s'];
$subcategory->id_parent=$category->id;
$subcategory->link_rewrite=[$default_lang => '%s'];
$subcategory->add();
        """ % (nazwa,nazwa)
                with open(php_file+'%i.php' % file_index, 'a') as fi:
                    fi.write(file_content)
                    fi.close()
                while True:
                    try:
                        with urlopen(strona_z_produktami) as response:
                            html_response = response.read()
                            encoding = response.headers.get_content_charset('utf-8')
                            decoded_html = html_response.decode(encoding)
                            decoded_html = decoded_html.replace('&quot;','\'')
                            filename = '%s.txt' % nazwa
                            products = re.findall(r"\'product'[^\s]*\'title\':\'[^']*\'[^\s]*\'shortDescription\':\'[^']*\'[^\s]*\'unitPrice\'", decoded_html)
                            for product in products: 
                                if utf_error == True:
                                    utf_error = False
                                    continue
                                product_content=''
                                full_title = re.findall(r"\'title\':\'[^']*\'", product)
                                #title = re.findall(r"[^']*", title_with_quote)

                                full_defaultImageUrl = re.findall(r"\'defaultImageUrl\':\'[^']*\'", product)
                                #defaultImageUrl = re.findall(r"[^']*", defaultImageUrl_with_quote)

                                full_price = re.findall(r"\'price\':[^']*\'", product)
                                index=0
                                while isinstance(full_title, list) and index < len(full_title):
                                    product_content += "$product = new Product();\n"
                                    product_content += "$image = new Image();\n"
                                    id+=1 

                                    title_with_quote = re.findall(r"\'[^']*\'", full_title[index])
                                    title = re.findall(r"[^']*", title_with_quote[1])[1]
                                    title_with_quote[1]=unicodedata.normalize('NFKD', title_with_quote[1]).encode('ascii', 'ignore')
                                    title_with_quote[1]=title_with_quote[1].decode('ascii')
                                    title_with_quote[1] = title_with_quote[1].replace(" ","_")
                                    for code in htmlCodes:
                                        title_with_quote[1] = title_with_quote[1].replace(code[1], code[0])

                                    product_content += "$product->name = [$default_lang => %s];\n" % title_with_quote[1]
                                    
                                    cut_title=(title_with_quote[1][:10]+'\'') if len(title_with_quote[1])>10 else title_with_quote[1]
                                    cut_title=cut_title.translate(str.maketrans("!@#*&","_____"))
                                    product_content += "$product->link_rewrite = [$default_lang => %s];\n" % cut_title

                                    defaultImageUrl_with_quote = re.findall(r"\'[^']*\'", full_defaultImageUrl[index])[1]
                                    defaultImageUrl = re.findall(r"[^']*", defaultImageUrl_with_quote)[1]
                                    file_name =  defaultImageUrl.split('/')[len(defaultImageUrl.split('/'))-1]

                                    urlretrieve(defaultImageUrl, "images/%s.png" % title)

                                    price_with_quote = re.findall(r":[^']*", full_price[index])
                                    price = re.findall(r"[^:,]*", price_with_quote[0])
                                    product_content += "$product->price = %2.2f;\n" % float(price[1])

                                    product_content += "$product->quantity = 10;\n"
                                    product_content += "$product->id_category =[$category->id,$subcategory->id];\n" 
                                    product_content += "$product->id_category_default = $category->id;\n" 

                                    product_content += """if ($product->add())
                {
                $product->updateCategories($product->id_category);
                StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
                }
            """
                                    product_content+="""$image->id_product = $product->id;
$image->position = Image::getHighestPosition($product->id) + 1;
$image->cover = true; // or false;
$shops = Shop::getShops(true, null, true);
if (($image->validateFields(false, true)) === true &&
($image->validateFieldsLang(false, true)) === true && $image->add())
{
	
    $image->associateTo($shops);
    if (!copyImg($product->id, $image->id, 'images/%s.png'))
    {
    
        $image->delete();
    }
    
}\n""" %(title)
                                    with open(php_file+'%i.php' % file_index, 'a') as fi:
                                        try:
                                            fi.write(product_content)
                                            fi.close()
                                        except Exception as e:
                                            fi.close()
                                            utf_error = True
                                            break
                                    index+=1
                    except Exception as e:
                        break

                    page_number += 1
                    strona_z_produktami = strona_z_produktami[:-1] + (str(page_number))
                sub_type += 1
        type += 1







    #LOCK TABLES `ps_product` WRITE;
    #/*!40000 ALTER TABLE `ps_product` DISABLE KEYS */;
    #INSERT INTO `ps_product` VALUES (8,0,1,2,1,0,0,0,'','',0.000000,0,1,1.000000,0.000000,'',0.000000,0.00,'12341234','','',0.000000,0.000000,0.000000,0.000000,2,0,0,0,0,1,'404',0,1,'0000-00-00','new',1,1,'both',0,0,0,0,'2017-10-20 22:54:14','2017-10-20 22:57:10',0,3),(9,0,0,2,1,0,0,0,'','',0.000000,0,1,1.000000,0.000000,'',0.000000,0.00,'','','',0.000000,0.000000,0.000000,0.000000,2,0,0,0,0,1,'404',0,1,'0000-00-00','new',1,1,'both',0,0,0,0,'2017-10-20 22:58:00','2017-10-20 22:59:34',0,3);
    #/*!40000 ALTER TABLE `ps_product` ENABLE KEYS */;
    #UNLOCK TABLES;


    #LOCK TABLES `ps_product_attribute` WRITE;
    #/*!40000 ALTER TABLE `ps_product_attribute` DISABLE KEYS */;
    #INSERT INTO `ps_product_attribute` VALUES (1,1,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,1,1,'0000-00-00'),(2,1,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(3,1,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(4,1,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(5,1,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(6,1,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(7,2,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,1,1,'0000-00-00'),(8,2,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(9,2,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(10,2,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(11,2,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(12,2,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(13,3,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,1,1,'0000-00-00'),(14,3,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(15,3,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(16,4,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,1,1,'0000-00-00'),(17,4,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(18,4,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(19,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,1,1,'0000-00-00'),(20,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(21,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(22,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(23,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(24,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(25,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(26,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(27,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(28,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(29,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(30,5,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(31,6,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,1,1,'0000-00-00'),(32,6,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(33,6,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(34,7,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,1,1,'0000-00-00'),(35,7,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(36,7,'','','','','',0.000000,0.000000,0.000000,100,0.000000,0.000000,NULL,1,'0000-00-00'),(37,7,'','','','','',0.000000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00'),(38,7,'','','','','',0.000000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00'),(39,7,'','','','','',6.150000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00'),(40,6,'','','','','',0.000000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00'),(41,6,'','','','','',0.000000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00'),(42,6,'','','','','',0.000000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00'),(43,4,'','','','','',0.000000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00'),(44,4,'','','','','',0.000000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00'),(45,4,'','','','','',0.000000,0.000000,0.000000,0,0.000000,0.000000,NULL,1,'0000-00-00');
    #/*!40000 ALTER TABLE `ps_product_attribute` ENABLE KEYS */;
    #UNLOCK TABLES; 

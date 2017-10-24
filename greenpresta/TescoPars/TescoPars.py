import os
import re
import requests
from urllib.request import urlopen
from urllib.request import urlretrieve
from enum import Enum

tesco_url = {'Owoce i warzywa':{'warzywa':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/owoce-warzywa/warzywa/all?page=1',
                                'owoce':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/owoce-warzywa/owoce/all?page=1',
                                'grzyby':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/owoce-warzywa/grzyby/all?page=1',
                                'orzechy i ziarniste':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/owoce-warzywa/orzechy-i-ziarniste/all?page=1'},
             'Nabial i jaja':{'mleko':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/mleko/all?page=1',
                              'smietana':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/smietana/all?page=1',
                              'jaja':'https://ezakupy.tesco.pl/groceries/pl-PL/shop/nabial-i-jaja/jaja/all?page=1'}}

type = 1
sub_type = 1
id=0
php_file = 'produkty.php'
file_content="""<?php
include(dirname(__FILE__).'/config/config.inc.php');
include(dirname(__FILE__).'/init.php');
$default_lang = Configuration::get(\'PS_LANG_DEFAULT\');\n"""
for kategoria, podkategoria in tesco_url.items():
    for nazwa, strona_z_produktami in podkategoria.items():
        page_number = 1
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
                        full_title = re.findall(r"\'title\':\'[^']*\'", product)
                        #title = re.findall(r"[^']*", title_with_quote)

                        full_defaultImageUrl = re.findall(r"\'defaultImageUrl\':\'[^']*\'", product)
                        #defaultImageUrl = re.findall(r"[^']*", defaultImageUrl_with_quote)

                        full_price = re.findall(r"\'price\':[^']*\'", product)
                        index=0
                        while isinstance(full_title, list) and index < len(full_title):
                            file_content += "$product = new Product(%s);\n" % str(id)
                            file_content += "$image = new Image();"
                            id+=1 

                            title_with_quote = re.findall(r"\'[^']*\'", full_title[index])
                            title = re.findall(r"[^']*", title_with_quote[1])[1]

                            file_content += "$product->name = [$default_lang => %s];\n" % title_with_quote[1]
                            file_content += "$product->link_rewrite = [$default_lang => %s];\n" % title_with_quote[1]

                            defaultImageUrl_with_quote = re.findall(r"\'[^']*\'", full_defaultImageUrl[index])[1]
                            defaultImageUrl = re.findall(r"[^']*", defaultImageUrl_with_quote)[1]
                            file_name =  defaultImageUrl.split('/')[len(defaultImageUrl.split('/'))-1]

                            urlretrieve(defaultImageUrl, "images/%s.png" % title)

                            price_with_quote = re.findall(r":[^']*", full_price[index])
                            price = re.findall(r"[^:,]*", price_with_quote[0])
                            file_content += "$product->price = %2.2f;\n" % float(price[1])

                            file_content += "$product->quantity = 10;\n"
                            file_content += "$product->id_category =[%s,%s];\n" % (str(type), str(sub_type))
                            file_content += "$product->id_category_default = %s;\n" % str(type)

                            file_content += """if ($product->add())
                            {
                                $product->updateCategories($product->id_category);
                                StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
                            }
                            """
                            file_content += """ $image->id_product = intval($product->id);

                                                $image->position = Image::getHighestPosition($product->id) + 1;
                                                if(Image::getImagesTotal($product->id)>0)
                                                 $image->cover = false;
                                                else
                                                  $image->cover = true;
                                                $languages = Language::getLanguages();
                                                foreach ($languages as $language)
                                                 $image->legend[$language['id_lang']] = 'Click to view';
                                                $id_image = $image->id;
                                                $image->add();
                                                $tmpName = tempnam(_PS_IMG_DIR_, 'PS');
                                                move_uploaded_file($_FILES['design']['tmp_name'], $tmpName);"""

                            index+=1
            except Exception as e:
                break

            page_number += 1
            strona_z_produktami = strona_z_produktami[:-1] + (str(page_number))
        sub_type += 1
    type += 1
with open(php_file, 'w') as fi:
    fi.write(file_content)






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

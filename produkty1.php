<?php
include(dirname(__FILE__).'/config/config.inc.php');
include(dirname(__FILE__).'/init.php');
$default_lang = Configuration::get('PS_LANG_DEFAULT');
$product = new Product(0);
$image = new Image();$product->name = [$default_lang => 'Banany'];
$product->link_rewrite = [$default_lang => 'Banany'];
$product->price = 0.75;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/1';
$image_path = '\images\Banany.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(0);
$image = new Image();$product->name = [$default_lang => 'Banany'];
$product->link_rewrite = [$default_lang => 'Banany'];
$product->price = 0.75;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/1';
$image_path = '\images\Banany.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(1);
$image = new Image();$product->name = [$default_lang => 'Cytryna'];
$product->link_rewrite = [$default_lang => 'Cytryna'];
$product->price = 0.78;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/2';
$image_path = '\images\Cytryna.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(0);
$image = new Image();$product->name = [$default_lang => 'Banany'];
$product->link_rewrite = [$default_lang => 'Banany'];
$product->price = 0.75;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/1';
$image_path = '\images\Banany.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(1);
$image = new Image();$product->name = [$default_lang => 'Cytryna'];
$product->link_rewrite = [$default_lang => 'Cytryna'];
$product->price = 0.78;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/2';
$image_path = '\images\Cytryna.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(2);
$image = new Image();$product->name = [$default_lang => 'Pomarañcze'];
$product->link_rewrite = [$default_lang => 'Pomarañcze'];
$product->price = 0.60;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/3';
$image_path = '\images\Pomarañcze.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(0);
$image = new Image();$product->name = [$default_lang => 'Banany'];
$product->link_rewrite = [$default_lang => 'Banany'];
$product->price = 0.75;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/1';
$image_path = '\images\Banany.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(1);
$image = new Image();$product->name = [$default_lang => 'Cytryna'];
$product->link_rewrite = [$default_lang => 'Cytryna'];
$product->price = 0.78;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/2';
$image_path = '\images\Cytryna.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(2);
$image = new Image();$product->name = [$default_lang => 'Pomarañcze'];
$product->link_rewrite = [$default_lang => 'Pomarañcze'];
$product->price = 0.60;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/3';
$image_path = '\images\Pomarañcze.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(3);
$image = new Image();$product->name = [$default_lang => 'Tesco Jab³ka polskie Jonagold s³odkie twarde'];
$product->link_rewrite = [$default_lang => 'Tesco Jab³ka polskie Jonagold s³odkie twarde'];
$product->price = 0.40;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/4';
$image_path = '\images\Tesco Jab³ka polskie Jonagold s³odkie twarde.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(4);
$image = new Image();$product->name = [$default_lang => 'Tesco Awokado'];
$product->link_rewrite = [$default_lang => 'Tesco Awokado'];
$product->price = 4.69;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/5';
$image_path = '\images\Tesco Awokado.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(5);
$image = new Image();$product->name = [$default_lang => 'Mandarynki'];
$product->link_rewrite = [$default_lang => 'Mandarynki'];
$product->price = 0.18;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/6';
$image_path = '\images\Mandarynki.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(5);
$image = new Image();$product->name = [$default_lang => 'Mandarynki'];
$product->link_rewrite = [$default_lang => 'Mandarynki'];
$product->price = 0.18;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/6';
$image_path = '\images\Mandarynki.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(6);
$image = new Image();$product->name = [$default_lang => 'Tesco Jab³ka polskie Szampion s³odkie lekko twarde'];
$product->link_rewrite = [$default_lang => 'Tesco Jab³ka polskie Szampion s³odkie lekko twarde'];
$product->price = 0.35;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/7';
$image_path = '\images\Tesco Jab³ka polskie Szampion s³odkie lekko twarde.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(7);
$image = new Image();$product->name = [$default_lang => 'Tesco Gruszka zielona Konferencja'];
$product->link_rewrite = [$default_lang => 'Tesco Gruszka zielona Konferencja'];
$product->price = 1.29;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/8';
$image_path = '\images\Tesco Gruszka zielona Konferencja.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(8);
$image = new Image();$product->name = [$default_lang => 'Tesco Mango'];
$product->link_rewrite = [$default_lang => 'Tesco Mango'];
$product->price = 4.99;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/9';
$image_path = '\images\Tesco Mango.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(9);
$image = new Image();$product->name = [$default_lang => 'Kiwi'];
$product->link_rewrite = [$default_lang => 'Kiwi'];
$product->price = 0.95;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/10';
$image_path = '\images\Kiwi.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(9);
$image = new Image();$product->name = [$default_lang => 'Kiwi'];
$product->link_rewrite = [$default_lang => 'Kiwi'];
$product->price = 0.95;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/10';
$image_path = '\images\Kiwi.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(10);
$image = new Image();$product->name = [$default_lang => 'Grejpfrut czerwony'];
$product->link_rewrite = [$default_lang => 'Grejpfrut czerwony'];
$product->price = 3.15;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/11';
$image_path = '\images\Grejpfrut czerwony.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(11);
$image = new Image();$product->name = [$default_lang => 'Sun Grown Kiwi koszyk 1 kg'];
$product->link_rewrite = [$default_lang => 'Sun Grown Kiwi koszyk 1 kg'];
$product->price = 5.49;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/12';
$image_path = '\images\Sun Grown Kiwi koszyk 1 kg.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);$product = new Product(12);
$image = new Image();$product->name = [$default_lang => 'Jab³ka Gala'];
$product->link_rewrite = [$default_lang => 'Jab³ka Gala'];
$product->price = 0.40;
$product->quantity = 10;
$product->id_category =[1,1];
$product->id_category_default = 1;
if ($product->add())
    {
        $product->updateCategories($product->id_category);
        StockAvailable::setQuantity((int)$product->id,0,$product->quantity,Context::getContext()->shop->id);
    }
$url = 'http://localhost:8080/api/images/products/13';
$image_path = '\images\Jab³ka Gala.png';
$key = 'My web service key';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key.':');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
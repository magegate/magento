<?php

use \Magegate\Entity;
use \Magegate\EntityCatalogProduct;

class EntityCatalogProductTest extends EntityTest {

    public function testModel()
    {
        parent::testModel();
    }

    /**
     * @depends testModel
     */
    public function testConfig()
    {
        parent::testConfig();

        $host = $this->host;
        $this->assertNotEmpty($id=\Config::get($k="magento::config.entity.catalog/category.$host.id"),
            "Config $k not found");

        $this->assertNotEmpty($entity = Entity::helperLink2Mage($host,'catalog/category',0,$id),
            "Root Entity for $id not found");
    }

    /**
     * @depends testConfig
     */
    public function testHost2MageList()
    {
        $host = $this->host;
        $list = $this->providerCatalogProduct();

        foreach($list as $i=>$data)
        {
            $list[$i] = $data = EntityCatalogProduct::helperHost2Mage($host,$data);

            $this->assertTrue(isset($data[$key='product_id']),"$key in item $i:?? not found");

            $productId = $data['product_id'];
            $this->assertTrue(isset($data[$key='sku']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='type']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='categories']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='name']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='description']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='short_description']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='weight']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='status']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='visibility']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='price']),"$key in item $i:$productId not found");
            $this->assertTrue(isset($data[$key='tax_class_id']),"$key in item $i:$productId not found");
        }

        return $list;
    }

    public function providerCatalogProduct()
    {
        $files = array_slice(scandir($dir=__DIR__."/ImportCatalogProduct/files"),2,20);
        $items = array();

        foreach($files as $file)
        {
            $load = include("$dir/$file");
            if(!is_array($load)) continue;

            $item = array(
                'ASIN' => $asin = $load['ASIN'],
                'product_id' => $load['ASIN'],
                'description' => '',
                'categories' => $load['BrowseNodeIds'],
                'images' => array(),
            );

            unset($load['BrowseNodes']);
            unset($load['BrowseNodeIds']);
            unset($load['CustomerReviews']);

            $adot = array_dot($load);

            if(array_key_exists($k='SmallImage.URL',$adot))
                $item['images'][0]['thumbnail'] = $adot[$k];
            if(array_key_exists($k='MediumImage.URL',$adot))
                $item['images'][0]['small_image'] = $adot[$k];
            if(array_key_exists($k='LargeImage.URL',$adot))
                $item['images'][0]['image'] = $adot[$k];

            for($j=1;$j<10;++$j)
            {
                if(array_key_exists($k="ImageSets.ImageSet.$j.LargeImage.URL",$adot))
                    $item['images'][$j]['image'] = $adot[$k];
            }

            if(array_key_exists($k='ItemAttributes.Title',$adot))
                $item['title'] = $item['short_description'] = $adot[$k];

            if(array_key_exists($k='EditorialReviews.EditorialReview.Content',$adot))
                $item['description'] = $item['short_description'] = $adot[$k];
            if(array_key_exists($k='EditorialReviews.EditorialReview.0.Content',$adot))
                $item['description'] = $item['short_description'] = $adot[$k];
            if(array_key_exists($k='EditorialReviews.EditorialReview.1.Content',$adot))
                $item['description'] = $item['short_description'] = $adot[$k];

            if(array_key_exists($k='OfferSummary.LowestUsedPrice.Amount',$adot))
                $item['amount'] = $adot[$k]/100;
            if(array_key_exists($k='OfferSummary.LowestNewPrice.Amount',$adot))
                $item['amount'] = $adot[$k]/100;
            if(array_key_exists($k='ItemAttributes.ListPrice.Amount',$adot))
                $item['amount'] = $adot[$k]/100;
            if(array_key_exists($k='ItemAttributes.PackageDimensions.Weight',$adot))
                $item['weight'] = $adot[$k]/100;
            if(array_key_exists($k='ItemAttributes.Feature.0',$adot))
            {
                $item['description'] = "<ul>";
                for($i=0;$i<10;++$i)
                    if(array_key_exists($k="ItemAttributes.Feature.$i",$adot))
                        $item['description'].="\n<li>".$adot[$k]."</li>";
                $item['description'].= "\n</ul>";
            }
            if(empty($item['description']))
                $item['description'] = $item['short_description'];

            $items[] = $item;
        }

        return $items;
    }
}
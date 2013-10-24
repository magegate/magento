<?php
namespace Magegate\Magento\Stubs;

class CatalogProductStub {

    static protected $increment = 100;
    static protected $product = array();
    static protected $product_sku = array();

    public function __construct()
    {
        self::$increment = \Cache::driver('file')->get(__CLASS__.'$increment',100);
        self::$product = \Cache::driver('file')->get(__CLASS__.'$product',array());
        self::$product_sku = \Cache::driver('file')->get(__CLASS__.'$product_sku',array());
    }

    public function __destruct()
    {
        \Cache::driver('file')->put(__CLASS__.'$increment',self::$increment,8*60);
        \Cache::driver('file')->put(__CLASS__.'$product',self::$product,8*60);
        \Cache::driver('file')->put(__CLASS__.'$product_sku',self::$product_sku,8*60);

    }

    public function create($type, $set, $sku, $productData)
    {
        static $api_category;
        static $info_category = array();

        $productData->type = $type;
        $productData->set = $set;
        $productData->sku = $sku;

        if(!isset($api_category))
            $api_category =
                \Config::get("magento::config.imports.catalog_category_api",
                    \Config::get("magento::config.imports_api",'\Magegate\MagentoApiMock'));

        foreach($productData->categories as $category)
        {
            if(!array_key_exists($category,$info_category) || empty($info_category[$category]))
            {
                $info_category[$category]=$api_category::info('catalog/category',$category);
                if(empty($info_category[$category]))
                    throw(new \Exception("$category category not found",404));
            }
        }

        if(array_key_exists($sku=$productData->sku,self::$product))
            throw(new \Exception("$sku already exists",403));

        $productData->product_id = ++self::$increment;
        self::$product[$productData->product_id] = $productData;
        self::$product_sku[$productData->sku] = $productData->product_id;
        return $productData->product_id;
    }

    public function delete($id)
    {
        if(!array_key_exists($id,self::$product))
            throw(new \Exception("$id not found",403));

        unset(self::$product[$id]);

        return true;
    }

    public function info($id)
    {
        if(!array_key_exists($id,self::$product))
            throw(new \Exception("$id product not found",404));

        return (array)self::$product[$id];
    }
}
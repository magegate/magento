<?php
namespace Magegate\Magento\Stubs;

class CatalogCategoryStub {

    static protected $increment = 100;
    static protected $categories = array();

    public function __construct()
    {
        self::$increment = \Cache::driver('file')->get(__CLASS__.'$increment',100);
        self::$categories = \Cache::driver('file')->get(__CLASS__.'$categories',array());
    }

    public function __destruct()
    {
        \Cache::driver('file')->put(__CLASS__.'$increment',self::$increment,8*60);
        \Cache::driver('file')->put(__CLASS__.'$categories',self::$categories,8*60);

    }

    public function create($parentId, $categoryData)
    {
        $newId=++self::$increment;
        if($parentId>100)
        {
            if(!array_key_exists($parentId,self::$categories))
                throw(new \Exception("$parentId not found",404));

        }

        if(!array_key_exists($parentId,self::$categories)) {
            self::$categories[$parentId] = (object)array(
                'category_id' => $parentId,
                'parent_id' => null,
                'children' => array(),
            );
        }

        self::$categories[$parentId]->children[] = $newId;

        $categoryData->parent_id = $parentId;
        $categoryData->children = array();
        self::$categories[$newId] = $categoryData;
        return $newId;
    }

    public function delete($id)
    {
        if(!array_key_exists($id,self::$categories))
            throw(new \Exception("$id not found",404));

        if(!empty(self::$categories[$id]->children))
            throw(new \Exception("$id has children",403));

        $parent_id = self::$categories[$id]->parent_id;
        $parent_children = self::$categories[$parent_id]->children;
        $parent_children = array_flip($parent_children);
        unset($parent_children[$id]);
        $parent_children = array_keys($parent_children);
        self::$categories[$parent_id]->children = $parent_children;

        unset(self::$categories[$id]);
        return true;
    }

    public function info($id)
    {
        if(!array_key_exists($id,self::$categories))
            throw(new \Exception("$id not found",404));

        $info = (array)self::$categories[$id];
        $info['children'] = implode(',',$info['children']);
        return $info;
    }
}
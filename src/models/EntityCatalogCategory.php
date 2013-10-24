<?php
namespace Magegate;

class EntityCatalogCategory extends Entity {

    static public function helperPresetDefaults($host,$data,$model='catalog/category')
    {
        $data = parent::helperPresetDefaults($host,$data,$model='catalog/category');

        static $getAttributesUsedForSortBy;

        while(empty($data['available_sort_by']) && !$data['available_sort_by']=$getAttributesUsedForSortBy)
        {
            $getAttributesUsedForSortBy = array('position');
            foreach(\App::make('Mage')->getSingleton('catalog/config')->getAttributesUsedForSortBy() as $attr)
            {
                array_push($getAttributesUsedForSortBy,$attr['attribute_code']);
            }
        }

        if(!is_array($data['available_sort_by']))
            $data['available_sort_by'] = array($data['available_sort_by']);

        return $data;
    }

    static public function helperHost2Mage($host,$data,$model='catalog/category')
    {
        $data = static::helperTransferToMage($host,$data,'catalog/category');
        $data = static::helperPresetDefaults($host,$data,'catalog/category');

        return $data;
    }

    static public function helperSortList($list)
    {
        $nodes = array();
        $items = array();
        foreach($list as $data)
        {
            $nodes[$categoryId=''.$data['category_id'].''] = $data;
        }

        $sortitems = function(){};
        $sortitems = function($item) use (&$sortitems, &$nodes, &$items)
        {
            if($item==0 || array_key_exists($item,$items)) return;
            foreach($nodes[$item]['parent_id'] as $parentId)
            {
                $sortitems($parentId);
            }
            $items[$item] = &$nodes[$item];
        };
        foreach(array_keys($nodes) as $item)
        {
            $sortitems($item);
        }

        return array_values($items);
    }

    static public function helperProvidePathHostIdList($categoryList)
    {
        $categoryNode = array();
        foreach($categoryList as $path=>$node)
        {
            $categoryNode[''.$node.''] = array(
                'node' => $node,
                'name' => basename($path),
                'parents' => array(),
            );
        }
        foreach($categoryList as $path=>$node)
        {
            $base = dirname($path);
            if(array_key_exists($base,$categoryList))
            {
                $categoryNode[''.$node.'']['parents'][$base] = $categoryList[$base];
            }
        }
        foreach($categoryNode as $node=>$data)
        {
            $categoryNode[$node]['parents'] = array_values(array_unique($categoryNode[$node]['parents']))?:array(0);
        }
        return array_values($categoryNode);
    }

    static public function helperDeleteChildren($host,$id)
    {
        $entities = parent
            ::where('host','=',$host)
            ->where('host_id',$id)
            ->where('mage_model','=','catalog/category');

        foreach($entities->get() as $entity)
        {
            $category = (object)\Magegate::magento('catalog/category')->info($entity->mage_id);
            $category->children = explode(',',$category->children);

            if(empty($category->children))
            {
                continue;
            }

            $children = parent
                ::where('host','=',$host)
                ->where('mage_model','=','catalog/category')
                ->whereIn('mage_id',$category->children)
                ->where('is_owner','=',true);

            foreach($children->get() as $child)
            {
                if(!self::helperDeleteChildren($host,$child->host_id))
                {
                    return false;
                }

                if(!\Magegate::magento('catalog/category')->delete($child->mage_id))
                {
                    return false;
                }

                if(!$child->delete())
                {
                    return false;
                }
            }
        }
        return true;
    }

    static public function create(array $attributes)
    {
        if(!array_key_exists($k='host',$attributes))
            throw(new \Exception("array key '$k' missing",404));
        $host = $attributes[$k];

        if(!array_key_exists($k='data',$attributes))
            throw(new \Exception("array key'$k' missing",404));
        $data = $attributes[$k];

        if(empty($data[$k='category_id']))
            throw(new \Exception("data '$k' missing",404));
        $host_id = $data[$k];
        unset($data[$k]);

        if(empty($data[$k='parent_id']))
            throw(new \Exception("data '$k' missing",404));
        if(!is_array($data[$k]))
            $data[$k] = array($data[$k]);
        $parent_id = $data[$k];
        unset($data[$k]);

        $parentEntities = parent
            ::where('host','=',$host)
            ->whereIn('host_id',$parent_id)
            ->where('mage_model','=','catalog/category');

        $hostEntity = array();
        foreach($parentEntities->get() as $parentEntity)
        {
            $hostEntity[] =
                parent::create(array(
                    'host' => $host,
                    'host_id' => $host_id,
                    'mage_id' => \Magegate::magento('catalog/category')
                        ->create($parentEntity->mage_id,(object)$data),
                    'mage_model' => 'catalog/category',
                    'is_owner' => true,
                ))->toArray();
        }

        return parent
            ::where('host','=',$host)
            ->where('host_id','=',$host_id)
            ->where('mage_model','=','catalog/category')
            ->where('is_owner','=',true);
    }

}
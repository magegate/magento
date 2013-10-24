<?php
namespace Magegate;

class EntityCatalogProduct extends Entity {

    static public function helperPresetDefaults($host,$data,$model='catalog/product')
    {
        return parent::helperPresetDefaults($host,$data,$model='catalog/product');
    }

    static public function helperHost2Mage($host,$data,$model='catalog/product')
    {
        $data = static::helperTransferToMage($host,$data,'catalog/product');
        $data = static::helperPresetDefaults($host,$data,'catalog/product');

        return $data;
    }

    static public function helperDeleteAll($host)
    {
        $entities = parent
            ::where('host','=',$host)
            ->where('mage_model','=','catalog/product')
            ->where('is_owner','=',true);

        foreach($entities->get() as $entity)
        {
            if(!$entity->delete())
            {
                return false;
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

        if(empty($data[$k='product_id']))
            throw(new \Exception("product_id missing",404));
        $product_id = $data[$k];
        unset($data[$k]);

        if(empty($data[$k='categories']))
            $data[$k] = array();
        if(!is_array($data[$k]))
            $data[$k] = array($data[$k]);

        $categories = $data['categories'];
        $data['categories'] = array();

        $categoriesEntities = EntityCatalogCategory
            ::where('host','=',$host)
            ->where('mage_model','=','catalog/category')
            ->whereIn('host_id',$categories);

        foreach($categoriesEntities->get() as $categoryEntity)
        {
            $data['categories'][] = $categoryEntity->mage_id;
        }

        static $image_types = array(
            'thumbnail','small_image','image',
        );

        static $image_mimes = array(
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
        );

        if(array_key_exists('images',$data))
        {
            foreach($data['images'] as $i=>$image)
            {
                foreach($image as $type=>$url)
                {
                    if(!in_array($type,$image_types))
                        throw(new \Exception("Invalid image type '$type' detected"));

                    $file = basename($url);
                    $mime = array_slice(explode('.',$file),-1,1)[0];

                    if(!array_key_exists($mime,$image_mimes))
                        throw(new \Exception("Unknown mime type for '.$mime' image"));

                    if(!$content=\Cache::driver('file')->get($k=__CLASS__.'$content#'.$type.$file,false))
                    {
                        \Cache::driver('file')
                            ->put($k,$content=base64_encode(file_get_contents($url)),60*24*7);
                    }

                    $types = ($i==0)?array($type=>$type):array();
                    if($i==0 && !array_key_exists($k='thumbnail',$image)) $types[$k] = $k;
                    if($i==0 && !array_key_exists($k='small_image',$image)) $types[$k] = $k;

                    $data['images'][$i][$type] = array(
                        'file' => array(
                            'name' => $file,
                            'mime' => $image_mimes[$mime],
                            'content' => $content,
                        ),
                        'label'    => $data['name'],
                        'position' => $i+1,
                        'types'    => array_values($types),
                        'exclude'  => ($type!='image')?1:0,
                    );
                }
            }
        }

        $entity =
            parent::create(array(
                'host' => $host,
                'host_id' => $product_id,
                'mage_id' => \Magegate::magento('catalog/product')
                    ->create($type=$data['type'], $set=$data['set'], $sku=$data['sku'], (object)$data),
                'mage_model' => 'catalog/product',
                'is_owner' => true,
            ));

        if(!empty($entity))
        {
            foreach($data['images'] as $i=>$image)
            {
                foreach($image as $type=>$media)
                {
                    $imageFile = \Magegate::magento('catalog/product_attribute_media')
                        ->create($entity->mage_id,$media);

                    if(!$imageFile) continue;
                }
            }
        }

        return $entity;
    }

    public function delete()
    {
        if($this->is_owner)
        {
            if(!\Magegate::magento('catalog/product')->delete($this->mage_id))
            {
                return false;
            }
        }

        return parent::delete();
    }
}
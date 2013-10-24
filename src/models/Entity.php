<?php
namespace Magegate;

class Entity extends \Eloquent {

    public $table = 'entity';
    public $fillable = array('host','host_id','mage_model','mage_id','is_owner');

    static public function helperTransferToMage($host,$data,$model)
    {
        $arrange=\Config::get("magento::config.entity.$model.$host.arrange",array());

        foreach($arrange as $mage_key=>$host_key)
        {
            if(array_key_exists($host_key,$data))
            {
                $data[$mage_key] =
                    \Config::has($key="magento::config.entity.$model.$host.host2mage.$mage_key")
                    ? \Config::get($key,$data[$host_key])
                    : $data[$host_key];
            }
        }

        foreach($arrange as $host_key)
        {
            if(array_key_exists($host_key,$data))
            {
                unset($data[$host_key]);
            }
        }
        return $data;
    }

    static public function helperPresetDefaults($host,$data,$model)
    {
        foreach(\Config::get("magento::config.entity.$model.$host.default") as $key=>$value)
        {
            if(!array_key_exists($key,$data) || !isset($data[$key]))
            {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    static public function helperHost2Mage($host,$data,$model)
    {
        $data = static::HelperTransferToMage($host,$data,$model);
        $data = static::HelperPresetDefaults($host,$data,$model);

        return $data;
    }

    static public function helperLink2Mage($host,$model,$host_id,$mage_id)
    {
        return
            \Magegate\Entity::where('host','=',$host)
                ->where('host_id','=',$host_id)
                ->where('mage_id','=',$mage_id)
                ->where('mage_model','=',$model)
                ->first()
                ?:\Magegate\Entity::create(array(
                'host' => $host,
                'host_id' => $host_id,
                'mage_id' => $mage_id,
                'mage_model' => $model,
                'is_owner' => false,
            ));
    }

}
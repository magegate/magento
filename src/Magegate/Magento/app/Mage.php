<?php namespace Magegate\Magento\App;

/**
 * Class Mage
 * @package Magegate\Magento\App
 *
 * Laravel to Magento Mage class wrapper over \App::make('Mage')
 * registered by MagentoServiceProvider.
 */
class Mage {

    static protected $mage_api_instance = array();
    static protected $mage_include_path;

    public function magento($model)
    {
        if(!isset(self::$mage_api_instance[$model])) {

            if($stub=\Config::get("magento::config.magento.$model"))
            {
                return self::$mage_api_instance[$model] = new $stub();
            }

            if(!defined('MAGENTO_ROOT'))
                $this->magentoBootCodeRuntime();

            if(empty(self::$mage_include_path))
            {
                self::$mage_include_path = array(
                    realpath(MAGENTO_ROOT.'/lib'),
                    realpath(MAGENTO_ROOT.'/app'),
                    realpath(MAGENTO_ROOT.'/app/code/local'),
                    realpath(MAGENTO_ROOT.'/app/code/community'),
                    realpath(MAGENTO_ROOT.'/app/code/core'),
                );
            }

            list($p,$m) = preg_split('/\//',$model);
            $name = explode('_','Mage_'.$p.'_Model_'.$m);
            foreach($name as $i=>$value) $name[$i] = ucfirst(strtolower($value));
            $path = implode('/',$name);
            $name = implode('_',$name);
            $name1 = $name.'_Api';
            $path1 = $path.'/Api';
            $name2 = $name.'_Api_V2';
            $path2 = $path.'/Api/V2';

            foreach(self::$mage_include_path as $base)
            {
                if(file_exists($file="$base/$path1.php"))
                {
                    include_once($file);
                    self::$mage_api_instance[$model] = new $name2();
                }
                if(file_exists($file="$base/$path2.php"))
                {
                    include_once($file);
                    self::$mage_api_instance[$model] = new $name2();
                    break;
                }
            }
        }
        return self::$mage_api_instance[$model];
    }

    protected function magentoBootCodeRuntime()
    {
        /**
         * Magento is not loaded. Startup Mage boot sequence
         */
        if (version_compare(phpversion(), '5.2.0', '<')) {
            throw( new \ErrorException(
                'It looks like you have an invalid PHP version. Magento supports PHP 5.2.0 or newer',500));
        }

        /**
         * Register magento class directories to autoload magento classes
         */
        define('MAGENTO_ROOT',$basedir = realpath($b=\Config::get('magento::config.basedir')));
        if(empty($basedir))
            throw(new \Exception("Invalid mage basedir: $b"));

        set_include_path(get_include_path() . PATH_SEPARATOR . realpath($basedir.'/lib'));
        set_include_path(get_include_path() . PATH_SEPARATOR . realpath($basedir.'/app'));
        set_include_path(get_include_path() . PATH_SEPARATOR . realpath($basedir.'/app/code/local'));
        set_include_path(get_include_path() . PATH_SEPARATOR . realpath($basedir.'/app/code/community'));
        set_include_path(get_include_path() . PATH_SEPARATOR . realpath($basedir.'/app/code/core'));

        /**
         * Preload Magento compiled sources if exists
         */
        $compilerConfig = MAGENTO_ROOT . '/includes/config.php';
        if (file_exists($compilerConfig)) {
            include $compilerConfig;
        }

        /**
         * Lookup mage core App-Class and maintenance file
         */
        $mageFilename = MAGENTO_ROOT . '/app/Mage.php';
        $maintenanceFile = MAGENTO_ROOT.'/maintenance.flag';

        if (!file_exists($mageFilename)) {
            throw( new \ErrorException('Mage file not found',500));
        }

        if (file_exists($maintenanceFile)) {
            throw( new \ErrorException("Magento maintenance mode",503));
        }


        /**
         * Load Magento Mage App and ensure the Mage class exists
         */
        require_once $mageFilename;
        if( ! class_exists('\Mage') ) {
            throw new \ErrorException("Magento runtime class not loaded.",500);
        }

        /**
         * Examine that Magento is already installed and set developer mode if necessary
         */
        if (!\Mage::isInstalled()) {
            throw( new \ErrorException(
                'Application is not installed yet, please complete install wizard first.',500));
        }

        if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
            \Mage::setIsDeveloperMode(true);
        }

        /*
         * Now startup Mage::run() and set admin store rights
         * emulate index.php entry point for correct URLs generation in API
         */
        \Mage::register('custom_entry_point', true);
        \Mage::$headersSentThrowsException = false;
        \Mage::init('admin');

    }

    /**
     * Calls the Magento Mage instance
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \ErrorException
     */
    public function __call($name, $arguments)
    {
        if(!defined('MAGENTO_ROOT'))
            $this->magentoBootCodeRuntime();

        try
        {
            return call_user_func_array("Mage::{$name}", $arguments);
        }
        catch(\Exception $e)
        {
            throw(new \ErrorException("Mage call {$name} failed.",500,$e));
        }
    }
}
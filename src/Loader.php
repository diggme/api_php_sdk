<?php

namespace diggme;

/**
 * Class Loader
 * @package diggme
 * @created 2017-04-18 develop@diggme.cn
 * @modified 2017-04-18 develop@diggme.cn
 * @copyright © 2017 www.diggme.cn
 * @contact DP <develop@diggme.cn>
 */
class Loader
{

    /**
     * Psr class container
     * @var array
     */
    protected static $psr4 = [
        'diggme' => __DIR__,
        'diggme\\opensdk' => __DIR__ . '/opensdk'
    ];

    /**
     * Autoload class mapping
     * @var array
     */
    protected static $classes = [];

    /**
     * Bind diggme autoload
     * @retun void
     */
    public static function register()
    {
        spl_autoload_register(['\\diggme\\Loader', 'autoLoad']);
    }

    /**
     * Unbind diggme autoload
     * @retun void
     */
    public static function unRegister()
    {
        spl_autoload_unregister(['\\diggme\\Loader', 'autoLoad']);
    }

    /**
     * Autoload handler
     * @param string $class
     * @return void
     */
    protected static function autoLoad($class)
    {
        if ($cls = self::getClass($class)) {
            require $cls;
        } else if ($cls = self::loadPsr4($class)) {
            require $cls;
        }
    }

    /**
     * find a class real path in registered mapping
     * @param $class
     * @return mixed|null
     */
    public static function getClass($class)
    {
        return isset(self::$classes[$class]) ? self::$classes[$class] : null;
    }

    /**
     * load psr4 style class
     * @param $class
     * @param string $suffix
     * @return mixed|null
     */
    public static function loadPsr4($class, $suffix = '.php')
    {
        $cls = trim(substr($class, strrpos($class, '\\')), '\\');
        $ns = trim(str_replace($cls, '', $class), '\\');
        if (array_key_exists($ns, self::$psr4)) {
            $file = self::$psr4[$ns] . '/' . $cls . $suffix;
            if (file_exists($file)) {
                self::$classes[$class] = $file;
            }
        }
        return self::getClass($class);
    }

}
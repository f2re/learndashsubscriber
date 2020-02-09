<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0c4720c38ecfb3e98ebef3791011b743
{
    public static $prefixLengthsPsr4 = array (
        'H' => 
        array (
            'Helpers\\' => 8,
        ),
        'C' => 
        array (
            'Controllers\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/helpers',
        ),
        'Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/controllers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0c4720c38ecfb3e98ebef3791011b743::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0c4720c38ecfb3e98ebef3791011b743::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}

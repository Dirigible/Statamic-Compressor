<?php

namespace Statamic\Addons\Compressor;

use Statamic\Extend\Listener;

class CompressorListener extends Listener
{
    public $events = [
        'glide.generated' => 'optimize'
    ];

    public function optimize($path, $params)
    {
        // $path = /path/to/local/cache/glide/foo.jpg/shahash
        // $params = ['w' => 100,   'h' => 200, etc]
        $jpegoptim = "jpegoptim -s ";
        $jpegtran = "jpegtran -optimize -progressive ";
        $optipng = "optipng ";
        $pngcrush = "pngcrush -ow ";
        $pngcrush = "gifsicle -O5 ";
        $filetype = "";

        if ( strpos( $path, ".jpg" ) || strpos( $path, ".JPG" ) || strpos( $path, ".jpeg" ) || strpos( $path, ".JPEG" ) !== false ) {
            $filetype = "jpg";
        }
        elseif ( strpos( $path, ".png" ) || strpos( $path, ".PNG" ) !== false ) {
            $filetype = "png";
        }
        elseif ( strpos( $path, ".gif" ) || strpos( $path, ".GIF" ) !== false ) {
            $filetype = "gif";
        }

        if ( $filetype === "jpg" ) {
            $temp = $path . ".jpg";
            exec("cp " . $path . " " . $temp);
            exec($jpegoptim . "-t " . $temp);
            exec($jpegtran . $temp);
            exec("mv " . $temp . " " . $path);
        }
        if ( $filetype === "png" ) {
            $temp = $path . ".png";
            exec($pngcrush . $path . " " . $temp);
            exec($optipng . $temp);
            exec("mv " . $temp . " " . $path);
        }
        if ( $filetype === "gif" ) {
            $temp = $path . ".gif";
            exec($gifsicle . $path . " > " . $temp);
            exec("mv " . $temp . " " . $path);
        }
    }
}

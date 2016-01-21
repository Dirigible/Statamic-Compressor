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

        // The name of the bin with default flags to make them run
        $jpegoptim = "jpegoptim --strip-all ";
        $jpegtran = "jpegtran -copy none -optimize -progressive ";
        $optipng = "optipng ";
        $pngcrush = "pngcrush -ow ";
        $gifsicle = "gifsicle -O5 ";
        $filetype = "";

        // Test the path for the file extensions and assign a filetype
        if ( strpos( $path, ".jpg" ) ||
             strpos( $path, ".JPG" ) ||
             strpos( $path, ".jpeg" ) ||
             strpos( $path, ".JPEG" ) !== false ) {
            $filetype = "jpg";
        }
        elseif ( strpos( $path, ".png" ) ||
                 strpos( $path, ".PNG" ) !== false ) {
            $filetype = "png";
        }
        elseif ( strpos( $path, ".gif" ) ||
                 strpos( $path, ".GIF" ) !== false ) {
            $filetype = "gif";
        }

        if ( $filetype === "jpg" && $this->getConfig('jpegoptim') ) {
            $temp = $path . ".jpg";
            exec("cp " . $path . " " . $temp);
            exec($jpegoptim . $temp);
            exec("mv " . $temp . " " . $path);
        }

        if ( $filetype === "jpg" && $this->getConfig('jpegtran') ) {
            $temp = $path . ".jpg";
            exec($jpegtran . $path . " > " . $temp);
            exec("mv " . $temp . " " . $path);
        }

        if ( $filetype === "png" && $this->getConfig('pngcrush') ) {
            $temp = $path . ".png";
            exec($pngcrush . $path . " " . $temp);
            exec("mv " . $temp . " " . $path);
        }

        if ( $filetype === "png" && $this->getConfig('optipng') ) {
            $temp = $path . ".png";
            exec("cp " . $path . " " . $temp);
            exec($optipng . $temp);
            exec("mv " . $temp . " " . $path);
        }

        if ( $filetype === "gif" && $this->getConfig('gifsicle') ) {
            $temp = $path . ".gif";
            exec($gifsicle . $path . " > " . $temp);
            exec("mv " . $temp . " " . $path);
        }
    }
}

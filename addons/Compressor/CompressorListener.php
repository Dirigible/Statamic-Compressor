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
        $jpegoptim = "jpegoptim -s ";
        $jpegtran = "jpegtran -optimize -progressive ";
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

        if ( $filetype === "jpg" ) {
            // Copy the existing glide-generated image to a tempory location and
            // add a file extension. Some of the libraries have trouble if the
            // file extension is not in place.
            $temp = $path . ".jpg";
            exec("cp " . $path . " " . $temp);

            // Run jpegoptim and jpegtran
            exec($jpegoptim . "-t " . $temp);
            exec($jpegtran . $temp);

            // Move the optimized file back to the original file location
            // This gets rid of the extra file and should be a safe operation
            // in case of the temp file never being generated.
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

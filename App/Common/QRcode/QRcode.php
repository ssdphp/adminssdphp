<?php

namespace App\Common\QRcode ;
use App\Common\QRcode\Adaptor\QRencode;

class QRcode{
   
    public static function png($text){

        $enc = QRencode::factory(0,200,0);
        $enc->encodePNG($text);
    }

    public static function pngdata($text,$size=20){
        ob_start();
        $enc = QRencode::factory(0,$size,1);
        $enc->encodePNG($text);
        $err = ob_get_contents();
        ob_end_clean();
        return $err;
    }

}

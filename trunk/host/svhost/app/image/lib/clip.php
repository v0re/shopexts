<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */
 
class image_clip{
    function image_resize(&$imgmdl,$src_file,$target_file,$new_width,$new_height){
        if(isset($src_file)&&is_file($src_file)){
            list($width, $height,$type) = getimagesize($src_file);
            $size = self::get_image_size($new_width,$new_height,$width,$height);
            $new_width = $size[0];
            $new_height = $size[1]; 
            if(function_exists('magickresizeimage')){
                $rs = NewMagickWand();
                if(MagickReadImage($rs,$src_file)){
                    MagickResizeImage($rs,$new_width,$new_height,MW_QuadraticFilter,0.3);
                    MagickSetImageFormat($rs,'image/jpeg');
                    MagickWriteImage($rs, $target_file);
                }
                return true;
            }elseif( function_exists('imagecopyresampled')){
                $quality  = 80;
                $image_p = imagecreatetruecolor($new_width, $new_height);
                imagealphablending($image_p,false);
                switch($type){
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($src_file);
                    $func = 'imagejpeg';
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($src_file);
                    $func = 'imagegif';
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($src_file);
                    imagesavealpha($image,true);
                    $func = 'imagepng';
                    $quality  = 8;
                    break;
                }
                imagesavealpha($image_p,true);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                if($func) $func($image_p, $target_file, $quality);
                imagedestroy($image_p);
                imagedestroy($image);
            }
        }
    }
    function get_image_size($new_width,$new_height,$org_width,$org_height){
        $dest_width = $new_width;
        $dest_height = $new_height;
        if($org_width>$org_height){
            if($org_width>=$new_width){
                $dest_width = $new_width;
                $dest_height = round(($org_height/$org_width)*$new_height);
            }
        }else{
            if($org_height>=$new_height){
                $dest_height = $new_height;
                $dest_width = round(($org_width/$org_height)*$new_width);
            }
        }
        return array($dest_width,$dest_height);
    }
    function image_watermark(&$imgmdl,$file,$set){
        switch($set['wm_type']){
        case 'text':
            $mark_image = $set['wm_text_image'];
            break;
        case 'image':
            $mark_image = $set['wm_image'];
            break;
        default:
            return;
        }

        $mark_image = $imgmdl->fetch($mark_image);

        list($watermark_width,$watermark_height,$type) = getimagesize($mark_image);
        list($src_width,$src_height) = getimagesize($file);
        list($dest_x, $dest_y ) = self::get_watermark_dest($src_width,$src_height,$watermark_width,$watermark_height,$set['wm_loc']);

        if(function_exists('NewMagickWand')){
            $sourceWand = NewMagickWand();
            $compositeWand = NewMagickWand();
            MagickReadImage($compositeWand, $mark_image);
            MagickReadImage($sourceWand, $file);
            MagickSetImageIndex($compositeWand, 0);
            MagickSetImageType($compositeWand, MW_TrueColorMatteType);
            MagickEvaluateImage($compositeWand, MW_SubtractEvaluateOperator, ($set['wm_opacity']?$set['wm_opacity']:50)/100, MW_OpacityChannel) ;
            MagickCompositeImage($sourceWand, $compositeWand, MW_ScreenCompositeOp, $dest_x, $dest_y);
            MagickWriteImage($sourceWand, $file);
        }elseif(method_exists(image_clip,'imagecreatefrom')){
            $sourceimage = self::imagecreatefrom($file);
            $watermark = self::imagecreatefrom($mark_image);
            imagecolortransparent($watermark, imagecolorat($watermark,0,0));
            imagealphablending($watermark,1);
            if($type==IMAGETYPE_PNG){
                imagecopy($sourceimage, $watermark, $dest_x, $dest_y, 0,
                    0, $watermark_width, $watermark_height);                
            }else{
                imagecopymerge($sourceimage, $watermark, $dest_x, $dest_y, 0,
                    0, $watermark_width, $watermark_height, $set['wm_opacity']?$set['wm_opacity']:100);
            }
            imagejpeg($sourceimage,$file);
            imagedestroy($sourceimage);
            imagedestroy($watermark);
        }
    }

    static function imagecreatefrom($file){
        list($w,$h,$type) = getimagesize($file);

        switch($type){
        case 2:
            return imagecreatefromjpeg($file);
        case 1:
            return imagecreatefromgif($file);
        case 3:
            return imagecreatefrompng($file);
        }
    }

    static function get_watermark_dest($src_w,$src_h,$wm_w,$wm_h,$loc){
        switch($loc{0}){
        case 't':
            $dest_y = ($src_h - 5 >$wm_h)?5:0;
            break;
        case 'm':
            $dest_y = floor(($src_h - $wm_h)/2);
            break;
        default:
            $dest_y = ($src_h - 5 >$wm_h)?($src_h - $wm_h - 5):0;
        }

        switch($loc{1}){
        case 'l':
            $dest_x = ($src_w - 5 >$wm_w)?5:0;
            break;
        case 'c':
            $dest_x = floor(($src_w - $wm_w)/2);
            break;
        default:
            $dest_x = ($src_w - 5 >$wm_w)?($src_w - $wm_w - 5):0;
        }

        return array($dest_x,$dest_y);
    }
}

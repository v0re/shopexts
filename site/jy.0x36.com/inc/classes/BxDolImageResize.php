<?
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/


// Function error codes
define( 'IMAGE_ERROR_SUCCESS',					0 );
define( 'IMAGE_ERROR_SOURCE_NOT_EXISTS',		1 );
define( 'IMAGE_ERROR_WRONG_TYPE',				2 );
define( 'IMAGE_ERROR_FILE_OPEN_FAILED',			3 );
define( 'IMAGE_ERROR_IMAGEMAGICK_ERROR',		4 );
define( 'IMAGE_ERROR_GD_NOT_INSTALLED',			5 );
define( 'IMAGE_ERROR_GD_TYPE_NOT_SUPPORTED',	6 );
define( 'IMAGE_ERROR_GD_OPEN_FAILED',			7 );
define( 'IMAGE_ERROR_GD_RESIZE_ERROR',			8 );
define( 'IMAGE_ERROR_GD_MERGE_ERROR',			9 );
define( 'IMAGE_ERROR_GD_WRITE_FAILED',			10 );
define( 'IMAGE_ERROR_GD_TTF_NOT_SUPPORTED',		11 );


// Image types for GD
// NOTE: actually these constants exist in PHP >= 4.3.0, but they are included for
//       back compatibility
define( 'IMAGE_TYPE_GIF', 		1 );
define( 'IMAGE_TYPE_JPG', 		2 );
define( 'IMAGE_TYPE_PNG', 		3 );

class BxDolImageResize {

    var $w = 64, $h = 64; // size of destination image
    var $_isCrop = false;
    var $_cropX, $_cropY, $_cropW, $_cropH;
    var $_isForceJPGOutput = false; // force  jpeg output
    var $_iJpegQuality = 75; // jpeg quality
    var $_isSquareResize = false; // use smart resize, destination image will be exact Width x Height size
    var $_isUseGD; // use GD library or command line ImagMagic utilites

    function BxDolImageResize() {
        $this->_isUseGD = getParam('enable_gd') == 'on' && extension_loaded('gd') ? true : false;
	}

    function & instance() {
        if (!isset($GLOBALS['oBxDolImageResize']))
            $GLOBALS['oBxDolImageResize'] = new BxDolImageResize ();
        return $GLOBALS['oBxDolImageResize'];
    }

    function resize ($mixedImage, $sDstImage = '') {
        if (is_array($mixedImage)) {
            $aRet = array();
            foreach ($mixedImage as $s) {
                $aRet[] = $this->_resize ($s, $s);
            }
            return $aRet;
        } else {
            return $this->_resize ($mixedImage, $sDstImage);
        }
    }

    function applyWatermark ($mixedImage, $sDstImage, $wtrFilename, $wtrTransparency) {
        if (is_array($mixedImage)) {
            $aRet = array();
            foreach ($mixedImage as $s) {
                $aRet[] = $this->_applyWatermark ($s, $s, $wtrFilename, $wtrTransparency);
            }
            return $aRet;
        } else {
            return $this->_applyWatermark ($mixedImage, $sDstImage, $wtrFilename, $wtrTransparency);
        }
    }

    function setSize ($w, $h) {
        $this->w = $w;
        $this->h = $h;
    }

    function removeCropOptions () {
        $this->_isCrop = false;
    }

    function setCropOptions ($x, $y, $w, $h) {
        $this->_isCrop = true;
        $this->_cropX = $x;
        $this->_cropY = $y;
        $this->_cropW = $w;
        $this->_cropH = $h;
    }

    function setJpegOutput ($b) {
        $this->_isForceJPGOutput = ($b ? true : false);
    }

    function setJpegQuality ($i) {
        $this->_iJpegQuality = $i;
    }

    function setSquareResize ($b) {
        $this->_isSquareResize = ($b ? true : false);
    }

    function isUsedGD () {
        return $this->_isUseGD;        
    }

    function getImageSize($sPath) {
        $aSize = getimagesize($sPath);
        return array ('w' => $aSize[0], 'h' => $aSize[1]);
    }

    // private functions are below -------------------------------

    function _resize ($sSrcImage, $sDstImage = '') {
	    if (!file_exists($sSrcImage))
            return IMAGE_ERROR_SOURCE_NOT_EXISTS;        

        if (!$sDstImage) 
            $sDstImage = $sSrcImage;

        if ($sDstImage == $sSrcImage) {
            chmod($sDstImage, 0666);
        }

        return $this->_isUseGD ? $this->_resizeGD ($sSrcImage, $sDstImage) : $this->_resizeImageMagick ($sSrcImage, $sDstImage);
    }

    function _resizeGD ($sSrcImage, $sDstImage) {

        $iErr = 0;
        $src_im =& $this->_createGDImage($sSrcImage, $size, $iErr);
        $sizeOrig = $size;

        if ($iErr) 
            return $iErr;
        if (!$src_im)
            return IMAGE_ERROR_GD_OPEN_FAILED;

        $xd = $yd = 0;
        $xs = $ys = 0;

        if ($this->_isCrop) {

            $size[0] = $this->_cropW;
            $size[1] = $this->_cropH;

            $xs = $this->_cropX;
            $ys = $this->_cropY;

            $destW = $this->w;
            $destH = $this->h;

        } elseif ($this->_isSquareResize) {

            $destW = $this->w;
            $destH = $this->h;

            if ($size[0] < $size[1]) {

                $d = ($size[1] - $size[0])/2;
                $size[1] = $size[0];

                $xs = 0;
                $ys = (int)$d;

            } else {

                $d = ($size[0] - $size[1])/2;
                $size[0] = $size[1];

                $xs = (int)$d;
                $ys = 0;

            }

        } else {            

            // determ destination size
            $sourceRatio = (float) ($size[0] / $size[1]);
            $destRatio = (float) ($this->w / $this->h);
            if ( $sourceRatio > $destRatio )
            {
                $resizeRatio = (float) ($this->w / $size[0]);
            }
            else
            {
                $resizeRatio = (float) ($this->h / $size[1]);
            }
            $destW = (int) ($resizeRatio * $size[0]);
            $destH = (int) ($resizeRatio * $size[1]);

        }

		// this is more qualitative function, but it doesn't exist in old GD and doesn't support GIF format
		if ( function_exists( 'imagecreatetruecolor' ) && $size[2] != IMAGE_TYPE_GIF )
		{
			// resize only if size is larger than needed
			if ( $this->_isCrop || $sizeOrig[0] > $this->w || $sizeOrig[1] > $this->h )
            {
                $dst_im = imagecreatetruecolor( $destW, $destH );
                imagecolortransparent($dst_im, imagecolorallocate($dst_im, 0, 0, 0));
                imagealphablending($dst_im, false);
                imagesavealpha($dst_im, true);
				$convertResult = imagecopyresampled( $dst_im, $src_im, $xd, $yd, $xs, $ys,
                    $destW, $destH, $size[0], $size[1] );
			}
			else
            {                
				$dst_im = $src_im;
				$convertResult = true;
			}
		}
		else // this is for old GD versions and for GIF images
		{
			// resize only if size is larger than needed
			if ( $size[0] > $this->w || $size[1] > $this->h )
			{
                $dst_im = imagecreate( $destW, $destH );
                if ($size[2] == IMAGE_TYPE_GIF) {
                    ImageColorTransparent( $dst_im, imagecolorallocate($dst_im, 0, 0, 0) );
                    imagealphablending( $dst_im, false ); 
                }
				$convertResult = imagecopyresized( $dst_im, $src_im, $xd, $yd, $xs, $ys,
					$destW, $destH, $size[0], $size[1] );
			}
			else
			{
				$dst_im = $src_im;
				$convertResult = true;
			}
		}

		if ( !$convertResult )
			return IMAGE_ERROR_GD_RESIZE_ERROR;

		// if output always in JPG
		if ( $forceJPGOutput )
        {            
			$writeResult = imagejpeg( $dst_im, $sDstImage, $this->_iJpegQuality);
		}
		else // otherwise
        {            
			switch ( $size[2] )
			{
				case IMAGE_TYPE_GIF:
					$writeResult = imagegif( $dst_im, $sDstImage );
					break;
                case IMAGE_TYPE_JPG:
					$writeResult = imagejpeg( $dst_im, $sDstImage, $this->_iJpegQuality);
					break;
				case IMAGE_TYPE_PNG:
					$writeResult = imagepng( $dst_im, $sDstImage );
					break;
			}
		}

		// free memory
		if ( $dst_im != $src_im )
		{
			imagedestroy( $src_im );
			imagedestroy( $dst_im );
		}
		else
		{
			imagedestroy( $src_im );
		}

		if ( $writeResult && file_exists($sDstImage) )
			return IMAGE_ERROR_SUCCESS;

		return IMAGE_ERROR_GD_WRITE_FAILED;
    }    

    function _resizeImageMagick ($sSrcImage, $sDstImage) {

        // TODO: $this->_isCrop and $this->_isSquareResize
		if ( $sSrcImage == $sDstImage )
		{
			$cmd = "{$GLOBALS['MOGRIFY']} -geometry {$this->w}\">\"x{$this->h}\">\" \"$sSrcImage\"";
			@exec( $cmd );
			$ext = strrchr($sSrcImage, '.');
			$nameWithoutExt = substr( $sSrcImage, 0, strrpos($sSrcImage, '.') );
			if ( file_exists( "{$nameWithoutExt}.mgk" ) )
				rename( "{$nameWithoutExt}.mgk", $sSrcImage );
		}
		else
		{
			$cmd = "{$GLOBALS['CONVERT']} \"$sSrcImage\" -geometry {$this->w}\">\"x{$this->h}\">\" \"$sDstImage\"";
			@exec( $cmd );
		}

		if ( file_exists($sDstImage) )
			return IMAGE_ERROR_SUCCESS;

		return IMAGE_ERROR_IMAGEMAGICK_ERROR;
    }    


    function _applyWatermark( $srcFilename, $dstFilename, $wtrFilename, $wtrTransparency )
    {
        // input validation
        $wtrTransparency = (int) $wtrTransparency;
        if ( $wtrTransparency > 100 )
            $wtrTransparency = 100;
        if ( !file_exists( $srcFilename ) )
            return IMAGE_ERROR_SOURCE_NOT_EXISTS;
        if ( !file_exists( $wtrFilename ) )
            return IMAGE_ERROR_SOURCE_NOT_EXISTS;

        // if destination and source filenames are equivalent then change mode for destination
        if ( $srcFilename == $dstFilename )
            chmod( $dstFilename, 0666 );

        if ( $this->_isUseGD )
        {
            return $this->_applyWatermarkGD($srcFilename, $dstFilename, $wtrFilename, $wtrTransparency);
        }
        else
        {
            return $this->_applyWatermarkImageMagick($srcFilename, $dstFilename, $wtrFilename, $wtrTransparency);
        }
    }

    function _applyWatermarkGD($srcFilename, $dstFilename, $wtrFilename, $wtrTransparency) {

        $iErr = 0;

        $src_im =& $this->_createGDImage($srcFilename, $size, $iErr);
        if ($iErr) 
            return $iErr;
        if (!$src_im)
            return IMAGE_ERROR_GD_OPEN_FAILED;

        $wtr_im =& $this->_createGDImage($wtrFilename, $wtrSize, $iErr);
        if ($iErr) 
            return $iErr;

        if (!$wtr_im )
            return IMAGE_ERROR_GD_OPEN_FAILED;

        if (function_exists('imagecreatetruecolor'))
            $dst_im = imagecreatetruecolor( $size[0], $size[1] );
        else
            $dst_im = imagecreate( $size[0], $size[1] );

        $watermarkX = ($size[0] - $wtrSize[0]) / 2;
        $watermarkY = $size[1] - $wtrSize[1];

        $copyResult = imagecopy( $dst_im, $src_im, 0, 0, 0, 0, $size[0], $size[1]);        
        if (!$copyResult)
            return IMAGE_ERROR_GD_MERGE_ERROR;

        
        imagealphablending($wtr_im, false);
        imagesavealpha($wtr_im, true);

        if (IMAGE_TYPE_PNG == $wtrSize[2] && imageistruecolor($wtr_im))
            $mergeResult = imagecopy  ( $dst_im, $wtr_im, $watermarkX, $watermarkY, 0, 0, $wtrSize[0], $wtrSize[1]);
        else
            $mergeResult = imagecopymerge  ( $dst_im, $wtr_im, $watermarkX, $watermarkY, 0, 0, $wtrSize[0], $wtrSize[1], $wtrTransparency );

        if ( !$mergeResult )
            return IMAGE_ERROR_GD_MERGE_ERROR;

        switch ( $size[2] )
        {
            case IMAGE_TYPE_GIF:
                $writeResult = imagegif( $dst_im, $dstFilename );
                break;
            case IMAGE_TYPE_JPG:
                $writeResult = imagejpeg( $dst_im, $dstFilename, $this->_iJpegQuality);
                break;
            case IMAGE_TYPE_PNG:
                $writeResult = imagepng( $dst_im, $dstFilename );
                break;
        }

        // free memory
        imagedestroy( $dst_im );
        imagedestroy( $src_im );
        imagedestroy( $wtr_im );

        if ( $writeResult )
            return IMAGE_ERROR_SUCCESS;
        else
            return IMAGE_ERROR_GD_WRITE_FAILED;    
    }


      
    function _applyWatermarkImageMagick($srcFilename, $dstFilename, $wtrFilename, $wtrTransparency) {

        $imgTransparency = 100 - $wtrTransparency;
        $cmd = "{$GLOBALS['COMPOSITE']} -gravity \"South\" -dissolve \"$imgTransparency\" \"$wtrFilename\" -dissolve $wtrTransparency \"$srcFilename\" \"$dstFilename\"";
        @exec( $cmd );

        if ( file_exists($dstFilename) )
            return IMAGE_ERROR_SUCCESS;

        return IMAGE_ERROR_IMAGEMAGICK_ERROR;
    }    

    function & _createGDImage($s, &$aSize, &$iErr) {

        if (!isset($this->gdInfoArray))
            $this->gdInfoArray = gd_info();

        $aSize = getimagesize($s);

        // only GIF, JPG and PNG allowed
        switch ( $aSize[2] )
        {
            case IMAGE_TYPE_GIF:
                if ( !$this->gdInfoArray['GIF Read Support'] || !$this->gdInfoArray['GIF Create Support'] )
                    return ($iErr = IMAGE_ERROR_GD_TYPE_NOT_SUPPORTED);
                return imagecreatefromgif( $s );
            case IMAGE_TYPE_JPG:
                if ( !$this->gdInfoArray['JPG Support'] && !$this->gdInfoArray['JPEG Support'] )
                    return ($iErr = IMAGE_ERROR_GD_TYPE_NOT_SUPPORTED);
                return imagecreatefromjpeg( $s );
            case IMAGE_TYPE_PNG:
                if ( !$this->gdInfoArray['PNG Support'] )
                    return ($iErr = IMAGE_ERROR_GD_TYPE_NOT_SUPPORTED);
                return imagecreatefrompng( $s );
            default:
                return ($iErr = IMAGE_ERROR_WRONG_TYPE);
        }
    }
}

?>

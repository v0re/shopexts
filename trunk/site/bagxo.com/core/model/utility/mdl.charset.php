<?php
class mdl_charset{

    function local2utf($strFrom,$charset='zh') {
        return $this->utfconvert($strFrom,$charset,false);
    }

    function utf2local($strFrom,$charset='zh') {
        return $this->utfconvert($strFrom,$charset,true);
    }

    function utfconvert($strFrom,$charset,$isfromUtf=false){
        if (!trim($strFrom)) return $strFrom;
        $fileGBU = fopen(CORE_DIR.'/lib/charset/'.($isfromUtf?'utf2'.$charset:$charset.'2utf').'.dat', "rb");
        $strBuf = fread($fileGBU, 2);
        $intCount = ord($strBuf{0}) + 256 * ord($strBuf{1});
        $strRet = "";
        $intLen = strlen($strFrom);
        for ($i = 0; $i < $intLen; $i++) {
            if (ord($strFrom{$i}) > 127) {
                $strCurr = substr($strFrom, $i, $isfromUtf?3:2);
                if($isfromUtf){
                    $intGB = $this->utf82u($strCurr);
                }else{
                    $intGB = hexdec(bin2hex($strCurr));
                }
                $intStart = 1;
                $intEnd = $intCount;
                while ($intStart < $intEnd - 1) {
                    $intMid = floor(($intStart + $intEnd) / 2);
                    $intOffset = 2 + 4 * ($intMid - 1);
                    fseek($fileGBU, $intOffset);
                    $strBuf = fread($fileGBU, 2);
                    $intCode = ord($strBuf{0}) + 256 * ord($strBuf{1});
                    if ($intGB == $intCode) {
                        $intStart = $intMid;
                        break;
                    }
                    if ($intGB > $intCode) $intStart = $intMid;
                    else $intEnd = $intMid;
                }
                $intOffset = 2 + 4 * ($intStart - 1);
                fseek($fileGBU, $intOffset);
                $strBuf = fread($fileGBU, 2);
                $intCode = ord($strBuf{0}) + 256 * ord($strBuf{1});
                if ($intGB == $intCode) {
                    $strBuf = fread($fileGBU, 2);
                    if($isfromUtf){
                        $strRet .= $strBuf{1}.$strBuf{0};
                    }else{
                        $intCodeU = ord($strBuf{0}) + 256 * ord($strBuf{1});
                        $strRet .= $this->u2utf8($intCodeU);
                    }
                } else {
                    $strRet .= "??";
                }
                $i+=$isfromUtf?2:1;
            } else {
                $strRet .= $strFrom{$i};
            }
        }
        return $strRet;
    }

    function u2utf8($c) {
        $str='';
        if ($c < 0x80) {
            $str.=$c;
        }
        else if ($c < 0x800) {
            $str.=chr(0xC0 | $c>>6);
            $str.=chr(0x80 | $c & 0x3F);
        }
        else if ($c < 0x10000) {
            $str.=chr(0xE0 | $c>>12);
            $str.=chr(0x80 | $c>>6 & 0x3F);
            $str.=chr(0x80 | $c & 0x3F);
        }
        else if ($c < 0x200000) {
            $str.=chr(0xF0 | $c>>18);
            $str.=chr(0x80 | $c>>12 & 0x3F);
            $str.=chr(0x80 | $c>>6 & 0x3F);
            $str.=chr(0x80 | $c & 0x3F);
        }
        return $str;
    }

    
    function utf82u($Char){
        switch(strlen($Char)){
            case 1:
                return ord($Char);
            case 2:
                $OutStr=(ord($Char[0])&0x3f)<<6;
                $OutStr+=ord($Char[1])&0x3f;
                return $OutStr;
            case 3:
                $OutStr=(ord($Char[0])&0x1f)<<12;
                $OutStr+=(ord($Char[1])&0x3f)<<6;
                $OutStr+=ord($Char[2])&0x3f;
                return $OutStr;
            case 4:
                $OutStr=(ord($Char[0])&0x0f)<<18;
                $OutStr+=(ord($Char[1])&0x3f)<<12;
                $OutStr+=(ord($Char[2])&0x3f)<<6;
                $OutStr+=ord($Char[3])&0x3f;
                return $OutStr;
        }
    }

}

?>

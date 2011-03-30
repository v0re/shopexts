<?php
function smarty_modifier_nicetime($string)
{
    if (ereg('(19|20[0-9]{2})[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01]) ([012 ][0-9])[: .]([0-5][0-9])[: .]([0-5][0-9])[ \\.].*', $string, $regs)) {
        $unixtime = gmmktime($regs[4],$regs[5],$regs[6],$regs[2],$regs[3],$regs[1]);
        $time = time()+ 28800 - $unixtime;
        if($time < 24*3600*5){
            if($time < 48*3600){
                if($time < 24*3600){
                    if($time < 5*3600){
                        if($time < 3600){
                            if($time < 60){
                                return $time.'秒前';
                            }else{
                                return floor($time/60).'分钟前';
                            }
                        }else{
                            $min = floor(($time%3600)/60);
                            return floor($time/3600).'小时'.(($min>0)?$min.'分钟':'').'前';
                        }
                    }else{
                        return '今天 '.$regs[4].':'.$regs[5].':'.$regs[6];
                    }
                }else{
                    return '昨天';
                }
            }else{
                return floor($time/(24*3600)).'天前';
            }
        }else{
            return $string;
        }
    } else {
        return $string;
    }    
}
?>
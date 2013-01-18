<?php
// $Id: util.php 895 2010-03-23 05:36:29Z thinkgem $

/**
 * 常用助手
 * @author WangZhen <thinkgem@163.com>
 */
class Helper_Util
{
    /**
     * 裁剪字符串，加“...”
     */
    static function substr($str, $length, $endfix='...')
    {
        mb_internal_encoding("UTF-8");
        $str_length = mb_strwidth($str);
        if($str_length>$length*2){
            return mb_substr($str,0,$length).$endfix;
        }else{
            return $str;
        }
    }

    /*
     * 裁剪字符串，不加“...”
     */
    static function cutstr($str, $startstr, $endstr)
    {
        $length = strlen($str);
        $start = mb_strpos($str, $startstr);
        $str = substr($str,$start,$length-$start);
        $end = mb_strpos($str,$endstr);
        return mb_substr($str, 0, $end);
    }

    /*
     * 获得客户端IP
     */
    static function getIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
        else
        $ip = "unknown";
        return($ip);
    }

    /*
     * 将任意时间字符串转化成时间戳
     */
    static function mktime($dtime)
    {
        if(!ereg("[^0-9]",$dtime)) return $dtime;
        $dt = Array(1970,1,1,0,0,0);
        $dtime = ereg_replace("[\r\n\t]|日|秒"," ",$dtime);
        $dtime = str_replace(".","-",$dtime);
        $dtime = str_replace("年","-",$dtime);
        $dtime = str_replace("月","-",$dtime);
        $dtime = str_replace("时",":",$dtime);
        $dtime = str_replace("分",":",$dtime);
        $dtime = trim(ereg_replace("[ ]{1,}"," ",$dtime));
        $ds = explode(" ",$dtime);
        $ymd = explode("-",$ds[0]);
        if(isset($ymd[0])) $dt[0] = $ymd[0];
        if(isset($ymd[1])) $dt[1] = $ymd[1];
        if(isset($ymd[2])) $dt[2] = $ymd[2];
        if(strlen($dt[0])==2) $dt[0] = '20'.$dt[0];
        if(isset($ds[1])){
            $hms = explode(":",$ds[1]);
            if(isset($hms[0])) $dt[3] = $hms[0];
            if(isset($hms[1])) $dt[4] = $hms[1];
            if(isset($hms[2])) $dt[5] = $hms[2];
        }
        foreach($dt as $k=>$v){
            $v = ereg_replace("^0{1,}","",trim($v));
            if($v=="") $dt[$k] = 0;
        }
        $mt = @mktime($dt[3],$dt[4],$dt[5],$dt[1],$dt[2],$dt[0]);
        if($mt>0) return $mt;
        else return time();
    }

    /*
     * 读取远程文件内容，使用curl库，有超时设置
     */
    static function file_get_contents($url, $timeout=10)
    {
        if ( function_exists("curl_init") )    {
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        } else if ( ini_get("allow_url_fopen") == 1 || strtolower(ini_get("allow_url_fopen")) == "on" )    {
            $file_contents = file_get_contents($url);
        } else {
            $file_contents = "";
        }
        return $file_contents;
    }

    /*
     * 自动探测字符编码，并转换到指定编码
     */
    static function encoding($data,$to)
    {
        $encode_arr = array('UTF-8','GBK','GB2312','BIG5');
        $encoded = mb_detect_encoding($data, $encode_arr);
        $data = mb_convert_encoding($data,$to,$encoded);
        return $data;
    }

    /**
     * 格式化文件大小
     */
    static function formattedFileSize($size) {
        if ($size != 0) {
            if ($size>=1099511627776) $size = round($size / 1024 / 1024 / 1024 / 1024, 2)." TB";
            elseif ($size>=1073741824) $size = round($size / 1024 / 1024 / 1024, 2)." GB";
            elseif ($size>=1048576) $size = round($size / 1024 / 1024, 2)." MB";
            elseif ($size>=1024) $size = round($size / 1024, 2)." KB";
            elseif ($size<1024) $size = round($size / 1024, 2)." B";
        }
        return $size;
    }
}
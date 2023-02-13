<?php
// +----------------------------------------------------------------------
// | common 应用公共文件
// +----------------------------------------------------------------------
// | Copyright (c) 2023, All rights reserved.
// +----------------------------------------------------------------------
// | Author: cgf <admin@123.com> 2023/2/13
// +----------------------------------------------------------------------
if (!function_exists('subpart')) {
    /**
     * 根据字数截取替换字符串
     * @param string $cont 字符串
     * @param int $l 长度
     * @param string $utf utf8
     * @return array
     */
    function subpart(string $cont, int $l, string $utf): array
    {
        $len = mb_strlen($cont, $utf);
        for ($i = 0; $i < $len; $i += $l)
            $arr[] = mb_substr($cont, $i, $l, $utf);
        return $arr;
    }
}


if (!function_exists('numToCNMoney')) {
    /**
     * 阿拉伯数字转中文大写金额
     * @param $num
     * @return string
     */
    function numToCNMoney($num)
    {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "金额太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j - 3;
                $slen = $slen - 3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        } else {
            return $c . "整";
        }
    }
}

if (!function_exists('sortMultiArray')) {
    /**
     * 使用冒泡排序法进行排序
     * @param $arr
     * @param $key
     * @return mixed
     */
    function sortMultiArray($arr, $key)
    {
        $len = count($arr);
        for ($i = 0; $i < $len - 1; $i++) {
            for ($j = $i + 1; $j < $len; $j++) {
                if ($arr[$i][$key] > $arr[$j][$key]) {
                    $tmp = $arr[$i][$key];
                    $arr[$i][$key] = $arr[$j][$key];
                    $arr[$j][$key] = $tmp;
                }
            }
        }
        return $arr;
    }
}

if (!function_exists('curl_request')) {
    /**
     * 使用curl函数库发送请求
     */
    function curl_request($url, $method = 'get', $params = [], $type = 'http')
    {
        /**
         * 初始化curl，返回资源
         */
        $curl = curl_init($url);
        /**
         * 默认是get请求，如果是post/put请求，设置请求方式和请求参数
         */
        if (strtoupper($method) == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        }
        if (strtoupper($method) == 'PUT') {
            curl_setopt($curl, CURLOPT_PUT, true);
        }
        /**
         * 如果是HTTPS协议，禁止从服务器验证本地证书
         */
        if (strtoupper($type) == 'HTTPS') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        /**
         * 发送请求，返回结果
         */
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        /**
         * 选择地址
         */
        $res = curl_exec($curl);
        /**
         * 关闭请求
         */
        curl_close($curl);
        return $res;
    }
}

if (!function_exists('toChinaseNum')) {
    /**
     * 阿拉伯数字转中文
     * @param int $num 数字
     * @return string
     */
    function toChinaseNum(int $num)
    {
        $char = ["零", "一", "二", "三", "四", "五", "六", "七", "八", "九"];
        $dw = ["", "十", "百", "千", "万", "亿", "兆"];
        $retval = "";
        $proZero = false;
        for ($i = 0; $i < strlen($num); $i++) {
            if ($i > 0) $temp = (int)(($num % pow(10, $i + 1)) / pow(10, $i));
            else $temp = (int)($num % pow(10, 1));

            if ($proZero == true && $temp == 0) continue;

            if ($temp == 0) $proZero = true;
            else $proZero = false;

            if ($proZero) {
                if ($retval == "") continue;
                $retval = $char[$temp] . $retval;
            } else $retval = $char[$temp] . $dw[$i] . $retval;
        }
        if ($retval == "一十") $retval = "十";
        return $retval;
    }
}

if (!function_exists('wordTime')) {
    /**
     * 把时间戳转换为几分钟或几小时前或几天前
     * @param int $time
     * @return string
     */
    function wordTime(int $time)
    {
        $time = (int)substr($time, 0, 10);
        $int = time() - $time;
        if ($int <= 30) {
            $str = '刚刚';
        } else if ($int < 60) {
            $str = sprintf('%d秒前', $int);
        } else if ($int < 3600) {
            $str = sprintf('%d分钟前', floor($int / 60));
        } else if ($int < 86400) {
            $str = sprintf('%d小时前', floor($int / 3600));
        } else if ($int < 2592000) {
            $str = sprintf('%d天前', floor($int / 86400));
        } else {
            $str = date('Y-m-d H:i:s', $time);
        }
        return $str;
    }
}

if (!function_exists('getDistance')) {
    /**
     * 获取两个位置的距离
     * @param float $longitude1 起点经度
     * @param float $latitude1 起点纬度
     * @param float $longitude2 终点经度
     * @param float $latitude2 终点纬度
     * @param int $unit 单位 1:米 2:公里
     * @param int $decimal 精度 保留小数位数
     * @return float
     */
    function getDistance(float $longitude1, float $latitude1, float $longitude2, float $latitude2, $unit = 2, $decimal = 2)
    {
        //$EARTH_RADIUS = 6370.996; // 地球半径系数
        $EARTH_RADIUS = 6378.137; // 地球半径系数
        $PI = M_PI;
        $radLat1 = $latitude1 * $PI / 180.0;
        $radLat2 = $latitude2 * $PI / 180.0;

        $radLng1 = $longitude1 * $PI / 180.0;
        $radLng2 = $longitude2 * $PI / 180.0;

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $distance = $distance * $EARTH_RADIUS * 1000;

        if ($unit == 2) {
            $distance = $distance / 1000;
        }
        return round($distance, $decimal);
    }
}

if (!function_exists('show')) {
    /**
     * 返回封装后的api数据到客户端
     * @param int $status
     * @param string $msg
     * @param array $data
     * @param string $type
     * @param array $header
     * @return \think\Response
     */
    function show($status = 0, $msg = 'success', $data = [], $type = 'json', $header = [])
    {
        $result = [
            'status' => !empty($status) ? 1 : 0,
            'msg' => $msg,
            'data' => $data,
        ];
        return \think\Response::create($result, $type)->header($header);
    }
}

if (!function_exists('getTree')) {
    /**
     * 无限极分类
     * @param $array
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $level
     * @return array
     */
    function getTree($array, $pk = 'id', $pid = 'parent_id', $child = 'child', $level = 0)
    {
        $tree = [];
        $packData = [];
        foreach ($array as $data) {
            $packData[$data[$pk]] = $data;
        }
        foreach ($packData as $key => $val) {
            if ($val[$pid] == $level) {//代表跟节点
                $tree[] = &$packData[$key];
            } else {
                //找到其父类
                $packData[$val[$pid]][$child][] = &$packData[$key];
            }
        }
        return $tree;
    }
}

if (!function_exists('array2file')) {
    /**
     * 调试，用于保存数组到txt文件 正式生产删除
     * 用法：array2file($info, runtime_path().'post.txt');
     * @param array|string $array
     * @param string $filename
     * @return bool|int
     */
    function array2file($array, $filename)
    {
        //修改文件时间
        file_exists($filename) or touch($filename);
        if (is_array($array)) {
            $str = var_export($array, true);
        } else {
            $str = $array;
        }
        return file_put_contents($filename, $str);
    }
}

if (!function_exists('genRandomString')) {
    /**
     * 产生一个指定长度的随机字符串,并返回给用户
     * @param int $len 产生字符串的长度
     * @return string 随机字符串
     */
    function genRandomString($len = 6)
    {
        $chars = [
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9",
        ];
        $charsLen = count($chars) - 1;
        // 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }
}

if (!function_exists('strCut')) {
    /**
     * 字符截取
     * @param string $sourcestr 需要截取的字符串
     * @param int $length 长度
     * @param string $dot
     * @return string
     */
    function strCut($sourcestr, $length = 0, $dot = '...')
    {
        $returnstr = '';
        $i = 0;
        $n = 0;
        $str_length = strlen($sourcestr); //字符串的字节数
        while (($n < $length) && ($i <= $str_length)) {
            $temp_str = substr($sourcestr, $i, 1);
            $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码
            if ($ascnum >= 224) {//如果ASCII位高与224，
                $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
                $i = $i + 3; //实际Byte计为3
                $n++; //字串长度计1
            } else if ($ascnum >= 192) { //如果ASCII位高与192，
                $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
                $i = $i + 2; //实际Byte计为2
                $n++; //字串长度计1
            } else if ($ascnum >= 65 && $ascnum <= 90) { //如果是大写字母，
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1; //实际的Byte数仍计1个
                $n++; //但考虑整体美观，大写字母计成一个高位字符
            } else {//其他情况下，包括小写字母和半角标点符号，
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;            //实际的Byte数计1个
                $n = $n + 0.5;        //小写字母和半角标点等与半个高位字符宽...
            }
        }
        if ($str_length > strlen($returnstr)) {
            $returnstr = $returnstr . $dot; //超过长度时在尾处加上省略号
        }
        return $returnstr;
    }
}

if (!function_exists('U')) {
    /**
     * URL生成 支持路由反射
     * @param string $url URL表达式，
     * 格式：'[模块/控制器/操作]?参数1=值1&参数2=值2...'
     * @控制器/操作?参数1=值1&参数2=值2...
     * \\命名空间类\\方法?参数1=值1&参数2=值2...
     * @param string|array $vars 传入的参数，支持数组和字符串
     * @param string|bool $suffix 伪静态后缀，默认为true表示获取配置值
     * @param boolean|string $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function U($url = '', $vars = '', $suffix = true, $domain = false)
    {
        $vars = empty($vars) ? [] : $vars;
        $url = url($url, $vars, $suffix, $domain)->build();
        if (substr($url, -17) == '/index/index.html') {
            $url = substr($url, 0, -16);
        } else if (substr($url, -11) == '/index.html') {
            $url = substr($url, 0, -10);
        }
        return $url;
    }
}
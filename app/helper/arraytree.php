<?php
// $Id: arraytree.php 895 2010-03-23 05:36:29Z thinkgem $

class Helper_ArrayTree {
    /**
     * 把一个Tree形数组转换成一个一维数组，用于方便地显示
     *
     * @param array  $tree 原始Tree形数组
     * @param array  $arr 二维数组
     * @param string $level 目录深度
     * @param string $T 上下均有项目的符号，可以是图片路径
     * @param string $L 这一级别中最末尾项目的符号，可以是图片路径
     * @param string $I 上级连接符，可以是图片路径
     * @param string $S 占位的空白符号，可以是图片路径
     *
     * 类似下面的效果
     * ├- 1
     * │  ├- 1.1
     * │  └- 1.2
     * └- 2
     */
    static function dumpArrayTree($tree, $level=0, $T='├', $L='└', $I='│', $S='　') {
        return self::_makeLevelstr(self::_dumpArrayTree($tree, array(), $level, $T, $L, $I, $S), $T, $L, $I, $S);
    }

    static function _dumpArrayTree($tree, $arr=array(), $level=0, $T='├', $L='└', $I='│', $S='　') {
        foreach ($tree as $node) {
            $arr[] = $node;
            $arr[count($arr)-1]['level'] = $level;
            //如果存在下级类目，则去掉该键值，并加深一层类目深度
            if(isset($arr[count($arr)-1]['childrens'])) {
                unset($arr[count($arr)-1]['childrens']);
                $level = $level + 1;
            }
            //如果childrens仍有数据则递归一下
            if (isset($node['childrens'])) {
                $arr = self::_dumpArrayTree($node['childrens'], $arr, $level, $T, $L, $I, $S);
                $level = $level - 1;
            }
        }
        return $arr;
    }

    static function _makeLevelstr($arr, $T, $L, $I, $S) {
        foreach ($arr as $key=>$value) {
            $arr[$key]['levelstr'] = '';
            //向下循环到数组尾部，寻找层特征
            $k = 0;
            $haveBrother = false;
            for($k=$key; $k<count($arr); $k++) {
                if(isset($arr[$k+1])) {
                    //有平级目录
                    if($arr[$key]['level'] == $arr[$k+1]['level']) {
                        $haveBrother = true;
                    }
                    //本级别结束
                    if($arr[$key]['level'] > $arr[$k+1]['level']) {
                        break;
                    }
                }
            }
            if ($haveBrother) {
                $arr[$key]['levelstr'] = $T;
                $arr[$key]['isend'] = false;
            }
            else {
                $arr[$key]['levelstr'] = $L;
                // isend 为 true 意味着这个节点是本级最后一个节点
                $arr[$key]['isend'] = true;
            }
            // $spaceHere 用来记录连接线的形态，表示当前行第几级是空白
            $spaceHere = array();
            // 逐级向上循环
            for($k=$key-1;$k>=0;$k=$k-1) {
                //如果$k是同级尾部isend=true
                if($arr[$k]['isend']) {
                    $spaceHere[$key][$arr[$k]['level']] = true;
                }
                // 判断到根后中断循环
                if($arr[$k]['level']==0) {
                    break;
                }
            }
            //根据级别判定显示连接线的显示
            $frontLine = '';
            for($j=0; $j<$value['level']; $j++) {
                if(isset($spaceHere[$key][$j])) {
                    $frontLine .= $S . $S;
                }
                else {
                    $frontLine .= $I . $S;
                }
            }
            $arr[$key]['levelstr'] = $frontLine . $arr[$key]['levelstr'];
        }
        return $arr;
    }


    /**
     * 将一个数组的所有元素变为小写字母
     * @param array $arrin
     * @param array $arrout
     * @return $arrout
     */
    static function array_to_lower($arrin, $arrout = array()) {
        foreach ($arrin as $key=>$value) {
            if(is_array($value)) {
                $arrout[$key] = self::array_to_lower($arrin[$key], $arrout);
            }
            $arrout[$key] = strtolower($value);
        }
        return $arrout;
    }

    /**
     * 判断一个字符串是否是数组中的值
     *
     * @param string $find
     * @param arr $inarr
     */
    static function find_in_array($find, $inarr) {
        return in_array($find, self::_expArray($inarr));
    }

    /**
     * 返回一个多维数组的所有值组成的二维数组
     *
     * @param array $arrin
     * @param array $arrout
     * @return $arrout
     */
    static function _expArray($arrin, $arrout = array() ) {
        foreach ($arrin as $value) {
            if(is_array($value)) {
                $arrout = self::_expArray($value, $arrout);
            }
            else {
                $arrout[] = $value;
            }
        }
        return $arrout;
    }
    
}
?>
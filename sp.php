<?php

/**
 * Created by PhpStorm.
 * User: zzz
 * Date: 2018-12-06
 * Time: 15:11
 */
//==============================================================================
//读取xml
$xml = simplexml_load_file('img.xml');
//对象转数组
$xml = object_to_array($xml);
$groupList = [];
foreach ($xml["Group"] as $gk => $group) {
    unset($group['comment']);
    $groupList[] = getGroup($gk, $group);
//    echo "<pre>";
//    print_r($img);
}
//==============================================================================
/**
 * 对象 转 数组
 *
 * @param object $obj 对象
 * @return array
 */
function object_to_array($obj) {
    $obj = (array) $obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array) object_to_array($v);
        }
    }
    return $obj;
}

/**
 * 常量转换
 * @param type $str
 * @return type
 */
function tranCl($str) {
    switch ($str) {
        case "#year/1000":
            return floor(date('Y', time()) / 1000);
            break;
        case "#year/100%10":
            return floor(date('Y', time()) / 100) % 10;
            break;
        case "#year%100/10":
            return floor((date('Y', time()) % 100) / 10);
            break;
        case "#year%10":
            return date('Y', time()) % 10;
            break;
        case "(#month+1)/10":
            return floor(date('m', time()) / 10);
            break;
        case "(#month+1)%10":
            return date('m', time()) % 10;
            break;
        case "#date/10":
            return floor(date('d', time()) / 10);
            break;
        case "#date%10":
            return date('d', time()) % 10;
            break;
        case "#day_of_week":
            return date('w', time()) + 1;
            break;
    }
}



/**
 * 获取组内图片组
 * @param type $gk
 * @param type $group
 * @return string
 */
function getGroup($gk, $group) {
    unset($group['Image']['comment']);
    $img = $group['Image'];
    $x0 = $group['@attributes']['x'];
    $y0 = $group['@attributes']['y'];
    foreach ($img as $k => $v) {
        unset($v['comment']);
//    遍历图片
//    var_dump($v['@attributes']);

        $imgProperty = $v['@attributes'];
        if (empty($v['@attributes']['srcid'])) {
            $imgsrc = $imgProperty['src'];
        } else {
            $num = tranCl($imgProperty['srcid']);
//        echo $num;
            $srcArr = explode(".", $imgProperty['src']);
            $imgsrc = $srcArr[0] . "_" . $num . "." . $srcArr[1];
        }

        $imgInfo = getimagesize("lockscreen/" . $imgsrc);


//    var_dump($imgInfo);
//    图片节点
        if (empty($imgProperty['x'])) {
            $x1 = $x0;
        } else {
            $x1 = eval('return ' . $imgProperty['x'] . ';') + $x0;
        }
        if (empty($imgProperty['y'])) {
            $y1 = $y0;
        } else {
            $y1 = eval('return ' . $imgProperty['y'] . ';') + $y0;
        }
        $imgStr = 'addDom("img' . $gk . '-' . $k . '","lockscreen/' . $imgsrc . '",'
                . $imgInfo[0] . ',' . $imgInfo[1] . ','
                . $x1 . ','
                . $y1 . ');';
//    初始位置
        $x = $x1;
        $y = $y1;

        $imgList[$k]['dom'] = $imgStr;
        $PositionScript = "";
        $AlphaScript = "";
        $RotationScript = "";
        foreach ($v as $kk => $vv) {
//        遍历动作
//        var_dump($kk);

            if ($kk == "PositionAnimation") {
                unset($vv['comment']);
//                遍历每个动作的分解
                foreach ($vv['Position'] as $kkk => $vvv) {

                    $vvv = $vvv['@attributes'];
                    $arr['time'] = $vvv['time'];
                    $arr['css'] = 'left:"' . ($x + $vvv['x']) . 'px",top:"' . ($y + $vvv['y']) . 'px",';
                    $PositionScript[] = $arr;
                }
            }
            if ($kk == "AlphaAnimation") {
                unset($vv['comment']);
//                遍历每个动作的分解
                foreach ($vv['Alpha'] as $kkk => $vvv) {
//                var_dump($vvv);
                    $vvv = $vvv['@attributes'];
                    $arr['time'] = $vvv['time'];
                    $arr['css'] = 'opacity:"' . ($vvv['a'] / 255) . '",';
                    $AlphaScript[] = $arr;
                }
            }

            if ($kk == "RotationAnimation") {
                unset($vv['comment']);
//                遍历每个动作的分解
                foreach ($vv['Rotation'] as $kkk => $vvv) {
//                var_dump($vvv);
                    $vvv = $vvv['@attributes'];
                    $RotationScript .= 'ratateAction("img' . $gk . '-' . $k . '",0,' . $vvv['angle'] . ',' . $vvv['time'] . ');';
                }
            }
        }
        if (count($PositionScript) > count($AlphaScript)) {
            foreach ($PositionScript as $pk => $pv) {
                foreach ($AlphaScript as $ak => $av) {
                    if ($av['time'] == $pv['time']) {
                        $PositionScript[$pk]['css'] = $pv['css'] . $av['css'];
                        unset($AlphaScript[$ak]);
                    }
                }
            }
        } else {
            foreach ($AlphaScript as $ak => $av) {
                foreach ($PositionScript as $pk => $pv) {
                    if ($av['time'] == $pv['time']) {
                        $AlphaScript[$ak]['css'] = $pv['css'] . $av['css'];
                        unset($PositionScript[$pk]);
                    }
                }
            }
        }

        $ps = "";
        foreach ($PositionScript as $p) {
            $ps .= '$("#img' . $gk . '-' . $k . '").animate({' . $p['css'] . '},' . $p['time'] . ');';
        }
        $as = "";
        foreach ($AlphaScript as $a) {
            $as .= '$("#img' . $gk . '-' . $k . '").animate({' . $a['css'] . '},' . $a['time'] . ');';
        }

        $imgList[$k]['PositionScript'] = $ps;
        $imgList[$k]['AlphaScript'] = $as;
        $imgList[$k]['RotationScript'] = $RotationScript;
    }
    return $imgList;
}

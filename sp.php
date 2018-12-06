<?php

/**
 * Created by PhpStorm.
 * User: zzz
 * Date: 2018-12-06
 * Time: 15:11
 */
//$xml=file_get_contents("manifest.xml");
$xml = simplexml_load_file('img.xml');

$xml = object_to_array($xml);
//var_dump($xml);


unset($xml['Image']['comment']);
$img = $xml['Image'];
foreach ($img as $k => $v) {
    unset($v['comment']);
//    遍历图片
//    var_dump($v['@attributes']);
    $imgProperty = $v['@attributes'];
    $imgInfo = getimagesize("lockscreen/" . $imgProperty['src']);
//    var_dump($imgInfo);


    $str = '<img id="img' . $k . '" src="lockscreen/' . $imgProperty['src'] . '" style="position: absolute;height:' . $imgInfo[0] . 'px;height:' . $imgInfo[1] . 'px;left:' . $imgProperty['x'] . 'px;top:' . $imgProperty['y'] . 'px;">';
    echo $str;
    foreach ($v as $kk => $vv) {
//        遍历动作
        switch ($kk) {
            case "PositionAnimation":

                unset($vv['comment']);
//                遍历每个动作的分解
                foreach ($vv['Position'] as $kkk => $vvv) {
//                    var_dump($vvv);
                }

                break;
            case "AlphaAnimation":



                break;
            case "RotationAnimation":



                break;
        }
    }
}

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

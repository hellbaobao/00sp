<?php
include_once "sp.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Title</title>
        <style>
            *{padding: 0;margin: 0;}
        </style>
        <script src="jquery.min.js" type="text/javascript"></script>
        <script src="jquery.rotate.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            function addDom(id, src, h, w, x, y) {
                var imgStr = '<img id="' + id + '" src="' + src + '" style="position: absolute;width:' + h + 'px;height:' + w + 'px;left:' + x + 'px;top:' + y + 'px;">';
                $("#lockbg").append(imgStr);
            }


            function ratateAction(id, jd, to, time) {
                $('#' + id).rotate({
                    angle: jd,
                    animateTo: to,
                    duration: time
                });
            }

            $(function () {
<?php
foreach ($groupList as $imgList) {
    foreach ($imgList as $v) {
        foreach ($v as $value) {
            echo $value;
        }
    }
}
?>
            });


        </script>
    </head>
    <body>
        <div id="lockbg" style="background-color: #000;width:1080px;height:1920px;"></div>
    </body>
</html>
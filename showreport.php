<?php
require_once "header.php";
require_once "functions.php";
require_once "jpgraph/jpgraph.php";
require_once "jpgraph/jpgraph_line.php";
header("Cache-control: private");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $filename = "Data/" . $_GET["type"] . "/" . $_GET["name"] . "/" . $_GET["name"] . "." . $_GET["am"] . ".results.txt";
    $file = @fopen($filename, 'r');
    $content = array();
    $peaknodes = $bottomnodes = "";
    $zcol = 15;
    $method = 0;
    $axis = "z";
    $ff = 0;
    if (!$file) {
        echo "File open fails!";
    } else {
        while (!feof($file)) {
            $line = fgets($file);
            //获取列号
            if (strpos($line, "Axis: Z") !== false) {
                $zcol = 15;
            } elseif (strpos($line, "Axis: Y") !== false) {
                $zcol = 14;
                $axis = "y";
            } elseif (strpos($line, "Axis: X") !== false) {
                $zcol = 13;
                $axis = "x";
            } else {
                if (strpos($_GET["am"], "t") !== false) {
                    $zcol = 15;
                } else {
                    $zcol = 14;
                    $axis = "y";
                }
            }
            //回滚量
            if (strpos($line, "Fallback Frames:") !== false) {$ff = str_replace("Fallback Frames: ", "", trim($line));}
            //获取所有的波峰
            if (strpos($line, "Crests\t") !== false) {$peaknodes = str_replace("\t", ",", trim(str_replace("Crests\t", "", $line)));}
            //获得所有的波谷
            if (strpos($line, "Troughs\t") !== false) {$bottomnodes = str_replace("\t", ",", trim(str_replace("Troughs\t", "", $line)));}
            $content[] = $line;
        }
        echo GenerateReport($content);
    }
    echo "<br><b>Analysis Result Export:</b> <a target=\"_blank\" href=\"$filename\">Report</a><br><br>";
    fclose($file);
    //打开原始数据记录
    $filename = "Data/" . $_GET["type"] . "/" . $_GET["name"] . "/" . $_GET["name"] . "." . $_GET["am"] . ".txt";

    //获得行数
    $linenumber = count(file($filename));

    $coordate = array(); //全部的y/z坐标数据
    //读取数据文件
    $file = @fopen($filename, 'r');
    if (!$file) {
        echo "Raw data open fails!";
    } else {
        $i = 0;
        //文件循环开始
        while (!feof($file)) {
            $line = fgets($file);
            $IndexArray = explode("\t", $line);
            //开始记录主运动轴的数据
            if ($i > 2) {$coordate[] = $IndexArray[$zcol];}
            $i++;
        }
    }

    //生成图表
    $chartname = "Mild reinforcing-attenuating of lifting-thrusting";
    if ($_GET["am"] == "tp") {
        $chartname = "Mild reinforcing-attenuating of lifting-thrusting";
    } elseif ($_GET["am"] == "tb") {
        $chartname = "Reinforcing of lifting-thrusting";
        $method = 1;
    } elseif ($_GET["am"] == "tx") {
        $chartname = "Attenuating of lifting-thrusting";
        $method = 2;
    } elseif ($_GET["am"] == "np") {
        $chartname = "Attenuating of twirling";
        $method = 3;
    } elseif ($_GET["am"] == "nb") {
        $chartname = "Reinforcing of twirling";
        $method = 4;
    } elseif ($_GET["am"] == "nx") {
        $chartname = "Attenuating of twirling";
        $method = 5;
    }
    $src = generatechart($linenumber, $chartname, $_GET["name"], $coordate, explode(",", $peaknodes), explode(",", $bottomnodes));
    echo "<div id=\"chart\"><img src=\"data:image/png;base64,$src\" /></div>";
}
?>
</div>
<div class="line">
<form action="analyze.php" method="post" name="upload">
<input type="hidden" name="step" value="1">
<?php
echo "Fallback Frames: <input type=\"text\" size=\"2\" name=\"ff\" value=\"$ff\">\n";
echo "<input type=\"hidden\" name=\"type\" value=\"" . $_GET["type"] . "\">\n";
echo "<input type=\"hidden\" name=\"tester\" value=\"" . $_GET["name"] . "\">\n";
echo "<input type=\"hidden\" name=\"method\" value=\"$method\">\n";
echo "<input type=\"hidden\" name=\"enc\" value=\"15\">\n";
echo "<input type=\"hidden\" name=\"stn\" value=\"50\">\n";
echo "<input type=\"hidden\" name=\"axis\" value=\"$axis\">\n";
echo "<input type=\"hidden\" name=\"peaknumberarray\" value=\"$peaknodes\">\n";
echo "<input type=\"hidden\" name=\"bottomnumberarray\" value=\"$bottomnodes\">\n";
?>
<input type="submit" name="submit" value="Re-Analysis">
</form>
</div>
</body>
</html>

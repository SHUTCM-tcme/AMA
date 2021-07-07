<?php
require_once "header.php";
require_once "functions.php";
require_once "jpgraph/jpgraph.php";
require_once "jpgraph/jpgraph_line.php";
header("Cache-control: private");
$testername = $peakresult = $bottomresult = "";
$ammethods = array("Mild reinforcing-attenuating of lifting-thrusting|tp", "Reinforcing of lifting-thrusting|tb", "Attenuating of lifting-thrusting|tx", "Mild reinforcing-attenuating of twirling|np", "Reinforcing of twirling|nb", "Attenuating of twirling|nx");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //接收的参数
    //文件夹名字$_POST['tester']
    $testername = $_POST['tester'];
    //手法的名字$_POST['method']
    $methodnumber = 0;
    if (!empty($_POST['method'])) {$methodnumber = (int) $_POST['method'];}
    $am = (explode("|", $ammethods[$methodnumber]))[1];
    //对象类型$_POST['type']
    $testertype = "Teachers";
    if (!empty($_POST['type'])) {$testertype = $_POST['type'];}
    $cycles = $_POST['enc']; //预计包含的周期数$_POST['enc']
    $stn = $_POST['stn']; //开始计数点$_POST['stn']
    $ff = $_POST['ff']; //回滚帧数$_POST['ff']
    //轴设定$_POST['axis']
    if ($_POST['axis'] == "z") {
        $zcol = 15; //z轴坐标列号
        $zvcol = 18; //z轴速度列号
    } elseif ($_POST['axis'] == "y") {
        $zcol = 14; //y轴坐标列号
        $zvcol = 17; //y轴速度列号
    } elseif ($_POST['axis'] == "x") {
        $zcol = 13; //x轴坐标列号
        $zvcol = 16; //x轴速度列号
    }

    //读取数据
    $filename = "Data/" . $testertype . "/$testername/$testername.$am.txt";
    $file = @fopen($filename, 'r');

    //获得行数
    $linenumber = count(file($filename));

    //阈值设定
    if (strpos($filename, "p.txt") !== false) {
        $startmod = 0.4;
    } elseif (strpos($filename, "b.txt") !== false) {
        $startmod = 0.15;
    } elseif (strpos($filename, "x.txt") !== false) {
        $startmod = 0.4;
    }

    //开始处理
    if (!$file) {
        echo "File open fails!";
    } else {
        if ($_POST['step'] == "1") {
            //传递的参数
            echo "<form action=\"analyze.php\" method=\"post\" name=\"final\">\n";
            echo "<input type=\"hidden\" name=\"type\" value=\"$testertype\">\n";
            echo "<input type=\"hidden\" name=\"step\" value=\"2\">\n";
            echo "<input type=\"hidden\" name=\"tester\" value=\"$testername\">\n";
            echo "<input type=\"hidden\" name=\"method\" value=\"" . $_POST['method'] . "\">\n";
            echo "<input type=\"hidden\" name=\"enc\" value=\"$cycles\">\n";
            echo "<input type=\"hidden\" name=\"stn\" value=\"$stn\">\n";
            echo "<input type=\"hidden\" name=\"ff\" value=\"$ff\">\n";
            echo "<input type=\"hidden\" name=\"axis\" value=\"" . $_POST['axis'] . "\">\n";

            //相关的设置
            $start = false; //启动开关
            $timer1 = $timer2 = 0; //计时器1和2
            $peak = $bottom = ""; //波峰、波谷、坐标
            $peakcut = $bottomcut = false; //出现无效波峰、波谷时候的忽略开关
            $peaknumberarray = array(); //波峰时间点数组
            $bottomnumberarray = array(); //波谷时间点数组
            $coordate = array(); //全部的y/z坐标数据
            $peakinflexions = array(); //全部的波峰拐点
            $bottominflexions = array(); //全部的波谷拐点
            $tcol = 0; //时间点列号
            $cyclenumber = 0; //周期数
            $threshold = $linenumber / $cycles; //周期阈值
        } elseif ($_POST['step'] == "2") {
            $alllines = array(); //所有行的数组
        }

        $i = 0;
        $ifminus = false;
        //文件循环开始
        while (!feof($file)) {
            $line = fgets($file);
            $IndexArray = explode("\t", $line);
            //开始记录主运动轴的数据
            if ($i > 2) {
                $coordate[] = $IndexArray[$zcol];
                if($i == 2 && strpos($IndexArray[$zvcol], "-") !== false){$ifminus = true;}
            }
            //第一步判读开始
            if ($_POST['step'] == "1") {
                if ($i > $stn) {
                    //判断是否启动
                    if (!$start && $cyclenumber == 0 && strpos($IndexArray[$zvcol], "-") === false) {
                        $start = true;
                    }
                    //开始启动
                    if ($start) {
                        //时间向前移动一个记录点
                        $timer1++;
                        $timer2++;
                        //开始第一个波峰
                        if (empty($peak) && empty($bottom) && $cyclenumber == 0 && strpos($IndexArray[$zvcol], "-") !== false) {
                            $peak = $IndexArray[$zvcol];
                            $peaknumberarray[] = $i - $ff;
                            $timer2 = 0;
                        }
                        //启动波峰点位错误判断
                        if ($cyclenumber == 0 && !empty($peak) && strpos($IndexArray[$zvcol], "-") === false && $timer2 <= $threshold * $startmod) {
                            $start = false;
                            $peak = "";
                            unset($peaknumberarray);
                            $timer1 = $timer2 = 0;
                        }
                        //由波峰开始找波谷
                        if (!empty($peak) && empty($bottom) && $cyclenumber < 8) {
                            if (strpos($IndexArray[$zvcol], "-") === false) {
                                //判断这个波谷是否有效
                                if ($cyclenumber > 0) {
                                    if ($peakcut == false) {
                                        if ($timer1 > $threshold) {
                                            $bottom = $IndexArray[$zvcol];
                                            $bottomnumberarray[] = $i - $ff;
                                            $peak = "";
                                            $timer1 = 0;
                                            $peakcut = false;
                                        } else { $peakcut = true;}
                                    }
                                } else {
                                    //第一个波谷判断
                                    if ($timer2 > ($threshold * $startmod)) {
                                        $bottom = $IndexArray[$zvcol];
                                        $bottomnumberarray[] = $i - $ff;
                                        $peak = "";
                                        $timer1 = 0;
                                        $peakcut = false;
                                    } else {
                                        $peakcut = true;
                                    }
                                }
                            } else {
                                if ($peakcut) {$peakcut = false;}
                            }
                        }

                        //由波谷开始找波峰
                        if (empty($peak) && !empty($bottom) && $cyclenumber < 8) {
                            if (strpos($IndexArray[$zvcol], "-") !== false) {
                                //判断这个波峰是否有效
                                if ($bottomcut == false) {
                                    if ($timer2 > $threshold) {
                                        $peak = $IndexArray[$zvcol];
                                        $peaknumberarray[] = $i - $ff;
                                        $bottom = "";
                                        $timer2 = 0;
                                        $bottomcut = false;
                                        //计数一个完整周期
                                        $cyclenumber++;
                                    } else {
                                        $bottomcut = true;
                                    }
                                }
                            } else {
                                if ($bottomcut) {$bottomcut = false;}
                            }
                        }
                    }
                }
            } elseif ($_POST['step'] == "2") {
                $alllines[] = $IndexArray;
            }
            //记录所有的拐点
            if ($ifminus && strpos($IndexArray[$zvcol], "-") !== false) {
                $peakinflexions[] = $i - $ff;
                $ifminus = false;
            }
            if (!$ifminus && strpos($IndexArray[$zvcol], "-") === false) {
                $bottominflexions[] = $i - $ff;
                $ifminus = true;
            }
            $i++;
        }
        if ($_POST['step'] == "1") {
            //开始显示所有找到的拐点
            echo "<p><span class=\"bigsize\">Crest and Trough Check:</span></p>";
            $peakresult = $bottomresult = "";
            $k = 0;
            //是否有现有数据的波峰
            if (!empty($_POST['peaknumberarray'])) {$peaknumberarray = explode(",", $_POST['peaknumberarray']);}
            //开始显示波峰
            echo "Crests: ";
            foreach ($peaknumberarray as $peaktimenode) {
                echo "<select name=\"peaks$k\">\n";
                foreach ($peakinflexions as $peaksingle) {
                    echo "<option value=\"$peaksingle\"";
                    if ($peaksingle == $peaktimenode) {echo " selected";}
                    echo ">$peaksingle</option>\n";
                }
                echo "</select>\n";
                $k++;
            }
            $k = 0;
            //是否有现有数据的波谷
            if (!empty($_POST['bottomnumberarray'])) {
                $bottomnumberarray = explode(",", $_POST['bottomnumberarray']);
            }
            //开始显示波谷
            echo "<br><br>Troughs: ";
            foreach ($bottomnumberarray as $bottomtimenode) {
                echo "<select name=\"bottom$k\">\n";
                foreach ($bottominflexions as $bottomsingle) {
                    echo "<option value=\"$bottomsingle\"";
                    if ($bottomsingle == $bottomtimenode) {echo " selected";}
                    echo ">$bottomsingle</option>\n";
                }
                echo "</select>\n";
                $k++;
            }
            echo "<br><br><input type=\"submit\" name=\"fsubmit\" value=\"Get the result\"></form>";
        } elseif ($_POST['step'] == "2") {
            $includecolunms = array(1, 2, 3, 7, 8, 9, 13, 14, 15, 19, 20, 21, 25, 27, 26, 31, 32, 33); //需要统计的数据
            $peaknumberarray = array($_POST['peaks0'], $_POST['peaks1'], $_POST['peaks2'], $_POST['peaks3'], $_POST['peaks4'], $_POST['peaks5'], $_POST['peaks6'], $_POST['peaks7'], $_POST['peaks8']);
            $bottomnumberarray = array($_POST['bottom0'], $_POST['bottom1'], $_POST['bottom2'], $_POST['bottom3'], $_POST['bottom4'], $_POST['bottom5'], $_POST['bottom6'], $_POST['bottom7']);
            //生成数据
            $results = analyzedata($ff, $zcol, $alllines, $includecolunms, $peaknumberarray, $bottomnumberarray);
            //生成报告
            echo GenerateReport(explode("\n", trim($results)));
            //写入文件
            file_put_contents("Data/" . $testertype . "/$testername/$testername.$am.results.txt", trim($results));
            echo "<br><b>Analysis Result Export:</b> <a target=\"_blank\" href=\"Data/" . $testertype . "/$testername/$testername.$am.results.txt\">$testername.$am.results.txt</a><br><br>";
        }
        //加载图表
        $chartname = (explode("|", $ammethods[$methodnumber]))[0];
        $src = generatechart($linenumber, $chartname, $testername, $coordate, $peaknumberarray, $bottomnumberarray);
        echo "<div id=\"chart\"><img src=\"data:image/png;base64,$src\" /></div>";
    }
}
?>
</div>
<div class="line">
<form action="analyze.php" method="post" name="upload">
<?php
if(!empty($_POST['tester'])){
    echo "Folder Name: ". $_POST['tester'] . "<br /><br />";
}else{
    echo "Folder Name: ". $_GET['tester'] . "<br /><br />";
}
?>
Select Method:
<select name="method" onchange="setaxis(this)">
<?php
for ($a = 0; $a < count($ammethods); $a++) {
    echo "<option value=\"$a\"";
    if ($_POST['method'] == $a) {echo "selected";}
    $amname = (explode("|", $ammethods[$a]))[0];
    echo ">$amname</option>";
}
?>
</select><br/><br/>
Estimated Number of Cycles: <input type="text" size="2" name="enc" value="<?php if (!empty($_POST['enc'])) {echo $_POST['enc'];} else {echo "15";}?>"><br><br>
Start Time Node: <input type="text" size="2" name="stn" value="<?php if (!empty($_POST['stn'])) {echo $_POST['stn'];} else {echo "50";}?>"><br><br>
Fallback Frames: <input type="text" size="2" name="ff" value="<?php if (!empty($_POST['ff'])) {echo $_POST['ff'];} else {echo "0";}?>"><br><br>
Select Axis:
<select id="axis" name="axis">
<option value="z" <?php if ($_POST['axis'] == "z") {echo "selected";}?>>Z-Axis</option>
<option value="y" <?php if ($_POST['axis'] == "y") {echo "selected";}?>>Y-Axis</option>
<option value="x" <?php if ($_POST['axis'] == "x") {echo "selected";}?>>X-Axis</option>
</select><br/><br/>
<input type="hidden" name="step" value="1">
<?php
if(!empty($_POST['tester'])){
    echo "<input type=\"hidden\" name=\"tester\" value=\"". $_POST['tester'] . "\">";
    echo "<input type=\"hidden\" name=\"type\" value=\"". $_POST['type'] . "\">";
}else{
    echo "<input type=\"hidden\" name=\"tester\" value=\"". $_GET['tester'] . "\">";
    echo "<input type=\"hidden\" name=\"type\" value=\"". $_GET['type'] . "\">";
}
?>
<input type="submit" name="submit" value="submit">
</form>
</body>

<script>
function setaxis(obj){
  var val = obj.value;
  if(val == '0' || val == '1' || val == '2'){
    document.getElementById("axis").options[0].selected = true;
  }else{
    document.getElementById("axis").options[1].selected = true;
  }
}
</script>

</html>

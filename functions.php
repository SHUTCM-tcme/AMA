<?php
function SelectTester($dir, $Tester)
{
    $result = "";
    $arraytemp = array();
    $handle = opendir($dir);
    while ($file = readdir($handle)) {
        if ($file !== '..' && $file !== '.') {
            $arraytemp[] = $file;
        }
    }
    array_multisort($arraytemp);
    foreach ($arraytemp as $singletester) {
        $result .= "<option value=\"$singletester\"";
        if ($Tester == $singletester) {$result .= "selected";}
        $result .= ">$singletester</option>";
    }
    return $result;
}

function GenerateReport($data)
{
    $result = "<table><tr><td class=\"trbottom1\">Selected Axis and Fallback Frames</td></tr>\n";
    for ($i = 0; $i < count($data); $i++) {
        if (empty(trim($data[$i]))) {
            if ($i + 1 < count($data)) {
                if (strpos($data[$i + 1], "Inflection point type") !== false) {
                    $result .= "</table><br><br>\n<table><tr><td class=\"trbottom1\" colspan=\"10\">Table 1. Frame Point of Crests and Troughs</td></tr>\n";
                } elseif (strpos($data[$i + 1], "Crest Points") !== false) {
                    $result .= "</table><br><br>\n<table><tr><td class=\"trbottom1\" colspan=\"4\">Table 2. 3D coordinates of Crests</td></tr>\n";
                } elseif (strpos($data[$i + 1], "Trough Points") !== false) {
                    $result .= "</table><br><br>\n<table><tr><td class=\"trbottom1\" colspan=\"4\">Table 3. 3D coordinates of Troughs</td></tr>\n";
                } elseif (strpos($data[$i + 1], "Parameters") !== false) {
                    $result .= "</table><br><br>\n<table><tr><td class=\"trbottom1\" colspan=\"5\">Table 4. Technical parameters</td></tr>\n";
                } elseif (strpos($data[$i + 1], "T1 Variance") !== false) {
                    $result .= "</table><br><br>\n<table><tr><td class=\"trbottom1\" colspan=\"10\">Table 5. Motor coordination parameter</td></tr>\n";
                }
            }
        } else {
            $linearrary = explode("\t", $data[$i]);
            if (strpos($data[$i], "Inflection point type") !== false || strpos($data[$i], "Crest Points") !== false || strpos($data[$i], "Trough Points") !== false || strpos($data[$i], "Parameters") !== false || strpos($data[$i], "T1 Variance") !== false) {
                $result .= "<tr class=\"trbottom2\">";
            } else {
                $result .= "<tr>";
            }
            foreach ($linearrary as $col) {$result .= "<td>$col</td>";}
            $result .= "</tr>";
        }
    }
    $result .= "</table>";
    return $result;
}

function getVariance($arr)
{
    $num_of_elements = count($arr);
    $variance = 0.0;
    // calculating mean using array_sum() method
    $average = array_sum($arr) / $num_of_elements;
    foreach ($arr as $i) {
        // sum of squares of differences between
        // all numbers and means.
        $variance += pow(($i - $average), 2);
    }
    return (float) sqrt($variance / $num_of_elements);
}

function getMeanVariance($arr)
{
    $num_of_elements = count($arr);
    $variance = 0.0;
    // calculating mean using array_sum() method
    $average = array_sum($arr) / $num_of_elements;
    foreach ($arr as $i) {
        // sum of squares of differences between
        // all numbers and means.
        $variance += pow(($i - $average), 2);
    }
    return round($average, 4) . "±" . round(sqrt($variance / $num_of_elements), 4);
}

function fileShow($dir)
{
    if (empty($dir)) {$dir = "Teachers";}
    $ammethods = array("Mild reinforcing-attenuating of lifting-thrusting|tp", "Reinforcing of lifting-thrusting|tb", "Attenuating of lifting-thrusting|tx", "Mild reinforcing-attenuating of twirling|np", "Reinforcing of twirling|nb", "Attenuating of twirling|nx");
    $handle = opendir("Data/$dir");
    $i = 0;
    $files = array();

    while ($file = readdir($handle)) {
        if ($file !== '..' && $file !== '.') {
            $files[$i]["time"] = date("Y-m-d H:i:s", filemtime("Data/$dir/" . $file));
            $files[$i]["name"] = $file;
            $i++;
        }
    }
    closedir($handle);

    if ($i > 0) {
        foreach ($files as $k => $v) {
            $time[$k] = $v['time'];
            $name[$k] = $v['name'];
        }

        array_multisort($time, SORT_ASC, SORT_STRING, $files);
        echo "<div class=\"main\">";
        foreach ($files as $filesingle) {
            echo "<li>" . $filesingle["name"];
            foreach ($ammethods as $amsingle) {
                $aminfo = explode("|", $amsingle);
                $filenameshow = "Data/$dir/" . $filesingle["name"] . "/" . $filesingle["name"] . "." . $aminfo[1] . ".results.txt";
                if (file_exists($filenameshow)) {
                    echo " <a target=\"_blank\" href=\"showreport.php?type=$dir&name=" . $filesingle["name"] . "&am=" . $aminfo[1] . "\">$aminfo[0]</a>";
                }
            }
            echo "</li>";
        }
        echo "</div>";
    }
}

function Showtester($type, $conn, $page, $keyword)
{
    $rec_limit = 20;//每页数量
    $ammethods = array("MLT|tp", "RLT|tb", "ALT|tx", "MT|np", "RT|nb", "AT|nx");
    
    //添加搜索框
    echo "<form action=\"index.php\" method=\"post\" name=\"search\" id=\"search\">\n";
    echo "<div id=\"search\"><input type=\"text\" size=\"12\" name=\"keyword\" value=\"$keyword\">\n";
    echo "  <input type=\"submit\" name=\"searchsubmit\" value=\"Search\">\n";
    echo "</div></form>\n";

    //添加列表
    echo "<form action=\"edit.php\" method=\"post\" name=\"list\" id=\"list\">\n";
    echo "<table><tr id=\"listhead\"><td><input type=\"checkbox\" value=\"''\" name=\"selectall\" onclick=\"bunchchk(this)\" /></td><td>ID</td><td>Name</td><td>Type</td><td>Age</td><td>Gender</td><td>Practice Time</td><td>Operation</td></tr>\n";
    //查询
    $sqlkeywords = $sqlandkeywords = "";
    if(!empty($keyword)){
        $sqlkeywords = " WHERE name LIKE '%$keyword%'";
        $sqlandkeywords = " AND name LIKE '%$keyword%'";
    }
    //sql语句
    if (empty($type)) {
        $sql = "SELECT COUNT(id) FROM Tester$sqlkeywords";
    } elseif ($type == "Teachers") {
        $sql = "SELECT COUNT(id) FROM Tester WHERE type=1$sqlandkeywords";
    } elseif ($type == "Students") {
        $sql = "SELECT COUNT(id) FROM Tester WHERE type=2$sqlandkeywords";
    }
    //先获得所有的记录数
    $rec_count = $allpage = 0;
    $result = mysqli_query($conn, $sql);
    if($result) {
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        $rec_count = $row[0];
        $allpage = ceil($rec_count / $rec_limit);
    }else{
        echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($conn);
    }
    //获得页码
    if(!empty($page)) {
        $pageindex = $page - 1;
        $offset = $rec_limit * $pageindex ;
    }else {
        $page = 1;
        $offset = 0;
    }
    
    //开始读取详细记录
    if (empty($type)) {
        $sql = "SELECT * FROM Tester$sqlkeywords ORDER BY id DESC LIMIT $offset, $rec_limit";
    } elseif ($type == "Teachers") {
        $sql = "SELECT * FROM Tester WHERE type=1$sqlandkeywords ORDER BY id DESC LIMIT $offset, $rec_limit";
    } elseif ($type == "Students") {
        $sql = "SELECT * FROM Tester WHERE type=2$sqlandkeywords ORDER BY id DESC LIMIT $offset, $rec_limit";
    }

    $result = mysqli_query($conn, $sql);
    //开始循环显示
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr class=\"tester\"><td><input type=\"checkbox\" class=\"checkb2\" value=\"" . $row["id"] . "\" name=\"multi_id[]\" /></td>\n";
            echo "<td>" . $row["id"] . "</td>\n";
            echo "<td>" . $row["name"] . "</td>\n";
            $testtype = "Teachers";
            if ($row["type"] == 1) {
                echo "<td>Teacher</td>\n";
            } else {
                echo "<td>Student</td>\n";
                $testtype = "Students";
            }
            echo "<td>" . $row["age"] . "</td>\n";
            echo "<td>" . $row["gender"] . "</td>\n";
            echo "<td>" . $row["practicetime"] . $row["unit"] . "</td>\n";
            echo "<td><a href=\"edit.php?action=edit&testerid=" . $row["id"] . "\">Edit</a> <a href=\"edit.php?action=addrecord&testerid=" . $row["id"] . "\">Add new record</a></td>\n</td>\n";
            //查询记录
            $resql = "SELECT * FROM Folder WHERE testerid=" . $row["id"] . " ORDER BY operationdate DESC";
            $reresult = mysqli_query($conn, $resql);
            if ($reresult->num_rows > 0) {
                echo "</tr>\n";
                while ($rerow = $reresult->fetch_assoc()) {
                    echo "<tr><td></td><td colspan=\"6\">Record on " . $rerow["operationdate"] . " in Folder \"" . $rerow["foldername"] . "\"";
                    if (!empty($rerow["comment"])) {echo " (" . $rerow["comment"] . ")";}
                    //开始读取测试数据文件
                    $opdir = "Data/$testtype/" . $rerow["foldername"];
                    if(is_dir($opdir)){
                        foreach ($ammethods as $amsingle) {
                            $aminfo = explode("|", $amsingle);
                            $filenameshow = "$opdir/" . $rerow["foldername"] . "." . $aminfo[1] . ".results.txt";
                            if (file_exists($filenameshow)) { echo " <a target=\"_blank\" href=\"showreport.php?type=$testtype&name=" . $rerow["foldername"] . "&am=" . $aminfo[1] . "\">$aminfo[0]</a>"; }
                        }
                    }
                    echo "</td>\n<td><a href=\"analyze.php?type=$testtype&tester=" . $rerow["foldername"] . "\">Analysis</a> <a href=\"edit.php?action=editrecord&id=" . $rerow["id"] . "\">Edit</a> <a onclick=\"javascript:return del()\" href=\"edit.php?action=delrecord&id=" . $rerow["id"] . "\">Delete</a></td>";
                }
            }
            echo "</tr>\n";
        }
    } else {
        echo "<tr><td colspan=\"8\">0 result</td></tr>\n";
    }
    echo "</table>\n";

    //显示分页导航
    echo "<a href = \"index.php?type=$type&page=1\">First Page</a>";
    if( $page > 1 && $page < $allpage ) {
        $lastpage = $page - 1;
        $nextpage = $page + 1;
        echo " | <a href = \"index.php?type=$type&page=$lastpage\">Page Up</a> | <a href = \"index.php?type=$type&page=$nextpage\">Page Down</a>";
    } elseif ( $page == 1 && $allpage > 1) {
        echo " | <a href = \"index.php?type=$type&page=2\">Page Down</a>";
    } elseif ( $page == $allpage && $allpage > 1) {
        $lastpage = $page - 1;
        echo " | <a href = \"index.php?type=$type&page=$lastpage\">Page Up</a>";
    }
    if ($allpage > 1){
        echo " | <a href = \"index.php?type=$type&page=$allpage\">Last Page</a>";
    }
    echo " |  Toal $rec_count Testers, $allpage Pages. $rec_limit testers per page.";

    //显示按钮
    echo "<br /><input onclick=\"javascript:return del()\" type=\"submit\" name=\"batchdel\" value=\"Delete Testers\">  <a href=\"edit.php?action=new\"><input type=\"button\" name=\"newtester\" value=\"Add New Tester\"></a>\n";
    echo "<input type=\"hidden\" name=\"type\" value=\"$type\">\n<input type=\"hidden\" name=\"action\" value=\"del\"></form>\n";
}

//生成结果($cnumber: 用哪个轴; $alllines: 所有的数据; $includecolunms: 需要统计的列; $peaknumberarray: 波峰数组; $bottomnumberarray: 波谷数组)
function analyzedata($ff, $cnumber, $alllines, $includecolunms, $peaknumberarray, $bottomnumberarray)
{
    //第一部分结果：拐点数据和相关的三维坐标
    $results = $peaknodes = $peak3d = $bottomnodes = $bottom3d = "";
    $peak2dvaluearray = array();
    $bottom2dvaluearray = array();
    $t1cvaluearray = array();
    $t2cvaluearrary = array();
    $peak3darrary = array();
    $bottom3darray = array();

    for ($j = 0; $j < count($peaknumberarray); $j++) {
        $n = $j + 1;
        $peak3d .= "Point$n\t" . ($alllines[$peaknumberarray[$j]][13] * 100) . "\t" . ($alllines[$peaknumberarray[$j]][14] * 100) . "\t" . ($alllines[$peaknumberarray[$j]][15] * 100) . "\n";
        $peak3darrary[] = ($alllines[$peaknumberarray[$j]][13] * 100) . "," . ($alllines[$peaknumberarray[$j]][14] * 100) . "," . ($alllines[$peaknumberarray[$j]][15] * 100);
        $peak2dvaluearray[] = ($alllines[$peaknumberarray[$j]][$cnumber] * 100);
        if ($j < count($peaknumberarray) - 1) {
            $peaknodes .= $peaknumberarray[$j] . "\t";
            $bottom3d .= "Point$n\t" . ($alllines[$bottomnumberarray[$j]][13] * 100) . "\t" . ($alllines[$bottomnumberarray[$j]][14] * 100) . "\t" . ($alllines[$bottomnumberarray[$j]][15] * 100) . "\n";
            $bottom3darray[] = ($alllines[$bottomnumberarray[$j]][13] * 100) . "," . ($alllines[$bottomnumberarray[$j]][14] * 100) . "," . ($alllines[$bottomnumberarray[$j]][15] * 100);
            $bottom2dvaluearray[] = ($alllines[$bottomnumberarray[$j]][$cnumber] * 100);
            if ($j < count($peaknumberarray) - 2) {
                $bottomnodes .= $bottomnumberarray[$j] . "\t";
            } else {
                $bottomnodes .= $bottomnumberarray[$j];
            }
        } else {
            $peaknodes .= $peaknumberarray[$j];
            $bottomnodes .= "\t";
        }
    }

    if ($cnumber == 15) {
        $results = "Column: " . $cnumber . "\nAxis: Z\nFallback Frames: $ff\n\n";
    } elseif ($cnumber == 14) {
        $results = "Column: " . $cnumber . "\nAxis: Y\nFallback Frames: $ff\n\n";
    } else {
        $results = "Column: " . $cnumber . "\nAxis: X\nFallback Frames: $ff\n\n";
    }

    $results .= "Inflection point type\tPoint1\tPoint2\tPoint3\tPoint4\tPoint5\tPoint6\tPoint7\tPoint8\tPoint9\n";
    $results .= "Crests\t" . $peaknodes . "\nTroughs\t" . $bottomnodes . "\n\n"; //生成拐点记录
    $results .= "Crest Points\tCoordinate (x)\tCoordinate (y)\tCoordinate (z)\n$peak3d\n"; //波峰3维坐标
    $results .= "Trough Points\tCoordinate (x)\tCoordinate (y)\tCoordinate (z)\n$bottom3d\n"; //波谷3维坐标

    //第二部分：技术参数
    $t1c = $t2c = 0;
    $results .= "Parameters\tT1 - Amplitude\tT1 - Velocity\tT2 - Amplitude\tT2 - Velocity\n";
    foreach ($includecolunms as $colname) {
        $temp = $alllines[0][$colname];
        $results .= "$temp\t";
        //T1
        $t1a = 0;
        $t1v = 0;
        for ($c = 0; $c < 8; $c++) {
            $t1a += abs($alllines[$peaknumberarray[$c]][$colname] - $alllines[$bottomnumberarray[$c]][$colname]);
            $t1c += $alllines[$bottomnumberarray[$c]][0] - $alllines[$peaknumberarray[$c]][0];
            $t1cvaluearray[] = $alllines[$bottomnumberarray[$c]][0] - $alllines[$peaknumberarray[$c]][0];
            $t1v += (abs($alllines[$peaknumberarray[$c]][$colname] - $alllines[$bottomnumberarray[$c]][$colname])) / ($alllines[$bottomnumberarray[$c]][0] - $alllines[$peaknumberarray[$c]][0]);
        }
        $t1a = round((($t1a / 8) * 100), 4);
        $t1v = round((($t1v / 8) * 100), 4);
        $t1c = round(($t1c / 8), 4);
        $results .= "$t1a\t$t1v\t";
        //T2
        $t2a = 0;
        $t2v = 0;
        for ($c = 0; $c < 8; $c++) {
            $t2a += abs($alllines[$bottomnumberarray[$c]][$colname] - $alllines[$peaknumberarray[$c + 1]][$colname]);
            $t2c += $alllines[$peaknumberarray[$c + 1]][0] - $alllines[$bottomnumberarray[$c]][0];
            $t2cvaluearrary[] = $alllines[$peaknumberarray[$c + 1]][0] - $alllines[$bottomnumberarray[$c]][0];
            $t2v += (abs($alllines[$bottomnumberarray[$c]][$colname] - $alllines[$peaknumberarray[$c + 1]][$colname])) / ($alllines[$peaknumberarray[$c + 1]][0] - $alllines[$bottomnumberarray[$c]][0]);
        }
        $t2a = round((($t2a / 8) * 100), 4);
        $t2v = round((($t2v / 8) * 100), 4);
        $t2c = round(($t2c / 8), 4);
        $results .= "$t2a\t$t2v\n";
    }
    //第三部分：时间和协调参数
    $results .= "\nT1 Time\tT2 Time\tT1 Variance\tT2 Variance\tCrest Variance\tTrough Variance\tCrest Radius(CR)\tTrough Radius(TR)\tCR Variance\tTR Variance\n" . $t1c . "\t" . $t2c . "\t" . round(getVariance($t1cvaluearray), 4) . "\t" . round(getVariance($t2cvaluearrary), 4) . "\t" . round(getVariance($peak2dvaluearray), 4) . "\t" . round(getVariance($bottom2dvaluearray), 4) . "\t" . max(getsemidiameter($peak3darrary)) . "\t" . max(getsemidiameter($bottom3darray)) . "\t" . round(getVariance(getsemidiameter($peak3darrary)), 4) . "\t" . round(getVariance(getsemidiameter($bottom3darray)), 4);
    return $results;
}

function getsemidiameter($pointarrary)
{
    $xall = $yall = $zall = 0;
    foreach ($pointarrary as $point) {
        $axisarray = explode(",", $point);
        $xall += (float) $axisarray[0];
        $yall += (float) $axisarray[1];
        $zall += (float) $axisarray[2];
    }
    //球心
    $cx = $xall / count($pointarrary);
    $cy = $yall / count($pointarrary);
    $cz = $zall / count($pointarrary);
    //开始计算半径
    $results = array();
    foreach ($pointarrary as $point) {
        $axisarray = explode(",", $point);
        $xd = (float) $axisarray[0] - (float) $cx;
        $yd = (float) $axisarray[1] - (float) $cy;
        $zd = (float) $axisarray[2] - (float) $cz;
        $tempsd = round(sqrt(pow($xd, 2) + pow($yd, 2) + pow($zd, 2)), 4);
        $results[] = $tempsd;
    }
    return $results;
}

function generatechart($linenumber, $chartname, $testername, $coordate, $peaknumberarray, $bottomnumberarray)
{
    //设置大小
    $graph = new Graph(2000, 500);
    //设置边距，空余四角边距（左右上下）
    $graph->img->SetMargin(0, 0, 0, 0);
    $graph->SetScale('intlin', 0, 0, 0, $linenumber);
    $interval = 20;
    if ($linenumber > 1000) {$interval = 30;}
    if ($linenumber > 2000) {$interval = 40;}
    $graph->xaxis->scale->ticks->Set($interval);
    //设置统计图标题
    $graph->title->Set($chartname);
    $graph->subtitle->Set('Tester: ' . $testername);
    $graph->xaxis->title->Set('Time');
    $graph->yaxis->title->Set('Coordinate');
    $graph->title->SetFont(FF_FONT1, FS_BOLD);
    $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
    $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
    //创建折线图
    $lineplot = new LinePlot($coordate);
    //填区域
    //$lineplot->AddArea(246, 269, LP_AREA_FILLED, "red");
    for ($ii = 0; $ii < count($bottomnumberarray); $ii++) {
        $lineplot->AddArea($peaknumberarray[$ii] - 3, $bottomnumberarray[$ii] - 3, LP_AREA_FILLED, "red");
        if (($ii + 1) <= count($bottomnumberarray)) {$lineplot->AddArea($bottomnumberarray[$ii] - 3, $peaknumberarray[$ii + 1] - 3, LP_AREA_FILLED, "green");}
    }
    //折线填入图
    $graph->Add($lineplot);
    //显示折线图
    $lineimg = $graph->Stroke(_IMG_HANDLER);
    ob_start();
    imagepng($lineimg);
    $imageData = ob_get_contents();
    $encodeimgagedata = base64_encode($imageData);
    ob_end_clean();
    return $encodeimgagedata;
}

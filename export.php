<?php
require_once "header.php";
require_once "functions.php";
header("Cache-control: private");
$ammethods  = array("Mild reinforcing-attenuating of lifting-thrusting|tp", "Reinforcing of lifting-thrusting|tb", "Attenuating of lifting-thrusting|tx", "Mild reinforcing-attenuating of twirling|np", "Reinforcing of twirling|nb", "Attenuating of twirling|nx");
$parameters = array("thumb base joint right X|30", "thumb base joint right Y|31", "thumb base joint right Z|32", "thumb end joint right X|33", "thumb end joint right Y|34", "thumb end joint right Z|35", "thumb tip right X|36", "thumb tip right Y|37", "thumb tip right Z|38", "forefinger base joint right X|39", "forefinger base joint right Y|40", "forefinger base joint right Z|41", "forefinger middle joint right X|42", "forefinger middle joint right Y|43", "forefinger middle joint right Z|44", "forefinger tip right X|45", "forefinger tip right Y|46", "forefinger tip right Z|47", "Motor coordination|50", "All|All");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Parameter"] == "Motor coordination|50") {
        $result = "Name\tGroup\t\nT1 Time\tT2 Time\tT1 Variance\tT2 Variance\tCrest Variance\tTrough Variance\tCrest Radius(CR)\tTrough Radius(TR)\tCR Variance\tTR Variance\n";
    } elseif ($_POST["Parameter"] == "All|All") {
        $result = "Parameters\tT1 - Amplitude\tT1 - Velocity\tT2 - Amplitude\tT2 - Velocity\n";
    } else {
        $result = "Name\tGroup\tT1 - Amplitude\tT1 - Velocity\tT2 - Amplitude\tT2 - Velocity\n";
    }
    //操作目录
    $handle = opendir("Data/" . $_POST["testertype"]);
    //总的数组
    $allresult = array();
    //导出的参数类型
    $oppoint = explode("|", $_POST["Parameter"]);
    //开始读取测试人员
    while ($file = readdir($handle)) {
        if ($file !== '..' && $file !== '.') {
            $reportfile = "Data/" . $_POST["testertype"] . "/" . $file . "/" . "$file." . $_POST["method"] . ".results.txt";
            if (file_exists($reportfile)) {
                $handlefile = @fopen($reportfile, 'r');
                $hi         = 0;
                while (!feof($handlefile)) {
                    $line = fgets($handlefile);
                    if ($oppoint[1] !== "All") {
                        if ($hi == $oppoint[1]) {
                            if ($_POST["Parameter"] == "Motor coordination|50") {
                                $result .= $file . "\t" . $_POST["testertype"] . "\t" . trim($line) . "\n";
                            } else {
                                $result .= str_replace($oppoint[0], $file . "\t" . $_POST["testertype"], trim($line)) . "\n";
                            }
                        }
                    } else {
                        for ($pi = 0; $pi < count($parameters) - 2; $pi++) {
                            $oppoints = explode("|", $parameters[$pi]);
                            if ($hi == $oppoints[1]) {
                                $linearray           = explode("\t", trim($line));
                                $allresult[$pi][0][] = $linearray[1];
                                $allresult[$pi][1][] = $linearray[2];
                                $allresult[$pi][2][] = $linearray[3];
                                $allresult[$pi][3][] = $linearray[4];
                            }
                        }
                    }
                    $hi++;
                }
                fclose($handlefile);
            }
        }
    }
    if ($oppoint[1] == "All") {
        for ($pi = 0; $pi < count($parameters) - 2; $pi++) {
            $oppoints = explode("|", $parameters[$pi]);
            $result .= $oppoints[0] . "\t";
            for ($ppi = 0; $ppi < 4; $ppi++) {
                $result .= getMeanVariance($allresult[$pi][$ppi]);
                if ($ppi == 3) {$result .= "\n";} else { $result .= "\t";}
            }
        }
    }
    //生成数据表
    file_put_contents("Data/Exports/dataset.txt", trim($result));
    echo "<br><b>Download Dataset:</b> <a target=\"_blank\" href=\"Data/Exports/dataset.txt\">Dataset</a><br><br>";
    closedir($handle);
}
?>
</div>
<div class="line">
<form action="export.php" method="post" name="upload">
Select Tester Type:
<select name="testertype">
<option value="Teachers">Teacher</option>
<option value="Students">Student</option>
</select><br/><br/>
Select Method:
<select name="method">
<?php
for ($i = 0; $i < count($ammethods); $i++) {
    $amname = explode("|", $ammethods[$i]);
    echo "<option value=\"" . $amname[1] . "\">" . $amname[0] . "</option>";
}
?>
</select><br/><br/>
Select Parameter:
<select name="Parameter">
<?php
for ($i = 0; $i < count($parameters); $i++) {
    $singlepara = explode("|", $parameters[$i]);
    echo "<option value=\"" . $parameters[$i] . "\">" . $singlepara[0] . "</option>";
}
?>
</select><br/><br/>
<input type="submit" name="submit" value="submit">
</form>
</body>
</html>

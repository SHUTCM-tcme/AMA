<?php
require_once "header.php";
require_once "functions.php";
require_once "conn.php";

//提交数据
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //保存添加的测试者
    if ($_POST['action'] == "saveaddtester") {
        $name = mysqli_real_escape_string($con, trim($_POST['name']));
        $sql = "insert into Tester(name,age,gender,practicetime,unit,type) values('$name'," . trim($_POST['age']) . ",'" . $_POST['gender'] . "'," . trim($_POST['practicetime']) . ",'" . $_POST['unit'] . "'," . $_POST['type'] . ")";
        $re = mysqli_query($con, $sql);
        if ($re) {
            echo "<script>alert('Tester is added successfully!');window.location.href='index.php'</script>";
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }
    }
    //保存编辑的测试者
    if ($_POST['action'] == "saveedittester" && !empty($_POST['testerid'])) {
        $name = mysqli_real_escape_string($con, trim($_POST['name']));
        $sql = "update Tester set name='$name', age=" . trim($_POST['age']) . ", gender='" . $_POST['gender'] . "', practicetime=" . trim($_POST['practicetime']) . ", unit='" . $_POST['unit'] . "', type=". $_POST['type'] . " where id=" . $_POST['testerid'];
        $re = mysqli_query($con, $sql);
        if ($re) {
            echo "<script>alert('Tester is updated successfully!');window.location.href='edit.php?action=edit&testerid=" . $_POST['testerid'] . "'</script>";
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }
    }
    //删除测试者
    if ($_POST['action'] == "del" && !empty($_POST['multi_id'])) {
        $ids = implode(",", $_POST['multi_id']);
        $sql = "delete from Tester where id in ($ids)";
        $re = mysqli_query($con, $sql);
        if ($re) {
            $sql = "delete from Folder where testerid in ($ids)";
            $re = mysqli_query($con, $sql);
            if ($re) {
                echo "<script>alert('Testers are deleted successfully!');window.location.href='index.php?type=" . $_POST['type'] . "'</script>";
            } else {
                "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
            }
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }
    }
    //保持添加的记录
    if ($_POST['action'] == "saveaddrecord"  && !empty($_POST['testerid'])) {
        $foldername = mysqli_real_escape_string($con, trim($_POST['foldername']));
        $comment = "";
        if(!empty(trim($_POST['comment']))) {$comment = mysqli_real_escape_string($con, trim($_POST['comment']));}
        $sql = "insert into Folder(testerid,foldername,operationdate,comment) values(" . $_POST['testerid'] . ",'$foldername','" . $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'] . "','$comment')";
        $re = mysqli_query($con, $sql);
        if ($re) {
            $id = mysqli_insert_id($con);
            echo "<script>alert('Operation record is added successfully!');window.location.href='edit.php?action=editrecord&id=$id'</script>";
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }
    }
    //保存编辑的测试者
    if ($_POST['action'] == "saveeditrecord" && !empty($_POST['id'])) {
        $foldername = mysqli_real_escape_string($con, trim($_POST['foldername']));
        $comment = "";
        if(!empty(trim($_POST['comment']))) {$comment = mysqli_real_escape_string($con, trim($_POST['comment']));}
        $sql = "update Folder set foldername='$foldername', comment='$comment', operationdate='" . $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'] . "' where id=" . $_POST['id'];
        $re = mysqli_query($con, $sql);
        if ($re) {
            echo "<script>alert('Operation record is updated successfully!');window.location.href='edit.php?action=editrecord&id=" . $_POST['id'] . "'</script>";
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }
    }
}

//插入html代码
echo "</div>\n<div class=\"line\">\n";
?>
<SCRIPT LANGUAGE=javascript>
function validateForm()
{
    //验证姓名非空
    var x = document.getElementById("name").value;
    var number = new RegExp("^[\u4E00-\u9FA5A-Za-z0-9_]+$");
    if (x == null || x == "" || !number.test(x)){
        alert("Please input a valid name of the tester!");
        return false;
    }
    //验证年龄
    x = document.getElementById("age").value;
    number = new RegExp("^[0-9]*$");
    if (x == null || x == "" || !number.test(x)){
        alert("Please input a valid age of the tester!");
        return false;
    }
    //验证练习时间
    x = document.getElementById("practicetime").value;
    number = new RegExp("^[0-9]+(.[0-9]{1,1})?$");
    if (x == null || x == "" || !number.test(x)){
        alert("Please input a valid practice time of the tester!");
        return false;
    }
}
function validateRecordForm()
{
    //验证文件夹名
    var x = document.getElementById("foldername").value;
    var number = new RegExp("^[A-Za-z0-9]+$");
    if (x == null || x == "" || !number.test(x.replace(".", ""))){
        alert("Please input a valid folder name!");
        return false;
    }
}
</SCRIPT>

<?php
//获取数据
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //添加测试者
    if ($_GET['action'] == "new") {
        //相关信息框
        echo "<form action=\"edit.php\" method=\"post\" name=\"save\" onsubmit=\"return validateForm()\">";
        echo "Type: <select name=\"type\"><option value=\"2\">Student</option><option value=\"1\">Teacher</option></select><br><br>\n";
        echo "Tester Name: <input type=\"text\" size=\"10\" name=\"name\" id=\"name\" value=\"\"><br><br>\n";
        echo "Age: <input type=\"text\" size=\"2\" name=\"age\" id=\"age\" value=\"\"><br><br>\n";
        echo "Gender: <select name=\"gender\"><option value=\"Male\">Male</option><option value=\"Female\">Female</option></select><br><br>\n";
        echo "Practice Time: <input type=\"text\" size=\"2\" name=\"practicetime\" id=\"practicetime\" value=\"\"> <select name=\"unit\"><option value=\"Days\">Days</option><option value=\"Hours\">Hours</option><option value=\"Months\">Months</option><option value=\"Years\">Years</option></select><br><br>\n";
        echo "<input type=\"hidden\" name=\"action\" value=\"saveaddtester\">\n";
        echo "<input type=\"submit\" name=\"submit\" value=\"submit\">\n";
    }
    //修改测试者
    if ($_GET['action'] == "edit" && !empty($_GET['testerid'])) {
        $sql = "select * from Tester where id=" . $_GET['testerid'];
        $re = mysqli_query($con, $sql);
        if ($re->num_rows > 0) {
            while ($row = $re->fetch_assoc()) {
                echo "<form action=\"edit.php\" method=\"post\" name=\"save\" onsubmit=\"return validateForm()\">";
                if($row["type"] == 1){
                    echo "Type: <select name=\"type\"><option value=\"2\">Student</option><option value=\"1\" selected>Teacher</option></select><br><br>\n";
                }else{
                    echo "Type: <select name=\"type\"><option value=\"2\" selected>Student</option><option value=\"1\">Teacher</option></select><br><br>\n";
                }
                echo "Tester Name: <input type=\"text\" size=\"10\" name=\"name\" id=\"name\" value=\"" . $row["name"] . "\"><br><br>\n";
                echo "Age: <input type=\"text\" size=\"2\" name=\"age\" id=\"age\" value=\"" . $row["age"] . "\"><br><br>\n";
                if($row["gender"] == "Female"){
                    echo "Gender: <select name=\"gender\"><option value=\"Male\">Male</option><option value=\"Female\" selected>Female</option></select><br><br>\n";
                }else{
                    echo "Gender: <select name=\"gender\"><option value=\"Male\" selected>Male</option><option value=\"Female\">Female</option></select><br><br>\n";
                }               
                echo "Practice Time: <input type=\"text\" size=\"2\" name=\"practicetime\" id=\"practicetime\" value=\"" . $row["practicetime"] . "\"> ";
                if($row["unit"] == "Hours"){
                    echo "<select name=\"unit\"><option value=\"Days\">Days</option><option value=\"Hours\" selected>Hours</option><option value=\"Months\">Months</option><option value=\"Years\">Years</option></select><br><br>\n";
                }elseif($row["unit"] == "Months"){
                    echo "<select name=\"unit\"><option value=\"Days\">Days</option><option value=\"Hours\">Hours</option><option value=\"Months\" selected>Months</option><option value=\"Years\">Years</option></select><br><br>\n";
                }elseif($row["unit"] == "Years"){
                    echo "<select name=\"unit\"><option value=\"Days\">Days</option><option value=\"Hours\">Hours</option><option value=\"Months\">Months</option><option value=\"Years\" selected>Years</option></select><br><br>\n";
                }else{
                    echo "<select name=\"unit\"><option value=\"Days\" selected>Days</option><option value=\"Hours\">Hours</option><option value=\"Months\">Months</option><option value=\"Years\">Years</option></select><br><br>\n";
                }
                echo "<input type=\"hidden\" name=\"action\" value=\"saveedittester\">\n";
                echo "<input type=\"hidden\" name=\"testerid\" value=\"" . $_GET['testerid'] . "\">\n";
                echo "<input type=\"submit\" name=\"submit\" value=\"submit\">\n";
            }
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }
    }
    //添加新记录
    if ($_GET['action'] == "addrecord" && !empty($_GET['testerid'])) {
        $sql = "select name from Tester where id=" . $_GET['testerid'];
        $re = mysqli_query($con, $sql);
        if ($re->num_rows > 0) {
            while ($row = $re->fetch_assoc()) {
                echo "<form action=\"edit.php\" method=\"post\" name=\"save\" onsubmit=\"return validateRecordForm()\">";
                echo "Add a new operation record for " . $row["name"] . "<br><br>\n";
                echo "Folder name: <input type=\"text\" size=\"10\" name=\"foldername\" id=\"foldername\" value=\"\"><br><br>\n";
                echo "Operation date: <select name=\"year\">\n";
                for ($i = 2020; $i<2030; $i++) {
                    if (date("Y") == $i) {
                        echo "<option value=\"$i\" selected>$i</option>\n"; 
                    } else {
                        echo "<option value=\"$i\">$i</option>\n"; 
                    } 
                }
                echo "</select> - <select name=\"month\">\n";
                for ($i = 1; $i<13; $i++) { 
                    if (date("n") == $i) {
                        echo "<option value=\"$i\" selected>$i</option>\n";
                    } else {
                        echo "<option value=\"$i\">$i</option>\n"; 
                    }
                }
                echo "</select> - <select name=\"day\">\n";
                for ($i = 1; $i<32; $i++) {
                    if (date("j") == $i){
                        echo "<option value=\"$i\" selected>$i</option>\n";
                    } else {
                        echo "<option value=\"$i\">$i</option>\n"; 
                    }
                }
                echo "</select><br><br>\nComment: <input type=\"text\" size=\"15\" name=\"comment\" id=\"comment\" value=\"\"><br><br>\n";
                echo "<input type=\"hidden\" name=\"action\" value=\"saveaddrecord\">\n";
                echo "<input type=\"hidden\" name=\"testerid\" value=\"" . $_GET['testerid'] . "\">\n";
                echo "<input type=\"submit\" name=\"submit\" value=\"submit\">\n";   
            }
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }      
    }
    //编辑操作记录
    if ($_GET['action'] == "editrecord" && !empty($_GET['id'])) {
        $sql = "select * from Folder where id=" . $_GET['id'];
        $re = mysqli_query($con, $sql);
        if ($re->num_rows > 0) {
            while ($row = $re->fetch_assoc()) {
                $sql = "select name from Tester where id=" . $row["testerid"];
                $namere = mysqli_query($con, $sql);
                if ($namere->num_rows > 0) {
                    while ($namerow = $namere->fetch_assoc()) {
                        echo "<form action=\"edit.php\" method=\"post\" name=\"save\" onsubmit=\"return validateRecordForm()\">";
                        echo "Edit operation record for " . $namerow["name"] . "<br><br>\n";
                        echo "Folder name: <input type=\"text\" size=\"10\" name=\"foldername\" id=\"foldername\" value=\"" . $row["foldername"] . "\"><br><br>\n";
                        $timearray = explode("-", $row["operationdate"]);
                        echo "Operation date: <select name=\"year\">\n";
                        for ($i = 2020; $i<2030; $i++) {
                            if ($timearray[0] == $i) {
                                echo "<option value=\"$i\" selected>$i</option>\n"; 
                            } else {
                                echo "<option value=\"$i\">$i</option>\n"; 
                            } 
                        }
                        echo "</select> - <select name=\"month\">\n";
                        for ($i = 1; $i<13; $i++) { 
                            if ($timearray[1] == $i) {
                                echo "<option value=\"$i\" selected>$i</option>\n";
                            } else {
                                echo "<option value=\"$i\">$i</option>\n"; 
                            }
                        }
                        echo "</select> - <select name=\"day\">\n";
                        for ($i = 1; $i<32; $i++) {
                            if ($timearray[2] == $i){
                                echo "<option value=\"$i\" selected>$i</option>\n";
                            } else {
                                echo "<option value=\"$i\">$i</option>\n"; 
                            }
                        }
                        echo "</select><br><br>\nComment: <input type=\"text\" size=\"15\" name=\"comment\" id=\"comment\" value=\"" . $row["comment"] . "\"><br><br>\n";
                        echo "<input type=\"hidden\" name=\"action\" value=\"saveeditrecord\">\n";
                        echo "<input type=\"hidden\" name=\"id\" value=\"" . $_GET['id'] . "\">\n";
                        echo "<input type=\"submit\" name=\"submit\" value=\"submit\">\n"; 
                    }
                }else{
                    echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
                }  
            }
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }      
    }
    //删除记录
    if ($_GET['action'] == "delrecord" && !empty($_GET['id'])) {
        $sql = "delete from Folder where id=" . $_GET['id'];
        $re = mysqli_query($con, $sql);
        if ($re) {
            echo "<script>alert('Operation record is deleted successfully!');window.location.href='index.php'</script>";
        } else {
            echo "<font color=red><b>Operation failed, detailed information:</b></font><br>" . mysqli_error($con);
        }
    }
}

//插入尾部的html代码
echo "</form>\n</div></body>\n</html>";

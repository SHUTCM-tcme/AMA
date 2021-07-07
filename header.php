<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Acupuncture Manipulation Analysis Ver. 2.0</title>
</head>
<style type="text/css">
* { margin: 0; padding: 0; word-wrap: break-word; }
ul li, .xl li { list-style: none; }

body {text-align: center; background: #FFF; font-size: 14px; font-style: normal; font-family: Arial,Helvetica,sans-seri>
.editbox{
     background: #ffffff;
     border: 2px solid #b7b7b7;
     color: #003366;
     cursor: text;
     height: 40px;
         font-size: 16pt;
         margin:0 auto;
}
a {padding: 10px;}
.bigsize{font-size: 16pt; font-weight: bold;}
.checkb {border: 2px solid #b7b7b7; height: 30px; width: 30px; margin-left: 10px;}
.checkb2 {border: 1px solid #b7b7b7;}
.line {width: 600px; background:#E0E0E0; margin:10px auto; padding: 10px; text-align: left;}
.main {background:#ffffff; margin:10px auto; line-height: 200%; text-align: center;}
.resultline {width:85%; background:#FBFBFF; margin:0 auto; padding: 10px;}
.trbottom1{background: #ffffff; border-bottom-style:solid; border-width:2px; text-align: center; font-weight: bold;}
.trbottom2{border-bottom-style:solid; border-width:1px; font-weight: bold;}
.menu{width:100%; background:#E0E0E0; margin: 0px; padding-top: 10px; padding-bottom: 10px; text-align: center;}
select {background: #ffffff; border: 1px solid #b7b7b7; font-size: 14pt; padding: 5px;}
input {background: #ffffff; border: 1px solid #b7b7b7; font-size: 14pt; padding: 5px;}
table {background: #ffffff; margin: auto; border-bottom-style:solid; border-width:2px; border-collapse:collapse;}
td {padding-left: 5px; padding-right: 5px;}
#userinfo {text-align: center; padding: 5px;}
#listhead {background:#E0E0E0; padding: 5px; border-top-style:solid; border-width:2px; border-bottom-style:solid; borde>
.tester {background:#F5F5F5; padding: 5px; }
#search {margin: 10px auto;}
#chart {margin: 10px auto; height:520px; width:1500px; overflow:auto;}
</style>

<body>

<?php
//start menu
echo "<div class=\"menu\"><a href=\"index.php\">All</a>  <a href=\"index.php?type=teachers\">Teacher</a>  <a href=\"index.php?type=students\">Student</a>  <a href=\"export.php\">Export Dataset</a></div>\n\n";

echo "<div class=\"main\">\n";
session_start();
if (isset($_COOKIE['username'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['islogin'] = 1;
}
if (isset($_SESSION['islogin'])) {
    echo "<div id=\"userinfo\">Hi! " . $_SESSION['username'] . ", Welcome Back! <a href='login.php?logout=true'>Logout</a><br /></div>";
    session_write_close();
} else {
    session_write_close();
    header('location:login.php');
    exit;
}
?>

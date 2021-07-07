<?php
$con = mysqli_connect("localhost", "amauser", "amasdoehg");
if (!$con) {echo mysqli_error();}
if (!mysqli_select_db($con, "ama")) {echo mysqli_error($con);}

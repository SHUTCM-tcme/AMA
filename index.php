<?php
require_once "header.php";
require_once "functions.php";
require_once "conn.php";
//列数据
Showtester(ucfirst($_GET['type']), $con, $_GET['page'], $_POST['keyword']);
?>
</div>
</body>
<SCRIPT LANGUAGE=javascript>
function bunchchk(qx) {
    var ck = document.getElementsByClassName("checkb2");
    if(qx.checked) {
        for(i = 0; i < ck.length; i++) {
            ck[i].setAttribute("checked", "checked");
        }
    } else {
        for(var i = 0; i < ck.length; i++) {
            ck[i].removeAttribute("checked");
        }
    }
}

function del() {
  var msg = "Are you sure to delete these Testers?\n\nConfirm!";
  if (confirm(msg)==true){
    return true;
  }else{
    return false;
  }
}
</SCRIPT>
</html>

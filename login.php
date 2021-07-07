
<?php 
	header('Content-type:text/html; charset=utf-8');
	session_start();
 
	if (isset($_POST['login'])) {
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		if (($username == '') || ($password == '')) {
			header('refresh:3; url=login.php');
			echo "Empty username or password, please re-type!";
			exit;
		} elseif (($username !== 'tcme') || ($password !== '51322450')) {
			header('refresh:3; url=login.php');
			echo "Wrong username or password, please re-type!";
			exit;
		} elseif (($username == 'tcme') && ($password == '51322450')) {
			$_SESSION['username'] = $username;
			$_SESSION['islogin'] = 1;
			if ($_POST['remember'] == "yes") {
				setcookie('username', $username, time()+30*24*60*60);
				setcookie('code', md5($username.md5($password)), time()+30*24*60*60);
			} else {
				setcookie('username', '', time()-999);
				setcookie('code', '', time()-999);
			}
			header('location:index.php');
		}
    }
    
    if (isset($_GET['logout'])) {
        $username = $_SESSION['username'];
        $_SESSION = array();
        session_destroy();

        setcookie('username', '', time()-99);
        setcookie('code', '', time()-99);

        echo "Hi, ".$username.'<br>';
        echo "<a href='login.php'>Please re-login!</a>";
    }
 ?>

<form action="login.php" method="post">
		<fieldset>
			<legend>User Login</legend>
			<ul>
				<li>
					<label>Username:</label>
					<input type="text" name="username">
				</li>
				<li>
					<label>Password:</label>
					<input type="password" name="password">
				</li>
				<li>
					<label> </label>
					<input type="checkbox" name="remember" value="yes">Remember for 30 Days
				</li>
				<li>
					<label> </label>
					<input type="submit" name="login" value="Login">
				</li>
			</ul>
		</fieldset>
</form>
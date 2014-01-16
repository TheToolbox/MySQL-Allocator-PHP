<?php
	$netid = chop($_SERVER["eppn"],"@duke.edu");

	$con = new mysqli("localhost","root");

	$result = $con->query("SELECT password FROM mysql.user WHERE user='".$netid."';");

	$allocated = $result->num_rows>0;

	$pass = "";

	if ($_POST["allocate"]==1 && !$allocated) {

		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

		for ($i = 0; $i < 10; $i++) {
			$n = mt_rand(0, strlen($alphabet)-1);
			$pass .= $alphabet[$n];
		}

		if ($con->connect_error) {die("Sorry, but there has been a database connection error. It reads as follows: "+$mysqli->connect_error);}
		//CREATE SCHEMA IF NOT EXISTS ---NAME---
		//CREATE USER '---NAME---'@'%' IDENTIFIED BY '---PASSWORD---'
		//GRANT ALL ON ---NAME---.* TO '---NAME---'@'%'

		if (!$con->query("CREATE SCHEMA IF NOT EXISTS ".$netid.";")) {die("Error creating DB");}

		if (!$con->query("CREATE USER '".$netid."'@'%' IDENTIFIED BY '".$pass."';")) {die("Error creating user");}

		if (!$con->query("GRANT ALL ON ".$netid.".* TO '".$netid."'@'%';")) {die("Error granting privileges");}

		$allocated = true;

	}
	if ($_POST["resetpw"]==1 && $allocated) {

		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		for ($i = 0; $i < 10; $i++) {
			$n = mt_rand(0, strlen($alphabet)-1);
			$pass .= $alphabet[$n];
		}

		if ($con->connect_error) {die("MySQL connection error");}

		if(!$con->query("SET PASSWORD FOR '".$netid."'@'%' = PASSWORD('".$pass."');")) {die("Error: Could not change password.");}

		if(!$con->query("FLUSH PRIVILEGES;")) {die("Error: Could not flush privileges");}

	}

	$con->close();
?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<!--<meta http-equiv="refresh" content="1" />-->
		<title>Duke Colab MySQL provisioning system</title>
		<style>
			a {
				text-decoration:none;
			}
			body {
				width: 60%;
				margin:auto;
				margin-top:50px;
				padding:30px;
				padding-top:10px;
				box-shadow: 0 0 50px;
			}
			header,footer{
				text-align:center;
			}
			#main {
				text-align:center;
			}
		</style>

	</head>
	<body>

		<header>
			<h1>
				Duke.ly MySQL Platform
			</h1>
			<h2>
				This service is designed to give a flexible, independent MySQL environment to Duke Students looking to develop applications that might need it. This way, no one needs to provision a whole server just to host a little bit of data.
			</h2>
		</header>
		<div id="main">
			<?php

			if ($allocated) { ?>

				<h3>Welcome back, <?php echo $_SERVER["displayName"]." (".$netid.")!"; ?></h3>
				You have already allocated a database called '<?php echo $netid; ?>'. To access it, connect to port 3309 on sql.duke.ly with the username '<?php echo $netid; ?>' and the password 

				<?php if ($pass!="") { ?>
					<strong> <?php echo $pass; ?></strong>
				<?php } else { ?>
					that you received when you first allocated your db. To generate a new password, click the button below.
					<br />
					<br />
					<form action="index.php" method="POST">
						<input type="hidden" name="resetpw" value="1" />
						<input style="width:100px;height:50px;" type="submit" name="submit" value="Reset" />
					</form>
				<?php } ?>
			<?php } else { ?>
				<h3>Welcome, <?php echo $_SERVER["displayName"]." (".$netid.")"; ?></h3>
				Click the button below to allocate yourself a username and password for accessing your personal database!
				If you're not sure about what any of this means, odds are that this is something you don't really want. I'd recommend checking out mysql at mysql.com and coming back here when you've got a plan. Treat the system with respect, and happy building!
				<br /><br/>
				Please be aware that you are tasked with maintaining the security of your and your users' data. By using this service, you understand that we will put forth as much effort as we can to protect it, but we can't make any promises. Use SSL connections to prevent sniffing, and use secure passwords. If you have any questions feel free to email me at my conact info below. 

				<form action="index.php" method="POST">
					<input type="hidden" name="allocate" value="1" />
					<br />
					<input style="width:100px;height:50px;" type="submit" name="submit" value="Go!" />
				</form>




			<?php }	?>

			<br />
			<br />
		</div>
		<footer>
			Made by <a href="mmmgoodyes.com">Toolbox</a> and Ransel.
		</footer>

	</body>
</html>

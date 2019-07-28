<?php

/**
 * Infiniti standards framework.
 *
 * @package     Infiniti webservice framework
 * @author      Santhosh.M <santhosh.m@infinitisoftware.net>
 * @copyright   Copyright (c) 2018 Infiniti software solution
 * @version     Release: @V1
 * @link        http://www.infinitisoftware.net/
 * @date        2019-07-03 15:33:04
 **/


$filename = explode("/", $_SERVER['SCRIPT_FILENAME']);
$filename = $filename[count($filename) - 1];
$menusUsers = array(
	'wsadmin' => array(
		"PWD" => "wsadmin@123#321",
		"menus" => array(
			'Mapping' => './MappingScript.php',
			'FileWrite' => './FileWriteViewer.php',
			'QueryFileViewer' => './QueryFileViewer.php',
			'IOCL Interface' => './ioclTool.php',
			'HDFC Tracking' => './hdfcPaymentTracking.php',
			'Memcached' => './readMemcache.php',
			'Encrypt/Decrypt' => './encryptDecrypt.php'
		)
	),
	'techadmin' => array(
		"PWD" => "techadmin@#123%",
		"menus" => array(
			'FileWrite' => './FileWriteViewer.php',
			'IOCL Interface' => './ioclTool.php'
		)
	),
	'financeAdmin' => array(
		"PWD" => "financeAdmin@#123#%",
		"menus" => array(
			'HDFC Tracking' => './hdfcPaymentTracking.php'
		)
	),
	'aypTeamAdmin' => array(
		"PWD" => "aypTeamAdmin@321#123#",
		"menus" => array(
			'QueryFileViewer' => './QueryFileViewer.php',
			'FileWrite' => './FileWriteViewer.php',
			'HDFC Tracking' => './hdfcPaymentTracking.php'
		)
	)
);


if (!(in_array($_SERVER['PHP_AUTH_USER'], array_keys($menusUsers)))) {
	header('WWW-Authenticate: Basic realm="Please enter username and password to access  the file download"');
	header('HTTP/1.0 401 Unauthorized');
	echo '<h1>Unauthorized Access</h1>';
	exit;
} else {
	if (!($_SERVER['PHP_AUTH_PW'] == $menusUsers[$_SERVER['PHP_AUTH_USER']]['PWD'])) {
		header('WWW-Authenticate: Basic realm="Please enter username and password to access  the file download"');
		header('HTTP/1.0 401 Unauthorized');
		echo '<h1>Unauthorized Access</h1>';
		exit;
	}
}

$menus = $menusUsers[$_SERVER['PHP_AUTH_USER']]['menus'];
$filenamePath = "./" . $filename;
if (!in_array($filenamePath, array_values($menus))) {
	header('HTTP/1.0 401 Unauthorized');
	echo '<h1>Unauthorized Access</h1>';
	exit;
}

if ($filename != "QueryFileViewer.php") {
	?>
	<nav class="navbar navbar-inverse navbar-fixed-top" style="background-color:#271515 !important">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="./index.php">CommonWebservice Interface</a>
			</div>
			<ul class="nav navbar-nav">
				<?php
				foreach ($menusUsers[$_SERVER['PHP_AUTH_USER']]['menus'] as $key => $value) {
					echo '<li><a href="' . $value . '">' . $key . '</a></li>';
				}
				?>
			</ul>
		</div>
	</nav>
<?php } ?>
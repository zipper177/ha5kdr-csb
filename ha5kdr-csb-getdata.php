<?php
//	ini_set('display_errors','On');
//	error_reporting(E_ALL);

	function sanitize($s) {
		return strip_tags(stripslashes(trim($s)));
	}

	include('ha5kdr-csb-config.inc.php');

	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if (!$conn) {
		echo "can't connect to mysql database!\n";
		return;
	}

	$db = mysql_select_db(DB_NAME, $conn);
	if (!$db) {
		mysql_close($conn);
		echo "can't connect to mysql database!\n";
		return;
	}

	mysql_query("set names 'utf8'");
	mysql_query("set charset 'utf8'");

	$searchfor = sanitize($_POST['searchfor']);
	$searchtoks = explode(' ', $searchfor);
	$search = '';
	for ($i = 0; $i < count($searchtoks); $i++) {
		if ($i == 0)
			$search = 'where ';
		else
			$search .= 'and ';

		$searchtok = mysql_real_escape_string($searchtoks[$i]);
		$search .= "(`partnercode` like '%$searchtok%' or " .
			"`name` like '%$searchtok%' or " .
			"`country` like '%$searchtok%' or " .
			"`zip` like '%$searchtok%' or " .
			"`city` like '%$searchtok%' or " .
			"`streethouse` like '%$searchtok%' or " .
			"`licensenumber` like '%$searchtok%' or " .
			"`callsign` like '%$searchtok%' or " .
			"`communityorprivate` like '%$searchtok%' or " .
			"`state` like '%$searchtok%' or " .
			"`levelofexam` like '%$searchtok%' or " .
			"`morse` like '%$searchtok%' or " .
			"`licensedate` like '%$searchtok%' or " .
			"`validity` like '%$searchtok%' or " .
			"`chiefoperator` like '%$searchtok%') ";
	}

	$sorting = sanitize($_GET['jtSorting']);
	$startindex = sanitize($_GET['jtStartIndex']);
	if (!ctype_digit($startindex))
		return;
	$pagesize = sanitize($_GET['jtPageSize']);
	if (!ctype_digit($pagesize))
		return;

	// Getting record count
	$result = mysql_query('select count(*) as `recordcount` from `' . DB_TABLE . '` ' . $search);
	$row = mysql_fetch_array($result);
	$recordcount = $row['recordcount'];

	$result = mysql_query('select * from `' . DB_TABLE . '` ' . $search . 'order by ' . mysql_real_escape_string($sorting) .
		' limit ' . mysql_real_escape_string($startindex) . ',' . mysql_real_escape_string($pagesize));
	$rows = array();
	while ($row = mysql_fetch_array($result))
	    $rows[] = $row;

	$jtableresult = array();
	$jtableresult['Result'] = "OK";
	$jtableresult['TotalRecordCount'] = $recordcount;
	$jtableresult['Records'] = $rows;
	echo json_encode($jtableresult);

	mysql_close($conn);
?>
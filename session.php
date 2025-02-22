<?php

session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location:index.php');
	exit();
} else {
	$s = $_SESSION['login_user'];
}

$query = "SELECT uid,sname,sid,dept,ayear FROM student WHERE sid='$s'";
$query_run = mysqli_query($db, $query);
if (mysqli_num_rows($query_run) > 0) {
	$srow = mysqli_fetch_assoc($query_run);
	
	$sdept = $srow['dept'];
	$sname = $srow['sname'];
	$sid = $srow['sid'];
	$sayear = $srow['ayear'];
	// $_SESSION['user'] = [
	// 	'id' => $srow['sid'],
	// 	'name' => $srow['sname'],
	// 	'dept' => $srow['dept'],
	// 	'year' => getAcademicYear( $user['year']),
	// 	'role' => 'student',
	// 	'uid' => $srow['uid']
	// ];

}


$query2 = "SELECT uid,name,dept,role FROM faculty WHERE id='$s'";
$query_run2 = mysqli_query($db, $query2);
if (mysqli_num_rows($query_run2) > 0) {
	$frow = mysqli_fetch_assoc($query_run2);
	$fdept = $frow['dept'];
	$fname = $frow['name'];
	$frole = $frow['role'];
	$_SESSION['user'] = [
		'id' => $s,
		'name' => $frow['name'],
		'dept' => $frow['dept'],
		'role' => $frow['role'],
		'uid' => $frow['uid']
	];

}


$query3 = "SELECT gender,dob,mobile,department FROM sbasic WHERE sid='$s'";
$query_run3 = mysqli_query($db, $query3);
if (mysqli_num_rows($query_run3) > 0) {
	$srow2 = mysqli_fetch_assoc($query_run3);
	$sgender = $srow2['gender'];
	$sdob = $srow2['dob'];
	$smobile = $srow2['mobile'];
	$sdepartment = $srow2['department'];

}

// function getAcademicYear($courseYear)
// {
//     $currentDate = new DateTime();
//     $currentYear = $currentDate->format('Y');
//     $currentMonth = $currentDate->format('m');

//     list($startYear, $endYear) = explode('-', $courseYear);

//     $academicYear = $currentYear - $startYear + 1;

//     if ($currentMonth < 6) {
//         $academicYear--;
//     }

//     if ($academicYear > ($endYear - $startYear)) {
//         return $endYear - $startYear;
//     }
     
//     return $academicYear;
// }
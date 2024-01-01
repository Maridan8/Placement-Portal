<?php

session_start();

if (empty($_SESSION['id_user'])) {
	header("Location: index.php");
	exit();
}

require_once("db.php");

if (isset($_GET)) {

	$sql = "SELECT * from users WHERE id_user='$_SESSION[id_user]' ";
	$result1 = $conn->query($sql);

	if ($result1->num_rows > 0) {
		$row1 = $result1->fetch_assoc();
		$sum = $row1['hsc'] + $row1['ssc'] + $row1['ug'] + $row1['pg'];
		$total = ($sum / 4);
		$course1 = $row1['qualification'];
	}

	$sql = "SELECT maximumsalary, qualification FROM job_post WHERE id_jobpost='$_GET[id]'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$eligibility = $row['maximumsalary'];
		$course2 = $row['qualification'];
		if ($total >= $eligibility) {
			if ($course1 == $course2) {
				header("Location: view-job-post.php?id=$_GET[id]");
				$_SESSION['status'] = "You are eligible for this drive.";
				$_SESSION['status_code'] = "success";
			}
		}
	}

	if ($total >= $eligibility) {
		if ($course1 == $course2) {
			$sql1 = "SELECT * FROM apply_job_post WHERE id_user='$_SESSION[id_user]' AND id_jobpost='$_GET[id]'";
			$result1 = $conn->query($sql1);
			if ($result1->num_rows == 0) {

				// $_SESSION['status_code2'] = "success";

				$sql4 = "select C.id_company from job_post AS SJ inner join company AS C on SJ.id_company=C.id_company where id_jobpost = '$_GET[id]'";


				$sql = "INSERT INTO apply_job_post(id_jobpost, id_company, id_user) VALUES ('$_GET[id]', '$_SESSION[id_company]', '$_SESSION[id_user]')";

				if ($conn->query($sql) === TRUE) {

					$_SESSION['jobApplySuccess'] = true;
					header("Location: user/index.php");
					$_SESSION['status1'] = "Congrats!";
					$_SESSION['status_code1'] = "success";
					exit();
				} else {
					echo "Error " . $sql . "<br>" . $conn->error;
				}

				$conn->close();
			} else {
				header("Location: view-job-post.php?id=$_GET[id]");
				$_SESSION['status'] = "You have already applied for this Drive.";
				$_SESSION['status_code'] = "success";

				exit();
			}
		} else {

			header("Location: view-job-post.php?id=$_GET[id]");
			$_SESSION['status'] = "You are not eligible for this drive due to the course criteria.";
			$_SESSION['status_code'] = "success";
		}
	} else {

		header("Location: view-job-post.php?id=$_GET[id]");
		$_SESSION['status'] = "You are not eligible for this drive due to the overall percentage criteria. Update your marks in your profile, if you think you are eligible.";
		$_SESSION['status_code'] = "success";
	}
} else {
	header("Location: user/index.php");
}

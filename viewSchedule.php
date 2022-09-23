<?php

    error_reporting(E_ALL ^ E_NOTICE);
    require_once('connect.php');
    unset($_SESSION['dropavailableId']);
    unset($_SESSION['droppedCourseName']);
    unset($_SESSION['droppedSemester']);
    unset($_SESSION['droppedYear']);
    unset($_SESSION['numStudentsEnrolled']);
    unset($_SESSION['maxStudents']);
    unset($_SESSION['numStudentsOnWaitlist']);
    unset($_SESSION['waitlistedStudentId']);
    unset($_SESSION['dateTimeAdded']);
    $myConnection = $newConnection->connection;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> View Schedule </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-sacle=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="index.css">
    <script src="https://ajax.googleleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	
</head>
<body>

<?php include 'master.php';?>

    <div style='margin-bottom:60px' class="container text-center">
        <?php
            if(isset($_SESSION['username'])) {
                echo "<h1>Here is your course schedule for the 2022-2023 year, ".$_SESSION['username']."</h1>";
                echo "<br>";
                echo "<h2>You are registered for:</h2>";
                displayCourseSchedule($myConnection,$_SESSION['student_id']);
            }
            else {
                echo "<h1>Course Schedule Page</h1>";
                echo "<h3>Please login or register</h3>";
            };

            if (isset($_POST['dropButton'])) {  
                echo "<meta http-equiv='refresh' content='0'>";
                $_SESSION['dropavailableId'] = student_input($_POST["drop"]);                
                dropCourse($myConnection,$_SESSION['student_id'],$_SESSION['dropavailableId']);
                echo "<p style='padding-top:15px'>You have successfully dropped ".$_SESSION['droppedCourseName']." from ".$_SESSION['droppedSemester']." ".$_SESSION['droppedYear']."</p>";
                echo "<p>Please wait while your schedule is updated.</p>";
                numStudentsEnrolled($myConnection,$_SESSION['dropavailableId']);
                maxStudentsForCourse($myConnection,$_SESSION['dropavailableId']);
                if ($_SESSION['numStudentsEnrolled'] == $_SESSION['maxStudents'] - 1) {
                    numStudentsOnWaitlist($myConnection,$_SESSION['dropavailableId']);
                    if ($_SESSION['numStudentsOnWaitlist'] != 0) {
                        getWaitlistedStudent($myConnection,$_SESSION['dropavailableId']);
                        registerForCourse($myConnection,$_SESSION['waitlistedStudentId'],$_SESSION['dropavailableId']);
                        removeStudentFromWaitlist($myConnection,$_SESSION['waitlistedStudentId'],$_SESSION['dropavailableId'],$_SESSION['dateTimeAdded']);
                        notifyStudent($myConnection,$_SESSION['waitlistedStudentId'],$_SESSION['dropavailableId']);
                    }
                }
            };

        ?>

    </div>

<?php include 'footer.php';?>

</body>
</html>

<?php

    function student_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function displayCourseSchedule($connection,$studentId) {
        $getScheduleQuery =  "SELECT enrollment.student_id, available.available_id, course.courseName, available.year, available.semester
            FROM ((enrollment
                INNER JOIN available ON enrollment.available_id = available.available_id
                    AND enrollment.student_id = $studentId)
                INNER JOIN course ON course.course_id = available.course_id)";
        $results = mysqli_query($connection, $getScheduleQuery); 
        if (mysqli_num_rows($results) != 0) { 
            while($row = mysqli_fetch_assoc($results)) {
                $availableId = $row['available_id'];
                $courseName = $row['courseName'];
                $courseYear = $row['year'];
                $courseSemester = $row['semester'];
					echo "<div class='row'>";
						echo "<div class='col-md-5 text-left'>";
							echo "<td><h3><b>Course Name</b></h3></td>";
						echo "</div>";
						echo "<div class='col-md-3 text-left'>";
							echo "<td><h3><b>Semester</b></h3></td>";
						echo "</div>";
						echo "<div class='col-md-3 text-left'>";
							echo "<td><h3><b>Year</b></h3></td>";
						echo "</div>";
					echo "</div>";
						echo "<div class='row'>";
							echo "<tr>";
							echo "<div class='col-md-5 text-left'>";
								echo "<td><h3>".$courseName."</h3></td>";
							echo "</div>";
							echo "</tr>";
							echo "<div class='col-md-3 text-left'>";
								echo "<td><h3>".$courseSemester."</h3></td>";
							echo "</div>";
							echo "<div class='col-md-2 text-left'>";
								echo "<td><h3>".$courseYear."</h3></td>";
							echo "</div>";
							echo "<div style='padding-top:15px' class='col-md-2 text-left'>";
								echo "<form method='post'>";
									echo "<input type='hidden' name='drop' value=".$availableId.">";
									echo "<td><button data-toggle='tooltip' title='Warning: This will drop this class' style='font-family:sans-serif' type='submit' class='btn btn-danger' name='dropButton'>DROP</button></td>";
								echo "</form>";
							echo "</div>";
						echo "</div>";
            }
        } 
    };

    function dropCourse($connection,$studentId,$availableId) {
        $dropQuery =  "DELETE FROM enrollment
            WHERE student_id = $studentId AND available_id = $availableId";
        $results = mysqli_query($connection, $dropQuery);

        $getCourseInfoQuery =  "SELECT course.courseName, available.semester, available.year
            FROM course
            INNER JOIN available ON course.course_id = available.course_id
                AND available.available_id = $availableId";
        $results = mysqli_query($connection, $getCourseInfoQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['droppedCourseName'] = $row['courseName'];
                $_SESSION['droppedSemester'] = $row['semester'];
                $_SESSION['droppedYear'] = $row['year'];
            };
        };
    };

    function numStudentsEnrolled($connection,$availableId) {
        $numStuEnrolledQuery =  "SELECT COUNT(enrollment.available_id) as 'count'
            FROM enrollment
            WHERE available_id = $availableId";
        $results = mysqli_query($connection, $numStuEnrolledQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['numStudentsEnrolled'] = $row['count'];
            };
        };
    };

    function maxStudentsForCourse($connection,$availableId) {
        $maxStudentsQuery =  "SELECT course.maxStudents
            FROM course
            INNER JOIN available ON available.course_id = course.course_id
                AND available.available_id = $availableId";
        $results = mysqli_query($connection, $maxStudentsQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['maxStudents'] = $row['maxStudents'];
            };
        };
    };

    function numStudentsOnWaitlist($connection,$availableId) {
        $numStuWaitlistQuery =  "SELECT COUNT(*) as students
            FROM waitlist
            WHERE available_id = $availableId";
        $results = mysqli_query($connection, $numStuWaitlistQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['numStudentsOnWaitlist'] = $row['students'];
            };
        };
    };

    function getWaitlistedStudent($connection,$availableId) {
        $waitlistedStudentQuery =  "SELECT student_id, dateTimeAdded
            FROM waitlist
            WHERE available_id = $availableId
            ORDER BY dateTimeAdded LIMIT 1";
        $results = mysqli_query($connection, $waitlistedStudentQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['waitlistedStudentId'] = $row['student_id'];
                $_SESSION['dateTimeAdded'] = $row['dateTimeAdded'];
            };
        };
    };

    function registerForCourse($connection,$studentId,$availableId) {
        $registerQuery =  "INSERT INTO enrollment (student_id, available_id)
            VALUES 
                ($studentId,$availableId)";
        $results = mysqli_query($connection, $registerQuery);
    }
    
    function removeStudentFromWaitlist($connection,$studentId,$availableId,$dateTimeAdded) {
        $removeFromWaitlistQuery =  "DELETE FROM waitlist 
            WHERE student_id = $studentId
                AND available_id = $availableId
                AND dateTimeAdded = '$dateTimeAdded'";
        $results = mysqli_query($connection, $removeFromWaitlistQuery);
    };

    function notifyStudent($connection,$studentId,$availableId) {
        $createNotificationQuery =  "INSERT INTO notification (student_id, available_id)
            VALUES 
                ($studentId,$availableId)";
        $results = mysqli_query($connection, $createNotificationQuery);
    };

?>
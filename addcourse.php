<?php
    error_reporting(E_ALL ^ E_NOTICE);
    require_once('connect.php');
    unset($_SESSION['selectedAvailable_id']);
    unset($_SESSION['registered']);
    unset($_SESSION['waitlisted']);
    unset($_SESSION['numStudentsEnrolled']);
    unset($_SESSION['maxStudents']);
    require 'master.php';
    $myConnection = $newConnection->connection;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title> Add Course </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-sacle=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://ajax.googleleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container text-center">
        <?php

            if($_SESSION['selectedYear'] == 2022 && $_SESSION['selectedSemester'] == 'Spring') {
                echo "<h1>Sorry, registration for 	".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear']." is closed.</h1>";
            } else {
                echo "<h1>Register for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear']."</h1>";
                echo "<h3>Please select the course that you would like to register for</h3>";
            }
            
        ?>
    </div>
    <div style='margin-bottom:60px' class="container">
        <form class="padding-top" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-row">
                <div class="form-group col-md-12" id="no-padding-left">
                    <label for="inputCourse">Course</label>
                    <select id="inputCourse" class="form-control" name="course" required>
                        <option>Choose...</option>
                        <?php

                            $availableCoursesArray = array();
                            $availableCoursesArray = getAvailableCourses($myConnection,$_SESSION['selectedYear'],$_SESSION['selectedSemester']);
                            foreach($availableCoursesArray as $data) {
                                echo "<option>".$data['courseName']."</option>";
                            }

                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" name="select_course">Submit</button>
            <?php
                if (isset($_POST['select_course'])) {
                    $_SESSION['selectedCourse'] = student_input($_POST["course"]);
                    getavailableId($myConnection,$_SESSION['selectedCourse'],$_SESSION['selectedYear'],$_SESSION['selectedSemester']);
                    checkIfRegistered($myConnection,$_SESSION['student_id'],$_SESSION['selectedAvailable_id']);
                    if ($_SESSION['registered'] == 1) {
                        echo "<p style='padding-top:15px'>You are already registered for this course.  Please make another selection.</p>";
                    } else if ($_SESSION['registered'] == 0) {
                        numStudentsEnrolled($myConnection,$_SESSION['selectedAvailable_id']);
                        maxStudentsForCourse($myConnection,$_SESSION['selectedAvailable_id']);
                        if ($_SESSION['numStudentsEnrolled'] < $_SESSION['maxStudents']) {
                            registerForCourse($myConnection,$_SESSION['student_id'],$_SESSION['selectedAvailable_id']);
                            echo "<p style='padding-top:15px'>You have successfully registered for ".$_SESSION['selectedCourse']." for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear'].".</p>";
                        } else if ($_SESSION['numStudentsEnrolled'] == $_SESSION['maxStudents']) {
                            checkIfWaitlisted($myConnection,$_SESSION['student_id'],$_SESSION['selectedAvailable_id']);
                            if ($_SESSION['waitlisted'] == 1) {
                                echo "<p style='padding-top:15px'>You are already on the waitlist for ".$_SESSION['selectedCourse']." for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear'].".  Please make another selection.</p>";
                            } else {
                                addToWaitlist($myConnection,$_SESSION['student_id'],$_SESSION['selectedAvailable_id']);
                                echo "<p style='padding-top:15px'>This course is full.  You have been successfully added to the waitlist for ".$_SESSION['selectedCourse']." for ".$_SESSION['selectedSemester']." ".$_SESSION['selectedYear'].".</p>";
                            }                         
                        }
                    }
                }                
            ?>
        </form>
    </div>
<?php require_once 'footer.php';?>
</body>
</html>

<?php
    function student_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    function getAvailableCourses($connection,$year,$semester) {
        $items = array();

        $getSemestersQuery =  "SELECT course.courseName
            FROM course
            INNER JOIN available ON course.course_id = available.course_id
                AND available.year = $year
                AND available.semester = '$semester'";
        $results = mysqli_query($connection, $getSemestersQuery); 
        while($row = mysqli_fetch_assoc($results)) {
            $items[] = $row;
        }
        print_r($items);
        return $items;
    };

    function getavailableId($connection,$courseName,$year,$semester) {
        $availableIdQuery = "SELECT available.available_id
            FROM available
            INNER JOIN course ON available.course_id = course.course_id
                AND available.year = $year
                AND available.semester = '$semester'
                AND course.courseName = '$courseName'";
        $results = mysqli_query($connection, $availableIdQuery);
        if (mysqli_num_rows($results) != 0) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['selectedAvailable_id'] = $row['available_id'];
            };
        };
    };

    function checkIfRegistered($connection,$studentId,$availableId) {
        $checkIfRegisteredQuery =  "SELECT COUNT(*) as count
        FROM enrollment
        WHERE student_id = $studentId AND available_id = $availableId";
        $results = mysqli_query($connection, $checkIfRegisteredQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['registered'] = $row['count'];
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

    function checkIfWaitlisted($connection,$studentId,$availableId) {
        $checkIfWaitlistedQuery =  "SELECT COUNT(*) as count
        FROM waitlist
        WHERE student_id = $studentId AND available_id = $availableId";
        $results = mysqli_query($connection, $checkIfWaitlistedQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['waitlisted'] = $row['count'];
            };
        };
    };

    function addToWaitlist($connection,$studentId,$availableId) {
        $addToWaitlistQuery =  "INSERT INTO waitlist (student_id, available_id, dateTimeAdded)
            VALUES 
                ($studentId,$availableId,NOW())";
        $results = mysqli_query($connection, $addToWaitlistQuery);
    }
    
    function registerForCourse($connection,$studentId,$availableId) {
        $registerQuery =  "INSERT INTO enrollment (student_id, available_id)
            VALUES 
                ($studentId,$availableId)";
        $results = mysqli_query($connection, $registerQuery);
    }
?>
<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login_new.php");
    exit;
}
// Include connect php
require_once "connect.php";
require 'master.php';
$myConnection = $newConnection->connection;

?>

<?php
// Check existence of id parameter before processing further
if(isset($_SESSION["student_id"]) && !empty(trim($_SESSION["student_id"]))){
    
    // Prepare a select statement
    $sql = "SELECT * FROM student WHERE student_id = ?";
    
    if($stmt = mysqli_prepare($myConnection, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_SESSION["student_id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            //$result = mysqli_stmt_SESSION_result($stmt);
			$result = mysqli_stmt_GET_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
				$email = $row["email"];	
                $firstName = $row["firstName"];
				$lastName = $row["lastName"];
                $address = $row["address"];
				$address2 = $row["address2"];
				$city = $row["city"];
				$state = $row["state"];
				$zip = $row["zip"];
				$phone = $row["phone"];		   
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: error.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    //mysqli_stmt_close($stmt);
    
    // Close connection
    //mysqli_close($myConnection);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<body>


       <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
					<div class="container text-center">		
						<div class="container middle-container">
							<div class="col-lg-12 middle-text divcenter">
							<h3 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to your profile.</h3>
							<p>
								<?php
									if(isset($_SESSION["username"])) {
										checkNotifications($myConnection,$_SESSION['student_id']);
										if ($_SESSION['numNotifications'] != 0) {
											$notificationsArray = array();
											$notificationsArray = getNotifications($myConnection,$_SESSION['student_id']);
											foreach($notificationsArray as $data) {
												echo "<h3 style='padding-top:15px'>Congratulations: You have been registered for ".$data['courseName']." for ".$data['semester']." ".$data['year']."</h3>";
											}
										}
									}
									else {
										echo "<h3>Hi, ".$data["username"].". Welcome to your profile.</h3>";
									}
								?>
							</p>
							</div>		
						</div>
					</div>
					
					<div class="col-sm-12 zeropad center"><span class="navbar-text wp_txt">
					<div class="accordion" style="text-align:left;">
                    <div class="form-group">
                        <label>First Name</label>
                        <p><?php echo $row["firstName"]; ?></p>
                    </div>
					<div class="form-group">
                        <label>Last Name</label>
                        <p><?php echo $row["lastName"]; ?></p>
                    </div>
					<div class="form-group">
                        <label>Email</label>
                        <p><?php echo $row["email"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <p><?php echo $row["address"]; ?></p>
                    </div>
					 <div class="form-group">
                        <label>Address2</label>
                        <p><?php echo $row["address2"]; ?></p>
                    </div>
					<div class="form-group">
                        <label>City</label>
                        <p><?php echo $row["city"]; ?></p>
                    </div>
					<div class="form-group">
                        <label>State</label>
                        <p><?php echo $row["state"]; ?></p>
                    </div>
					<div class="form-group">
                        <label>Zip</label>
                        <p><?php echo $row["zip"]; ?></p>
                    </div>
					<div class="form-group">
                        <label>Phone</label>
                        <p><?php echo $row["phone"]; ?></p>
                    </div>
                    <p><a href="home.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</div>	
			
		
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
		
<?php require_once 'footer.php';?>
</body>
</html>

<?php

    function checkNotifications($connection,$student_id) {
        $numNotificationsQuery =  "SELECT COUNT(*) as notifications
            FROM notification
            WHERE student_id = $student_id";
        $results = mysqli_query($connection, $numNotificationsQuery);
        if (mysqli_num_rows($results) == 1) { 
            while($row = mysqli_fetch_assoc($results)) {
                $_SESSION['numNotifications'] = $row['notifications'];
            };
        };
    }

    function getNotifications($connection,$student_id) {
        $items = array();

        $getNotificationsQuery =  "SELECT course.courseName, available.year, available.semester
            FROM ((notification
            INNER JOIN available ON notification.available_id = available.available_id
                AND notification.student_id = $student_id)
            INNER JOIN course ON course.course_id = available.course_id)";
        $results = mysqli_query($connection, $getNotificationsQuery); 
        while($row = mysqli_fetch_assoc($results)) {
            $items[] = $row;
        }
        return $items;
    }

?>
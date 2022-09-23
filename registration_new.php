<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = $firstName = $param_lastName = 
$param_address = $param_address2 = $param_city = $param_state = $param_zip =  $param_phone = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT student_id FROM student WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
	// Post Email
    $email = trim($_POST["email"]);	
	// Post First Name
    $firstName = trim($_POST["firstName"]);	
	// Post Last Name
    $lastName = trim($_POST["lastName"]);	
	// Post Address
    $address = trim($_POST["address"]);
		// Post Address
    $address2 = trim($_POST["address2"]);
		// Post Address
    $city = trim($_POST["city"]);
		// Post Address
    $state = trim($_POST["state"]);
		// Post Address
    $zip = trim($_POST["zip"]);
	// Post Phone
    $phone = trim($_POST["phone"]);		

    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
			
		        // Prepare an insert statement
        $sql = "INSERT INTO student (username, password, email, firstName, lastName, address, address2, city, state, zip, phone) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt,"sssssssssss", $param_username, $param_password, $param_email, $param_firstName, 
									$param_lastName, $param_address, $param_address2, $param_city, $param_state, $param_zip, $param_phone);
            
            // Set parameters
			$param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
			$param_email = $email;
			$param_firstName = $firstName;
			$param_lastName = $lastName;
			$param_address = $address;
			$param_address2 = $address2;
			$param_city = $city;
			$param_state = $state;
			$param_zip = $zip;
			$param_phone = $phone;
            
			
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login_new.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up</title>
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
<?php require 'master.php';?>
	
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" required>
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" required>
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>" required>
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>			
			<div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
			<div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstName" class="form-control" required>  
            </div>
			<div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastName" class="form-control" required>
            </div>
			<div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Address 2</label>
                <input type="text" name="address2" class="form-control" placeholder="Apartment #">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>State</label>
                    <select name="state" class="form-control" required>
                        <option>Choose...</option>
                        <?php
                        $states = ["AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "LA", "MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT", "NC", "ND", "NE", "NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY"];
                        $stateLength = count($states);
                        for ($i=0; $i<$stateLength; $i++) {
                            echo "<option>$states[$i]</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Zip</label>
                    <input type="text" name="zip" class="form-control" required>
                </div>
			<div class="form-group">
                <label>Phone</label>
                <input type="phone" name="phone" class="form-control" required>
            </div> 
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login_new.php">Login here</a>.</p>
        </form>
    </div>
	</div>	
<?php require_once 'footer.php';?>	
</body>
</html>
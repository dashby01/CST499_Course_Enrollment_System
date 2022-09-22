<?php
	error_reporting(E_ALL ^ E_NOTICE);
	require 'footer.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-9">
				<img id="_ctl0_cphHeader_Header1_CMCImage1" class=" navbar-brand1 img-responsive" src="https://student.uagc.edu/Public/ash/images/logo.png" alt="Image of Logo" border="0">
			</div>
				<div class="container text-center">	
					<h1>Course Enrollment System</h1>
						<div class="cmcDate">
							<?php
							echo "Today is " . date("l, F d, Y") . " <br>";
							?>

						</div>
				</div>
		</div>
	</div>	

<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="myNavbar">
				<span class= "icon-bar"></span>
				<span class= "icon-bar"></span>
				<span class= "icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav">
				<li class="active"><a href="home.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
				<li><a href="AboutUs.php"><span class="glyphicon glyphicon-exclamation-sign"></span> AboutUs</a></li>
				<li><a hrefhref="#"><span class="glyphicon glyphicon-earphone"></span> ContactUs</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
		<?php
			session_start();
			
			if( isset($_SESSION['username']))
			{
				echo '<li><a href="profile.php"><span class="glyphicon glyphicon-briefcase"></span> Profile</a></li>';
				echo '<li><a href="viewSchedule.php"><span class="glyphicon glyphicon-th-list"></span> View Schedule</a></li>';
                echo '<li><a href="searchCourses.php"><span class="glyphicon glyphicon-plus"></span> Register for Courses</a></li>';
				echo '<li><a href="logout.php"><span class"glyphicon glyphicon-off"></span> Logout</a></li>';
			}
			else
			{
				echo '<li><a href="login_new.php"><span class="glyphicon glyphicon-user"></span> Login</a></lie>';
				echo '<li><a href="registration_new.php"><span class=""glyphicon glyphicon-pencil"></span> Registration</a></li>';
			}
		?>
			</ul>
		</div>
	</div>
</nav>

</body>
</html>


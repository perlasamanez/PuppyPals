<?php


	$inData = getRequestInfo();
    $firstName = $inData["fname"];
    $lastName = $inData["lname"];
    $username = $inData["username"];
	$email = $inData["email"];
    $password = $inData["password"];
	$password2 = $inData["password2"];

	$conn = new mysqli("localhost", "poopspg2_user_pp", "FYPykYr~@7T!", "poopspg2_PuppyPals");

	if ($conn->connect_error)
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
        $sql = "select firstName, lastName, email  from user where username ='" . $username . "'";
        $result = $conn->query($sql);
        if($result->num_rows > 0)
        {
            returnWithError("Username already exists");
        }
        else{

			if($password == $password2)
			{

				$salted = "alk3245jylj345j345g34fg6532l1".$password."lksdfjklerwwej23g42g34g234g32";

				$hashed = hash('sha512', $salted);

		        $sql = "insert into user (firstName, lastName, email, username, password) VALUES ('" . $firstName . "','" . $lastName . "', '" . $email . "', '" . $username . "','" . $hashed . "')";
		        if( $result = $conn->query($sql) != TRUE )
		        {
		            returnWithError( $conn->error );
		        }
		        else
		        {
		            returnWithError("");
		        }
			
			}
			else
			{

				returnWithError("Passwords do not match");

			}

        }
		$conn->close();
	}
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
?>

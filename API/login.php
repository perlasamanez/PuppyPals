<?php
    
    $inData = getRequestInfo();
    
    $userID = 0;
	$firstName = "";
	$lastName = "";
	$email = "";
	$username = $inData["username"];
	$password = $inData["password"];
    
    $conn = new mysqli("localhost", "poopspg2_user_pp", "FYPykYr~@7T!", "poopspg2_PuppyPals");
    if($conn->connect_error)
    {
        returnWithError( $conn->connect_error );
    }
    else
    {
		$salted = "alk3245jylj345j345g34fg6532l1".$password."lksdfjklerwwej23g42g34g234g32";

		$hashed = hash('sha512', $salted);
        
		$sql = "SELECT userID FROM user where username ='" . $username . "' and password ='" . $hashed . "'";

        $result = $conn->query($sql);
        if($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $userID = $row["userID"];
			$firstName = $row["firstName"];
            $lastName = $row["lastName"];
            $email = $row["email"];
            
            $conn->close();
            
            returnWithInfo($userID, $firstName, $lastName, $email);
        }
        else
        {
            $conn->close();
            
            returnWithError( "No Records Found" );
        }
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
        $retValue = '{"userID":0,"firstName":"","lastName":"","error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
    
    function returnWithInfo( $firstName, $lastName, $userID )
    {
        $retValue = '{"userID":' . $userID . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '", "email":"' . $email . '","error":""}';
        sendResultInfoAsJson( $retValue );
    }
    
?>

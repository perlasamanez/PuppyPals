<?php
    
    $inData = getRequestInfo();
    
    $userID = 0;
	$firstName = "";
	$lastName = "";
	$email = "";
    $dName = "";
    $size = -1;
    $breed = "";
    $age = -1;
    $bio = "";
    $gender = "";
    $imgDog = "";
    $sizeSeeking = -1;
    $radiusSeeking = -1;
    $genderSeeking = "";

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
        
        $sql = "SELECT * FROM user WHERE username ='" . $username . "' and password ='" . $hashed . "'";
        
        $result = $conn->query($sql);
        
        if($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $userID = $row["userID"];
			$firstName = $row["firstName"];
            $lastName = $row["lastName"];
            $email = $row["email"];

            //get dog info
            $sql = "SELECT * FROM dog WHERE userID=" . $userID;
            $dog_query = $conn->query($sql);
            $dog = $dog_query->fetch_assoc();
            $dName = $dog["dName"];
            $size = $dog["size"];
            $breed = $dog["breed"];
            $age = $dog["age"];
            $bio = $dog["bio"];
            $gender = $dog["gender"];
            $imgDog = $dog["imgDog"];

            //get settings info
            $sql = "SELECT * FROM settings WHERE userID=" . $userID;
            $settings_query = $conn->query($sql);
            $settings = $settings_query->fetch_assoc();
            $sizeSeeking = $settings["sizeSeeking"];
            $radiusSeeking = $settings["radiusSeeking"];
            $genderSeeking = $settings["genderSeeking"];
            
            $conn->close();
            
            returnWithInfo($userID, $firstName, $lastName, $email, $dName, $size, $breed, $age, $bio, $gender, $imgDog, $sizeSeeking, $radiusSeeking, $genderSeeking);
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
        $retValue = '{"userID":0,"firstName":"","lastName":"", "email": "","error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
    
    function returnWithInfo($userID, $firstName, $lastName, $email, $dName, $size, $breed, $age, $bio, $gender, $imgDog, $sizeSeeking, $radiusSeeking, $genderSeeking)
    {
        $retValue = '{"userID":' . $userID . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","email":"' . $email . '","dName":"' . $dName . '","size":' . $size . ',"breed":"' . $breed . '","age":"' . $age . '","bio":"' . $bio . '","gender":"' . $gender . '","imgDog":"' . $imgDog . '","sizeSeeking":' . $sizeSeeking . ',"radiusSeeking":"' . $radiusSeeking . '","genderSeeking":"' . $genderSeeking . '","error":""}';
        sendResultInfoAsJson( $retValue );
    }
    
?>

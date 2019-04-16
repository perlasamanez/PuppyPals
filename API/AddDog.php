<?php

    $inData = getRequestInfo();

    $userID = $inData["userID"];
    $age = $inData["age"];
    $bio = $inData["bio"];
    $breed = $inData["breed"];
    $dName= $inData["dName"];
    $gender = $inData["gender"];
    $imgDog = $inData["imgDog"];
    $size = $inData["size"];


    $conn = new mysqli("localhost", "poopspg2_user_pp", "FYPykYr~@7T!", "poopspg2_PuppyPals");
    if($conn->connect_error)
    {
        returnWithError( "$conn->connect_error" );
    }
    else
    {
      $sql = "insert into dog (userID, age, bio, breed, dName, gender, imgDog, size) VALUES (" . $userID . "," . $age . "," . '"' . $bio . '"' . ",'" . $breed . "','" . $dName. "','" . $gender . "','" . $imgDog . "'," . $size . ")";

  		if( $result = $conn->query($sql) != TRUE )
  		{
  			returnWithError( $conn->error );
  		}
        else{
            returnWithInfo(1);
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
        $retValue = '{"success":0,"error":"' . $err . '"}';
  		sendResultInfoAsJson( $retValue );
  	}

    function returnWithInfo($success)
    {
        $retValue = '{"success":' . $success . ',"error":""}';
        sendResultInfoAsJson( $retValue );
    }
?>

<?php

    $inData = getRequestInfo();

    $userID = $inData["userID"];
    $genderSeeking = $inData["genderSeeking"];
    $radiusSeeking = $inData["radiusSeeking"];
    $sizeSeeking = $inData["sizeSeeking"];

    $conn = new mysqli("localhost", "poopspg2_user_pp", "FYPykYr~@7T!", "poopspg2_PuppyPals");
    if($conn->connect_error)
    {
        returnWithError( "$conn->connect_error" );
    }
    else
    {

      $sql = "insert into settings (userID, genderSeeking, radiusSeeking, sizeSeeking) VALUES (" . $userID . ",'" . $genderSeeking . "'," . $radiusSeeking . ",'" . $sizeSeeking . "')";

  		if( $result = $conn->query($sql) != TRUE )
  		{
  			returnWithError( $conn->error );
  		}
  		else 
  		{
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

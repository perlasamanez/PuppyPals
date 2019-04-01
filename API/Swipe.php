<?php
    //extract the data from the JSON input
    $inData = getRequestInfo();
    
    //get specific values from the data
    $userID = $inData["userID"];
    $otherUserID = $inData["otherUserID"];
    
    //establish connection to the database
    $conn = new mysqli("localhost", "poopspg2_user_pp", "FYPykYr~@7T!", "poopspg2_PuppyPals");
    if($conn->connect_error)
    {
        returnWithError( "$conn->connect_error" );
    }
    else
    {
        //check if other user has already liked user
        $sql = "SELECT matchID_to_MatchesTable FROM likes WHERE other=" . $userID;
        $result = $conn->query($sql);

        if($result->num_rows > 0)
        {
            //the other user liked user, it's a match!
            $matchID_query = $result->fetch_assoc();
            $matchID = $matchID_query["matchID_to_MatchesTable"];

            //add the new like to the likes table
            $sql = "INSERT into likes (userID,other,status,matchID_Mutual) VALUES (" . $userID . "," . $otherUserID . ",1," . $matchID . ")";
            $conn->query($sql);

            //update otherUser's like to have successful match status
            $sql = "UPDATE likes SET status=1, matchID_Mutual=" . $matchID ." WHERE matchID_to_MatchesTable=" . $matchID;
            $conn->query($sql);

            //add the new match to the match table
            $sql = "INSERT into matches (matchID) VALUES (" . $matchID . ")";
            $conn->query($sql);

            //return a successful match
            returnWithInfo(1);
        }
        else
        {
            //not a match, just insert the new like
            $sql = "INSERT into likes (userID,other) VALUES (" . $userID . "," . $otherUserID . ")";
            $conn->query($sql);

            //return no match
            returnWithInfo(0);
        }
        
        
        //close the connection to the database
        $conn->close();
    }
    
    //decodes the JSON sent to the API
    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    //sends the given object back as JSON
    function sendResultInfoAsJson( $obj )
    {
        header('Content-type: application/json');
        echo $obj;
    }
    
    //sends a JSON object back containing error information
    function returnWithError( $err )
    {
        $retValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
    
    //sends a JSON object back containing result information
    function returnWithInfo( $matchStatus )
    {
        $retValue = '{"matchStatus":' . $matchStatus . ',"error":""}';
        sendResultInfoAsJson( $retValue );
    }
?>

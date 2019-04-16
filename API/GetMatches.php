<?php
    //decode incoming JSON object
    $inData = getRequestInfo();
    
    //extract data
    $userID = $inData["userID"];

    //return data
    $matchResults = "";
    $matchCount = 0;

    //establish connection to remote database
    $conn = new mysqli("localhost", "poopspg2_user_pp", "FYPykYr~@7T!", "poopspg2_PuppyPals");
    
    //check if connection was successful
    if($conn->connect_error)
    {
        returnWithError( "$conn->connect_error" );
    }
    else
    {
        //get the matchID and otherUserID for all matches for this user
        $sql = "SELECT other,matchID_Mutual FROM likes where userID=" . $userID . " AND status=1";

        //query the database
        $other_and_matchIDs = $conn->query($sql);

        if($other_and_matchIDs->num_rows > 0)
        {
            //loop until all matches processed
            while($match = $other_and_matchIDs->fetch_assoc()){
				//get other and matchID for this match
                $otherID = $match["other"];
                $matchID = $match["matchID_Mutual"];

                //fetch additional information from the database
                //get human name and dog name
                $sql = "SELECT firstName,matchMessage FROM user WHERE userID=" . $otherID;
                $name_query = $conn->query($sql);
                $name = $name_query->fetch_assoc();
                $humanName = $name["firstName"];
                $matchMessage = $name["matchMessage"];

				$sql = "SELECT dName,imgDog FROM dog WHERE userID=" . $otherID;
                $name_query = $conn->query($sql);
                $name = $name_query->fetch_assoc();
                $dogName = $name["dName"];
                $imgDog = $name["imgDog"];

                //add to JSON object containing results
                if(matchCount > 0)
                {
                    $matchResults .= ",";
                }
                $matchCount++;
                $matchResults .= '{' . $otherID . ',"' . $humanName . '","' . $dogName . '","' . $matchMessage . '","' . $imgDog . '"}';
            }

            
            $conn->close();
            
            returnWithInfo($matchResults);
        }
        else
        {
            $conn->close();
            
            returnWithError( "No Matches Found" );
        }
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
        $retValue = '{"matches":[],"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
    
    //sends a JSON object back containing result information
    function returnWithInfo($matches)
    {
        $retValue = '{"matches":[' . $matches . '],"error":""}';
        sendResultInfoAsJson( $retValue );
    }
    
?>

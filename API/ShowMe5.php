<?php
    //decode incoming JSON object
    $inData = getRequestInfo();
    
    //extract data
    $userID = $inData["userID"];
    
    //return data
    $results = "";
    $dogCount = 0;
    
    //establish connection to remote database
    $conn = new mysqli("localhost", "poopspg2_user_pp", "FYPykYr~@7T!", "poopspg2_PuppyPals");
    
    //check if connection was successful
    if($conn->connect_error)
    {
        returnWithError( "$conn->connect_error" );
    }
    else
    {
        //get the user's settings
        //default settings
        $genderSeeking = "Both";
        $radiusSeeking = 10;
        $sizeSeeking = 1;
        
        $sql = "SELECT sizeSeeking,radiusSeeking,genderSeeking FROM settings where userID=" . $userID;
        $settings_query = $conn->query($sql);
        if($settings_query->num_rows>0)
        {
            $settings = $settings_query->fetch_assoc();
            $genderSeeking = $settings["genderSeeking"];
            $radiusSeeking = $settings["radiusSeeking"];
            $sizeSeeking = $settings["sizeSeeking"];
        }
        
        //generate a pool of potential matches
        if($genderSeeking != "Both")
        {
            $sql = "SELECT user.userID, dog.dName, dog.size, dog.breed, dog.age, dog.bio, dog.gender FROM user INNER JOIN dog ON user.userID=dog.userID AND dog.gender='" . $genderSeeking . "' AND dog.size=" . $sizeSeeking . " AND user.userID<>" . $userID;
        }
        else
        {
            $sql = "SELECT user.userID, dog.dName, dog.size, dog.breed, dog.age, dog.bio, dog.gender FROM user INNER JOIN dog ON user.userID=dog.userID AND dog.size=" . $sizeSeeking . " AND user.userID<>" . $userID;
        }
        $dogs_query = $conn->query($sql);
        
        if($dogs_query->num_rows > 0)
        {
            //loop until out of dogs or 5 dogs found
            while($dog = $dogs_query->fetch_assoc()){
                if($dogCount > 4)
                {
                    break;
                }
                
                //get dog information
                $otherID = $dog["userID"];
                $dName = $dog["dName"];
                $size = $dog["size"];
                $breed = $dog["breed"];
                $age = $dog["age"];
                $bio = $dog["bio"];
                $gender = $dog["gender"];
                $imgDog = $dog["imgDog"];
                
                //check that the user has not liked this dog before
                $sql = "SELECT userID FROM likes WHERE userID=" . $userID . " AND other=" . $otherID;
                $like_check = $conn->query($sql);
                
                if($like_check->num_rows > 0)
                {
                    //this dog have been liked before, continue
                    continue;
                }
                
                //dog have not been liked before
                //add to JSON object containing results
                if($dogCount > 0)
                {
                    $results .= ",";
                }
                $dogCount++;
                $results .= '{' . $otherID . ',"' . $dName . '",' . $size . ',"' . $breed . '",' . $age . ',"' . $bio . '","' . $gender . '","' . $imgDog . '"}';
            }
            
            
            $conn->close();
            
            returnWithInfo($results);
        }
        else
        {
            $conn->close();
            
            returnWithError( "No New Dogs Found Matching Criteria" );
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
    function returnWithInfo($results)
    {
        $retValue = '{"results":[' . $results . '],"error":""}';
        sendResultInfoAsJson( $retValue );
    }
    
    ?>


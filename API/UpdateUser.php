<?php
    //decode incoming JSON object
    $inData = getRequestInfo();
    
    //extract data
    $userID = $inData["userID"];
    
    //user data
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $username = $inData["username"];
    $password = $inData["password"];
    $email = $inData["email"];
    $matchMessage = $inData["matchMessage"];
    
    //dog data
    $age = $inData["age"];
    $bio = $inData["bio"];
    $breed = $inData["breed"];
    $dName= $inData["dName"];
    $gender = $inData["gender"];
    $imgDog = $inData["imgDog"];
    $size = $inData["size"];
    
    //settings data
    $genderSeeking = $inData["genderSeeking"];
    $radiusSeeking = $inData["radiusSeeking"];
    $sizeSeeking = $inData["sizeSeeking"];

    //establish connection to remote database
    $conn = new mysqli("localhost", "poopspg2_user_pp", "FYPykYr~@7T!", "poopspg2_PuppyPals");
    
    //check if connection was successful
    if($conn->connect_error)
    {
        returnWithError( "$conn->connect_error" );
    }
    else
    {
        //hash the password
        $salted = "alk3245jylj345j345g34fg6532l1".$password."lksdfjklerwwej23g42g34g234g32";

        $hashed = hash('sha512', $salted);

        //update the info in the database
        $sql = "UPDATE user SET firstName='" . $firstName . "', lastName='" . $lastName . "', username='" . $username . "', password='" . $hashed . "', email='" . $email . "', matchMessage=" . '"' . $matchMessage . '"' . " WHERE userID=" . $userID;

        if($update_query = $conn->query($sql))
        {
            $sql = "UPDATE dog SET age='" . $age . "', bio=" . '"' . $bio . '"' . ", breed='" . $breed . "', dName='" . $dName . "', gender='" . $gender . "', imgDog='" . $imgDog. "', size='" . $size. "' WHERE userID=" . $userID;
            
            if($update_query = $conn->query($sql))
            {
                
                $sql = "UPDATE settings SET genderSeeking='" . $genderSeeking . "', radiusSeeking='" . $radiusSeeking. "', sizeSeeking='" . $sizeSeeking . "' WHERE userID=" . $userID;
                
                if($update_query = $conn->query($sql))
                {
                    $conn->close();
                    
                    returnWithInfo(1);
                }
                else
                {
                    $conn->close();
                    
                    returnWithError( "Error updating settings table" );
                }
            }
            else
            {
                $conn->close();
                
                returnWithError( "Error updating dog table" );
            }
        }
        else
        {
            $conn->close();
            
            returnWithError( "Error updating user table" );
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
        $retValue = '{"success": 0,"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
    
    //sends a JSON object back containing result information
    function returnWithInfo($success)
    {
        $retValue = '{"success":' . $success . ',"error":""}';
        sendResultInfoAsJson( $retValue );
    }
    
?>

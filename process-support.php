<?php
//Shahad Aldawsari
//This file contains our $conn variable so we can talk to MySQL.
include "db_connect.php";

//Security check: Did they actually click the "Submit" button? 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //Grab all the info the user typed into the HTML form
    $name = $_POST["full_name"];
    $email = $_POST["email"];
    $issue = $_POST["issue_type"];
    $message = $_POST["message"];

    //  AUTOMATIC TRANSLATION API 

    //Set up the URL for the MyMemory Translation API.
    // We use urlencode() so spaces and special characters in the message don't break the web link.
    // 'langpair=en|ar' tells the API to translate from English to Arabic.
    $apiURL = "https://api.mymemory.translated.net/get?q=" . urlencode($message) . "&langpair=en|ar";

    //send the request to the API and wait for the JSON response
    $jsonResponse = file_get_contents($apiURL);

    //Check if the API actually replied back to us
    if ($jsonResponse !== false) {
        // Turn the JSON string into an array that PHP can easily read
        $translationData = json_decode($jsonResponse, true);

        // search in the array to find the exact translated text
        if (isset($translationData["responseData"]["translatedText"])) {
            $translatedMessage = $translationData["responseData"]["translatedText"];
        } else {
            //in case the API gives us weird data
            $translatedMessage = "Translation not available.";
        }
    } else {
        //if the API is completely down or offline
        $translatedMessage = "Translation failed.";
    }

    //Combine the original English message and the new Arabic translation into one big string
    $finalMessage = "Original: " . $message . " | Translated (AR): " . $translatedMessage;

    // DATABASE SAVING

    //Write the SQL instruction to save our data into the 'support_messages' table
    $sql = "INSERT INTO support_messages (name, email, subject, message) 
            VALUES ('$name', '$email', '$issue', '$finalMessage')";

    //execute the SQL command
    if (mysqli_query($conn, $sql)) {
        
        // Fixing dead end: Redirects the user back to the support page to prevent a blank screen
        header("Location: support.php?success=1");
        
        exit();
        
    } else {
//Printing the exact database error to the screen for programmer knowing, not real user
        echo "Error: " . mysqli_error($conn);
    }
}
?>
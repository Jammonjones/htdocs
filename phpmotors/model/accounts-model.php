<?php

// This is the accounts model


// This function will handle site registrations
function regClient($clientFirstname, $clientLastname, $clientEmail, $clientPassword){
    // Create a db connection object using the function that we made to connect to our php motors db
    $db = phpmotorsConnect();
    // The SQL statement
    $sql = "INSERT INTO clients (clientFirstname, clientLastname, clientEmail, clientPassword)
        Values (:clientFirstname, :clientLastname, :clientEmail, :clientPassword)";
    // create the prepared statement using the phpmotors connection object
    $stmt = $db->prepare($sql);
    // These next 4 lines add actual values into the prepared sql statement and tell the data base what datatype they are to help control data coming into the database
    $stmt->bindValue(":clientFirstname", $clientFirstname, PDO::PARAM_STR);
    $stmt->bindValue(":clientLastname", $clientLastname, PDO::PARAM_STR);
    $stmt->bindValue(':clientEmail', $clientEmail, PDO::PARAM_STR);
    $stmt->bindValue(':clientPassword', $clientPassword, PDO::PARAM_STR);
    // Now use the sql prepared statement object to execute the statement with the info that it contains
    $stmt->execute();
    // ask how many rows changed as a result of our insert
    $rowsChanged = $stmt->rowCount();
    // close the database interaction
    $stmt->closeCursor();
    // Return the indication of success (rows changed)
    return $rowsChanged;
}

// This function will check if entered client email already exists
function checkIfEmailExists($email){
    // Create a DB connection object using the function that we made to create it with the right info for php motors
    $PDO = phpmotorsConnect();
    // create a sql prepared statement and store it in a variable to prevent db injection attacks
    $sql = "SELECT clientEmail FROM clients WHERE clientEmail = :email";
    $stmt = $PDO->prepare($sql);
    $stmt->bindValue(":email", $email);
    $stmt->execute();
    $existingEmail = $stmt->fetch(PDO::FETCH_NUM);
    $stmt->closeCursor();
    if(empty($existingEmail)){
        return 0;
    } else{
        return 1;
    }
}

// Get client data based on an email address
function getClient($clientEmail){
    $PDO = phpmotorsConnect();
    $sql = "SELECT clientId, clientFirstname, clientLastname, clientEmail, clientLevel, clientPassword FROM clients WHERE clientEmail = :clientEmail";
    $stmt = $PDO->prepare($sql);
    $stmt->bindValue(":clientEmail", $clientEmail);
    $stmt->execute();
    $clientData = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); 
    return $clientData;
}

// Get client data based on id
function getClientById($clientId){
    $PDO = phpmotorsConnect();
    $sql = "SELECT clientId, clientFirstname, clientLastname, clientEmail, clientLevel, clientPassword FROM clients WHERE clientId = :clientId";
    $stmt = $PDO->prepare($sql);
    $stmt->bindValue(":clientId", $clientId);
    $stmt->execute();
    $clientData = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); 
    return $clientData;
}

// Update Client Information
function processAccountUpdate($clientId, $clientFirstname, $clientLastname, $clientEmail){
    // Create a db connection object using the function that we made to connect to our php motors db
    $db = phpmotorsConnect();
    // The SQL statement
    $sql = "UPDATE clients SET clientFirstname = :clientFirstname, clientLastname = :clientLastname, clientEmail = :clientEmail WHERE clientId = :clientId";
    // create the prepared statement object using the phpmotors connection object
    $stmt = $db->prepare($sql);
    // These next 4 lines add actual values into the prepared sql statement and tell the data base what datatype they are to help control data coming into the database
    $stmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
    $stmt->bindValue(":clientFirstname", $clientFirstname, PDO::PARAM_STR);
    $stmt->bindValue(":clientLastname", $clientLastname, PDO::PARAM_STR);
    $stmt->bindValue(':clientEmail', $clientEmail, PDO::PARAM_STR);
    // Now use the sql prepared statement object to execute the statement with the info that it contains
    $stmt->execute();
    // ask how many rows changed as a result of our insert
    $rowsChanged = $stmt->rowCount();
    // close the database interaction
    $stmt->closeCursor();
    // Return the indication of success (rows changed)
    return $rowsChanged;
}

// Update Password with a new hash and return true if successful
function processPasswordUpdate($clientId, $clientPasswordHashed, $clientPassword){
    // create PHP DB connection object
    $PDO = phpmotorsConnect();
    // Create the sql statement to be used in the prepared statement object
    $sql = "UPDATE clients SET clientPassword = :clientPassword WHERE clientId = :clientId";
    // Create database prepared statement object
    $prepStateObj = $PDO->prepare($sql);
    $prepStateObj->bindValue(":clientPassword", $clientPasswordHashed, PDO::PARAM_STR);
    $prepStateObj->bindValue(":clientId", $clientId, PDO::PARAM_INT);
    // execute the update
    $prepStateObj->execute();
    $rowsChanged = $prepStateObj->rowCount();
    $prepStateObj->closeCursor();
    if($rowsChanged && $clientPasswordHashed != $clientPassword){
        return 1;
    }else{
        return 0;
    }

}
?>

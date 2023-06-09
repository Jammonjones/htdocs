<?php
// Vehicles Model

// Get vehicle classification name and Id from database
function getClassificationNameAndId(){
    // create a connection object to connect to the database with
    $db = phpmotorsConnect();
    // The SQL statement to be used with the database
    $sql = "SELECT classificationId, classificationName FROM carclassification ORDER BY classificationName ASC";
    // The next line creates the prepared statement using the phpmotors connection object
    $stmt = $db->prepare($sql);
    // Uses the PDO execute method to execute the prepared sql code string
    $stmt->execute();
    // The next line gets the data from the prepared object and
    // stores it as an array in the $classificationIds variable
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // The next line closes the interaction with the database
    $stmt->closeCursor();
    // The next line sends the array of data back to where the function was called
    // this should be the controller
    return $results;
}

// Insert new car classification
function addCarClassificationName($classificationName){
    $db = phpmotorsConnect();
    $sql = "INSERT INTO carclassification (classificationName)
        VALUES (:classificationName)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(":classificationName", $classificationName, PDO::PARAM_STR);
    $stmt->execute();
    $rowsChanged = $stmt->rowCount();
    $stmt->closeCursor();
    return $rowsChanged;
}

// insert a new vehicle
function addInventory($invMake, $invModel, $invDescription, $invImage, $invThumbnail, $invPrice, $invStock, $invColor, $classificationId){
    $db = phpmotorsConnect();
    $sql = "INSERT INTO inventory (invMake, invModel, invDescription, invImage, invThumbnail, invPrice, invStock, invColor, classificationId)
        VALUES (:invMake, :invModel, :invDescription, :invImage, :invThumbnail, :invPrice, :invStock, :invColor, :classificationId)";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(":invMake", $invMake, PDO::PARAM_STR);
    $stmt->bindValue(":invModel", $invModel, PDO::PARAM_STR);
    $stmt->bindValue(":invDescription", $invDescription, PDO::PARAM_STR);
    $stmt->bindValue(":invImage", $invImage, PDO::PARAM_STR);
    $stmt->bindValue(":invThumbnail", $invThumbnail, PDO::PARAM_STR);
    $stmt->bindValue(":invPrice", $invPrice, PDO::PARAM_STR);
    $stmt->bindValue(":invStock", $invStock, PDO::PARAM_STR);
    $stmt->bindValue(":invColor", $invColor, PDO::PARAM_STR);
    $stmt->bindValue(":classificationId", $classificationId, PDO::PARAM_STR);

    $stmt->execute();
    $rowsChanged = $stmt->rowCount();
    $stmt->closeCursor();
    return $rowsChanged; 
}

// Get vehicles by classificationId
function getInventoryByClassification($classificationId){
    $PDO = phpmotorsConnect();
    $sql = "SELECT * FROM inventory WHERE classificationId = :classificationId";
    $prepStateObj = $PDO->prepare($sql);
    $prepStateObj->bindValue(":classificationId", $classificationId, PDO::PARAM_INT);
    $prepStateObj->execute();
    $inventory = $prepStateObj->fetchAll(PDO::FETCH_ASSOC);
    $prepStateObj->closeCursor();
    return $inventory;
}

// Getting data for one vehicle from the database
// Get vehicle information by invId
function getInvItemInfo($invId){
    $db = phpmotorsConnect();
    // $sql = 'SELECT * FROM inventory WHERE invId = :invId';
    $sql = "SELECT images.invId, images.imgPath, inventory.invId, inventory.invMake, inventory.invModel, inventory.invDescription, inventory.invPrice, inventory.invStock, inventory.invColor, inventory.classificationId FROM images JOIN inventory ON images.invId = inventory.invId WHERE images.invId = :invId AND imgPath LIKE '%.jpg' AND imgPath NOT LIKE '%tn.jpg'";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':invId', $invId, PDO::PARAM_INT);
    $stmt->execute();
    $invInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $invInfo;
   }

// update vehicle information
function updateVehicle($invMake, $invModel, $invDescription, $invImage, $invThumbnail, $invPrice, $invStock, $invColor, $classificationId, $invId){
    $db = phpmotorsConnect();
    $sql = "UPDATE inventory SET invMake = :invMake, invModel = :invModel, 
	invDescription = :invDescription, invImage = :invImage, 
	invThumbnail = :invThumbnail, invPrice = :invPrice, 
	invStock = :invStock, invColor = :invColor, 
	classificationId = :classificationId WHERE invId = :invId";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(":invMake", $invMake, PDO::PARAM_STR);
    $stmt->bindValue(":invModel", $invModel, PDO::PARAM_STR);
    $stmt->bindValue(":invDescription", $invDescription, PDO::PARAM_STR);
    $stmt->bindValue(":invImage", $invImage, PDO::PARAM_STR);
    $stmt->bindValue(":invThumbnail", $invThumbnail, PDO::PARAM_STR);
    $stmt->bindValue(":invPrice", $invPrice, PDO::PARAM_STR);
    $stmt->bindValue(":invStock", $invStock, PDO::PARAM_STR);
    $stmt->bindValue(":invColor", $invColor, PDO::PARAM_STR);
    $stmt->bindValue(":classificationId", $classificationId, PDO::PARAM_STR);
    $stmt->bindValue(':invId', $invId, PDO::PARAM_INT);

    $stmt->execute();
    $rowsChanged = $stmt->rowCount();
    $stmt->closeCursor();
    return $rowsChanged; 
}

// delete selected vehicle
function deleteVehicle($invId){
    $db = phpmotorsConnect();
    $sql = "DELETE FROM inventory WHERE invId = :invId";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':invId', $invId, PDO::PARAM_INT);

    $stmt->execute();
    $rowsChanged = $stmt->rowCount();
    $stmt->closeCursor();
    return $rowsChanged; 
}

// get vehicles based on classificationName and not by classificationId
// this is going to use the relational key from the carclassification as
// a means to filter and select data from the inventory table that also has
// a classificationId that correlates to a classificationName in the carclassification table...
function getInventoryByClassificationName($classificationName){
    $db = phpmotorsConnect();
    // $sql = 'SELECT * FROM inventory WHERE classificationId IN (SELECT classificationId FROM carclassification WHERE classificationName = :classificationName)';
    $sql = 'SELECT * FROM images join inventory ON images.invId = inventory.invId WHERE imgPath LIKE "%tn.jpg" AND classificationId IN (SELECT classificationId FROM carclassification WHERE classificationName = :classificationName AND imgPrimary = 1)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':classificationName', $classificationName, PDO::PARAM_STR);
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $vehicles;
}

// get information for all vehicles for the uploads controller
function getVehicles(){
	$db = phpmotorsConnect();
	$sql = 'SELECT invId, invMake, invModel FROM inventory';
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$invInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	return $invInfo;
}

// get all thumbnail images for a vehicle based on vehicleId to later be used to display in the vehicle-details view
function getVehiclesThumbnails($invId){
    $PDO = phpmotorsConnect();
    $sql = "SELECT imgPath FROM images WHERE invId = :invId AND imgPath LIKE '%tn.jpg'";
    $PDOPSO = $PDO->prepare($sql);
    $PDOPSO->bindValue(":invId", $invId);
    $PDOPSO->execute();
    $extraVehicleImages = $PDOPSO->fetchAll(PDO::FETCH_ASSOC);
    $PDOPSO->closeCursor();
    return $extraVehicleImages;
}
?>
<?php 

require "config/db.php";

$sql = "select  *  from products ";
$result = $mysqli->query($sql);
// print_r($result);
foreach($result as $dt){
    echo "status is : " . $dt['status'];
}


// $row = $result->fetch_assoc();

// foreach($row as $dt){
//     echo "status is : " . $dt['status'];
// }
?>
<?php

$conn = mysqli_connect('localhost', 'root', '', 'vegetable_store') or die("Couldn't connect to database");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>

<?php
/*
$conn = new mysqli('localhost', 'root', '', 'dbvidlist', 3306);

if($conn)
    $conn->set_charset('utf8');
*/

$conn = new mysqli('HOST', 'LOGIN', 'PASS', 'DB', 3306);

if ($conn) {
    $conn->set_charset('utf8mb4');
} else {
    echo alert('error', 'We are sorry, we have some troubles with our database, please wait and come back later...', 'Error:(');
}
?>
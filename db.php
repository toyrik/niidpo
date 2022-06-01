<?php
$host = 'localhost'; // хост
$username = 'root'; // пользователь бд
$passwd = 'noname'; // пароль к бд
$dbname = 'niidpo'; // бд

// Create connection
$conn = new mysqli($host, $username, $passwd, $dbname);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

function mysql2_query($conn, $query)
{

    $query = htmlspecialchars($query);

    $result = $conn->real_query($query);
    return $conn->store_result($result);
}

function mysql2_num_rows($result)
{
   return mysqli_num_rows($result);
}

function mysql2_fetch_assoc($result)
{
    return mysqli_fetch_assoc($result);
}
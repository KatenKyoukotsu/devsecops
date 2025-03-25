<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['name'])) {
    $name = $_GET['name'];
    echo "Hello, " . $name;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>XSS Example</title>
</head>
<body>
    <form method="GET" action="">
        Name: <input type="text" name="name"><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['file'])) {
    $filename = $_GET['file'];
    if (file_exists($filename)) {
        echo file_get_contents($filename);
    } else {
        echo "File not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Viewer</title>
</head>
<body>
    <form method="GET" action="">
        File: <input type="text" name="file"><br>
        <input type="submit" value="View">
    </form>
</body>
</html>
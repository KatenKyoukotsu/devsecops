<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip'])) {
    $ip = $_POST['ip'];
    $output = shell_exec("ping " . $ip);
    echo "<pre>$output</pre>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ping Test</title>
</head>
<body>
    <form method="POST" action="">
        IP Address: <input type="text" name="ip"><br>
        <input type="submit" value="Ping">
    </form>
</body>
</html>
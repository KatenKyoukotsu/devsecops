<?php
class User {
    public $username;

    public function __destruct() {
        if ($this->username === "admin") {
            echo "You are logged in as admin!";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
    $data = $_POST['data'];
    unserialize($data);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insecure Deserialization</title>
</head>
<body>
    <form method="POST" action="">
        Data: <textarea name="data"></textarea><br>
        <input type="submit" value="Deserialize">
    </form>
</body>
</html>
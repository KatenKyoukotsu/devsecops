<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    
    if ($password === "password123") {
        $_SESSION['logged_in'] = true;
        echo "Login successful!";
    } else {
        echo "Invalid password!";
    }
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo "Welcome to the secure area!";
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Weak Auth</title>
    </head>
    <body>
        <form method="POST" action="">
            Password: <input type="password" name="password"><br>
            <input type="submit" value="Login">
        </form>
    </body>
    </html>
    <?php
}
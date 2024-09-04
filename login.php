<?php
include 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Prepare SQL statement
    $sql = "SELECT * FROM account WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    
    // Execute the query
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        session_start();
        $_SESSION['password'] = $user['password'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit();
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid username or password'); window.location.href='index.php';</script>";
    }
}
?>

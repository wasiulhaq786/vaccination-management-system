<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        
        // Direct Database Connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "vaccination_db"; // Apne database ka sahi naam confirm kar lein

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            echo "<script>alert('Connection Failed: " . $conn->connect_error . "'); window.location.href='contact.php';</script>";
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Thank you! Your message has been sent successfully.');
                    window.location.href = 'contact.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Something went wrong. Please try again.');
                    window.location.href = 'contact.php';
                  </script>";
        }

        $stmt->close();
        $conn->close();

    } else {
        echo "<script>
                alert('Please fill in all the required fields.');
                window.location.href = 'contact.php';
              </script>";
    }
} else {
    header("Location: contact.php");
    exit();
}
?>
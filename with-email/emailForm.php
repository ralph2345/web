<?php
session_start();
require 'config.php'; // Include your database connection

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'config2.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Set fixed values for subject and message
$default_subject = "Thank You For Your Purchase!";
$default_message = "<p>Dear Students,</p>"
                 . "<p>Thank you for your recent purchase. Your support means a lot to us.</p>"
                 . "<p>We appreciate your contribution to STI College Balagtas.</p>"
                 . "<p>Ma'am Bernie<br>STI College Balagtas</p>"
                 . "<p><strong>NOTE:</strong> Please download the receipt below for claiming the item.</p>";





function sendMail($email, $subject, $message, $file) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = MAILHOST;
        $mail->Username = USERNAME;
        $mail->Password = PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(SEND_FROM, SEND_FROM_NAME);
        $mail->addAddress($email);
        $mail->addReplyTo(REPLY_TO, REPLY_TO_NAME);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);

        // Check if a file is uploaded
        if ($file && $file['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($file['tmp_name'], $file['name']);
        }

        $mail->send();
        return "Success";
    } catch (Exception $e) {
        return "Email not sent. Error: " . $mail->ErrorInfo;
    }
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $file = $_FILES['image'];

    $_SESSION['email'] = $email;
    $_SESSION['subject'] = $subject;
    $_SESSION['message'] = $message;

    if (empty($email) || empty($subject) || empty($message)) {
        $_SESSION['response'] = "All fields are required";
    } else {
        $_SESSION['response'] = sendMail($email, $subject, $message, $file);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Initialize response and form data variables
$response = isset($_SESSION['response']) ? $_SESSION['response'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$subject = isset($_SESSION['subject']) ? $_SESSION['subject'] : $default_subject;
$message = isset($_SESSION['message']) ? $_SESSION['message'] : $default_message;

// Clear session variables after using them
unset($_SESSION['response'], $_SESSION['email'], $_SESSION['subject'], $_SESSION['message']);


// Determine if an admin is logged in
$is_admin = false;
if (isset($_SESSION['admin_name'])) {
    $logged_in_user = $_SESSION['admin_name'];
    $is_admin = true;
} else {
    $logged_in_user = 'Guest';
}

$logged_in_user = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email - Quantify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="HomePage.css">
    <link rel="stylesheet" href="emailForm.css">
</head>
<body>
<header>
    <div class="header-left">
        <div>Welcome, <?php echo htmlspecialchars($logged_in_user); ?></div>
    </div>
</header>
<div class="container">
        <aside class="sidebar" id="sidebar">
            <button class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-list"></i></button> 
            <h2 id="menuHeader">Menu</h2>
            <ul>
                <?php if ($is_admin) : ?>
                <li><a href="Dashboard.php"><i class="bi bi-person-video"></i> <span>Dashboard</span></a></li>
                <li><a href="Customer.php"><i class="bi bi-people"></i> <span>Customers</span></a></li>
                <li><a href="Product.php"><i class="bi bi-box2"></i> <span>Products</span></a></li>
                <li><a href="Order.php"><i class="bi bi-bag-check"></i> <span>Orders</span></a></li>
                <li><a href="emailForm.php"><i class="bi bi-envelope"></i> <span>Mail</span></a></li>
                <li><a href="logout.php" id="logoutLink"><i class="bi bi-box-arrow-left"></i> <span>Sign Out</span></a></li>
                <?php else : ?>
                <li><a href="Product.php"><i class="bi bi-box2"></i> <span>Products</span></a></li>
                <li><a href="Cart.php"><i class="bi bi-bag-check"></i> <span>Cart</span></a></li>
                <li><a href="logout.php" id="exitLink"><i class="bi bi-box-arrow-left"></i> <span>Exit</span></a></li>
                <?php endif; ?>
            </ul>
        </aside>
        <main class="main-content">
                <h2> Send Email</h2>
                <div class="form-wrapper">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group email-group">
                            <label for="email">Enter email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?> " required>
                        </div>
                        <div class="form-group subject-group">
                            <label for="subject">Enter subject</label>
                            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?> " required>
                        </div>
                        <div class="form-group image-group">
                            <label for="image">Upload file</label>
                            <input type="file" id="image" name="image">
                        </div>
                        <div class="form-group message-group">
                            <label for="message">Enter message</label>
                            <textarea id="message" name="message" rows="10" required><?php echo htmlspecialchars($message); ?>></textarea>
                        </div>
                        <button type="submit" class="submit-button" name="submit">Submit</button>

                        <div class="email-message">
                            <?php if ($response == "Success") { ?>
                                <p class="success">Email sent successfully</p>
                            <?php } elseif ($response) { ?>
                                <p class="error"><?php echo htmlspecialchars($response); ?></p>
                            <?php } ?>
                        </div>
                    </form>
                </div>
                     
        </main>
</div>
<footer>
    <p>&copy; 2024 <a href="#" style="color: white;">Quantify</a>. All rights reserved.</p>
</footer>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('minimized');
        const menuHeader = document.getElementById('menuHeader');
        menuHeader.style.display = sidebar.classList.contains('minimized') ? 'none' : 'inline';
    }

    document.getElementById('logoutLink').addEventListener('click', function(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to sign out?')) {
            window.location.href = this.href;
        }
    });
</script>
</body>
</html>

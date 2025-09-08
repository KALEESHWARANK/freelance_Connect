<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Composer autoload
require '../vendor/autoload.php';

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "freelance_connect";
$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mail function
function sendMail($toEmail, $subject, $bodyContent, $client_email) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kaleesh.kaleeeh@gmail.com';  // Your email
        $mail->Password   = 'akwe uitf wiys sxxj';        // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('kaleesh.kaleeeh@gmail.com', 'freelance_connect');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyContent;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'], $_POST['email'], $_POST['action'])) {
        $application_id = (int)$_POST['id'];
        $freelancer_email = trim($_POST['email']);
        $action = $_POST['action'];

        // Get client email from DB
        $clientEmailQuery = "
            SELECT u.email AS client_email
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            JOIN users u ON j.client_id = u.id
            WHERE a.id = ?
        ";
        $stmt = $conn->prepare($clientEmailQuery);
        $stmt->bind_param("i", $application_id);
        $stmt->execute();
        $emailResult = $stmt->get_result();
        $client_email = '';
        if ($emailResult->num_rows > 0) {
            $client_email = $emailResult->fetch_assoc()['client_email'];
        }

        if ($action === 'accept') {
            $subject = "🎉 Application Accepted - Freelance Connect";
            $body = "
                Hello,<br><br>
                Congratulations! Your application has been <b>Accepted</b>.<br><br>
                ✅ Please send your <b>ID proof</b> to: <b>$client_email</b><br>
                ✅ Start working on the project and make sure to <b>submit it within the deadline</b>.<br>
                ❓ For any questions, feel free to contact the client via email.<br><br>
                All the best!<br>
                <b>Freelance Connect</b>
            ";

            if (sendMail($freelancer_email, $subject, $body, $client_email)) {
                $sql = "UPDATE applications SET status='Accepted' WHERE id=$application_id";
                if ($conn->query($sql)) {
                    header("Location: ../frontend/view_applicants.php?msg=accepted");
                } else {
                    echo "Database error while accepting.";
                }
            } else {
                echo "Failed to send acceptance email.";
            }

        } elseif ($action === 'reject') {
            $subject = "❌ Application Rejected - Freelance Connect";
            $body = "
                Hello,<br><br>
                We’re sorry to inform you that your application has been <b>Rejected</b>.<br>
                Thank you for your interest. We wish you the best for future opportunities.<br><br>
                Regards,<br>
                <b>Freelance Connect</b>
            ";

            if (sendMail($freelancer_email, $subject, $body, $client_email)) {
                $sql = "DELETE FROM applications WHERE id=$application_id";
                if ($conn->query($sql)) {
                    echo "Application rejected and email sent.";
                } else {
                    echo "Database error while rejecting.";
                }
            } else {
                echo "Failed to send rejection email.";
            }

        } else {
            echo "Invalid action.";
        }

    } else {
        echo "Missing required fields.";
    }
} else {
    echo "Invalid request.";
}
?>

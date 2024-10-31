<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include Composer's autoloader
require 'vendor/autoload.php';

date_default_timezone_set('Asia/Kolkata');

$hostname = "localhost";
$username = "root";
$password = "";
$database = "sensor_db";

// Establish a connection to the database
$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connection established.<br>";

// Step 1: Set all records' warning_sent column to 0
$reset_sql = "UPDATE logs SET warning_sent = 0";
if ($conn->query($reset_sql) === TRUE) {
    echo "All warning_sent columns reset to 0.<br>";
} else {
    echo "Error resetting warning_sent column.<br>";
}

// Step 2: Determine current date and set deadline time
$current_time = new DateTime();
$deadline_time = new DateTime('2024-10-27 01:37:00');

echo "Current time: " . $current_time->format('Y-m-d H:i:s') . "<br>";
echo "Deadline time: " . $deadline_time->format('Y-m-d H:i:s') . "<br>";

$check_time = clone $current_time;
$check_time->add(new DateInterval('PT1H')); // Add 1 hour

$check_time_str = $check_time->format('Y-m-d H:i:s');
echo "Check time string: $check_time_str<br>";

$sql = "SELECT l.id, l.name, l.deadline_time, s.email 
        FROM logs l
        JOIN students s ON l.uid = s.uid
        WHERE l.warning_sent = FALSE AND l.deadline_time <= ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $check_time_str);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Records found: " . $result->num_rows . "<br>";
    while ($row = $result->fetch_assoc()) {
        $student_name = $row['name'];
        $deadline_time_str = $row['deadline_time']; // Get the deadline time as string
        $email = $row['email'];
        $log_id = $row['id'];

        echo "Processing student: $student_name with email: $email and deadline_time: $deadline_time_str<br>";

        // Convert string to DateTime for comparison
        $deadline_time = new DateTime($deadline_time_str);

        // Only send warnings if the deadline is approaching
        if ($current_time < $deadline_time) {
            $email_sent = sendWarningEmail($student_name, $email, $deadline_time_str); // Use string for email

            if ($email_sent) {
                $update_sql = "UPDATE logs SET warning_sent = TRUE WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('i', $log_id);
                $update_stmt->execute();
                echo "Log updated for student: $student_name<br>";
            } else {
                echo "Failed to send email to student: $student_name<br>";
            }
        }
    }
} else {
    echo "No records found.<br>";
}

$stmt->close();

// Check all warning_sent column values
$check_warning_sent_sql = "SELECT id, warning_sent FROM logs";
$check_warning_sent_stmt = $conn->query($check_warning_sent_sql);

if ($check_warning_sent_stmt) {
    while ($row = $check_warning_sent_stmt->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Warning Sent: " . $row['warning_sent'] . "<br>";
    }
} else {
    echo "Error retrieving warning_sent values: " . $conn->error . "<br>";
}

$conn->close();

function sendWarningEmail($student_name, $email, $deadline_time) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kaushikkumar2373@gmail.com'; // Your Gmail address
        $mail->Password = 'ezql ojsy phme amdw';      // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('kaushikkumar2373@gmail.com', 'College Entry System');
        $mail->addAddress($email, $student_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Warning: Entry Deadline Approaching';
        $mail->Body    = "Dear $student_name,<br><br>You have only 1 hour left to enter the college. Your entry deadline is at " . $deadline_time . ".<br><br>Please make sure to enter the college before the deadline.<br><br>Thank you.";

        $mail->send();
        echo 'Email has been sent to ' . $email . '<br>';
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent to $email. Mailer Error: {$mail->ErrorInfo}<br>";
        return false;
    }
}
?>

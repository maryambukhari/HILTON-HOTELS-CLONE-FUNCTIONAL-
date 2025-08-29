<?php 
include 'db.php'; 
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guest_name = $_POST['guest_name'];
    $guest_email = $_POST['guest_email'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    // Check availability again
    $sql = "SELECT COUNT(*) as count FROM bookings WHERE room_id = ? AND NOT (check_out <= ? OR check_in >= ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $room_id, $check_in, $check_out);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        // Book it
        $insert_sql = "INSERT INTO bookings (room_id, guest_name, guest_email, check_in, check_out) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("issss", $room_id, $guest_name, $guest_email, $check_in, $check_out);
        if ($insert_stmt->execute()) {
            echo '<script>alert("Booking confirmed! Thank you."); window.location.href = "index.php";</script>';
        } else {
            echo '<script>alert("Error booking. Please try again.");</script>';
        }
    } else {
        echo '<script>alert("Room is no longer available for these dates.");</script>';
    }
    $conn->close();
    exit;
}

// Fetch room details
$sql = "SELECT h.name, r.type, r.price FROM rooms r JOIN hotels h ON r.hotel_id = h.id WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hilton Hotels Clone - Book Room</title>
    <style>
        /* Amazing, colorful, real-looking CSS with effects */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #e6f2ff, #ffffff);
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background: #003b95;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .booking-form {
            margin: 40px auto;
            width: 80%;
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .booking-form:hover {
            transform: scale(1.02);
        }
        .booking-form input {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        .booking-form input:focus {
            border-color: #003b95;
        }
        .booking-form button {
            width: 100%;
            padding: 12px;
            background: #003b95;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .booking-form button:hover {
            background: #002b6f;
        }
        /* Responsiveness */
        @media (max-width: 600px) {
            .booking-form {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Book Your Stay at <?php echo $room ? htmlspecialchars($room['name'] . ' - ' . $room['type']) : 'Room'; ?></h1>
    </header>
    <?php if ($room): ?>
    <div class="booking-form">
        <form method="POST">
            <input type="text" name="guest_name" placeholder="Your Name" required>
            <input type="email" name="guest_email" placeholder="Your Email" required>
            <input type="date" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>" required>
            <input type="date" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>" required>
            <p>Price per night: $<?php echo $room['price']; ?></p>
            <button type="submit">Confirm Booking</button>
        </form>
    </div>
    <?php else: ?>
    <p>Invalid room selected.</p>
    <?php endif; ?>
    <?php $conn->close(); ?>
</body>
</html>

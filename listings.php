<?php 
include 'db.php'; 
$location = isset($_GET['location']) ? $_GET['location'] : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$price_min = isset($_GET['price_min']) ? $_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? $_GET['price_max'] : 10000;
$rating_min = isset($_GET['rating_min']) ? $_GET['rating_min'] : 0;
$amenities = isset($_GET['amenities']) ? $_GET['amenities'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc';

$order_by = 'r.price ASC';
if ($sort == 'price_desc') $order_by = 'r.price DESC';
if ($sort == 'rating_desc') $order_by = 'h.rating DESC';

// Function to check room availability
function is_available($conn, $room_id, $check_in, $check_out) {
    $sql = "SELECT COUNT(*) as count FROM bookings WHERE room_id = ? AND NOT (check_out <= ? OR check_in >= ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $room_id, $check_in, $check_out);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] == 0;
}

$amenities_where = '';
if ($amenities) {
    $amenities_array = explode(',', $amenities);
    foreach ($amenities_array as $am) {
        $amenities_where .= " AND h.amenities LIKE '%" . $conn->real_escape_string(trim($am)) . "%'";
    }
}

// Query for available rooms
$sql = "SELECT h.id as hotel_id, h.name, h.location, h.rating, h.amenities, r.id as room_id, r.type, r.price, r.image, r.description 
        FROM hotels h 
        JOIN rooms r ON h.id = r.hotel_id 
        WHERE h.location LIKE '%" . $conn->real_escape_string($location) . "%' 
        AND r.price BETWEEN $price_min AND $price_max 
        AND h.rating >= $rating_min 
        $amenities_where 
        ORDER BY $order_by";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hilton Hotels Clone - Hotel Listings</title>
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
        .filters {
            margin: 20px auto;
            width: 80%;
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .filters form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .filters input, .filters select, .filters button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .filters button {
            background: #003b95;
            color: white;
            cursor: pointer;
        }
        .sort-links {
            text-align: center;
            margin: 10px;
        }
        .sort-links a {
            margin: 0 10px;
            color: #003b95;
            text-decoration: none;
            font-weight: bold;
        }
        .room-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .room-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            overflow: hidden;
            width: 300px;
            transition: transform 0.4s ease;
        }
        .room-card:hover {
            transform: translateY(-10px);
        }
        .room-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .room-card h3 {
            font-size: 1.5em;
            margin: 10px;
            color: #003b95;
        }
        .room-card p {
            margin: 5px 10px;
        }
        .book-btn {
            display: block;
            background: #003b95;
            color: white;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
            transition: background 0.3s;
        }
        .book-btn:hover {
            background: #002b6f;
        }
        /* Responsiveness */
        @media (max-width: 768px) {
            .filters form {
                flex-direction: column;
            }
            .room-grid {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Available Rooms in <?php echo htmlspecialchars($location); ?></h1>
    </header>
    <div class="filters">
        <form method="GET">
            <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
            <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
            <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
            <input type="number" name="price_min" placeholder="Min Price" value="<?php echo $price_min; ?>">
            <input type="number" name="price_max" placeholder="Max Price" value="<?php echo $price_max; ?>">
            <input type="number" name="rating_min" placeholder="Min Rating" step="0.1" value="<?php echo $rating_min; ?>">
            <input type="text" name="amenities" placeholder="Amenities (comma-separated)" value="<?php echo htmlspecialchars($amenities); ?>">
            <select name="sort">
                <option value="price_asc" <?php if($sort=='price_asc') echo 'selected'; ?>>Price Low to High</option>
                <option value="price_desc" <?php if($sort=='price_desc') echo 'selected'; ?>>Price High to Low</option>
                <option value="rating_desc" <?php if($sort=='rating_desc') echo 'selected'; ?>>Best Rated</option>
            </select>
            <button type="submit">Apply Filters</button>
        </form>
    </div>
    <div class="room-grid">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (is_available($conn, $row['room_id'], $check_in, $check_out)) {
                    echo '<div class="room-card">';
                    echo '<img src="' . $row['image'] . '" alt="' . $row['type'] . '">';
                    echo '<h3>' . $row['name'] . ' - ' . $row['type'] . '</h3>';
                    echo '<p>Location: ' . $row['location'] . '</p>';
                    echo '<p>Price: $' . $row['price'] . '/night</p>';
                    echo '<p>Rating: ' . $row['rating'] . '/5</p>';
                    echo '<p>Amenities: ' . $row['amenities'] . '</p>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '<a class="book-btn" href="book.php?room_id=' . $row['room_id'] . '&check_in=' . $check_in . '&check_out=' . $check_out . '">Book Now</a>';
                    echo '</div>';
                }
            }
        } else {
            echo '<p>No available rooms found. Try different filters.</p>';
        }
        $conn->close();
        ?>
    </div>
</body>
</html>

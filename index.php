<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hilton Hotels Clone - Homepage</title>
    <style>
        /* Amazing, colorful, real-looking CSS with effects */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #e6f2ff, #ffffff);
            color: #333;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        header {
            background: #003b95; /* Hilton blue */
            color: white;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        h1 {
            font-size: 3em;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .search-bar {
            margin: 40px auto;
            width: 80%;
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .search-bar:hover {
            transform: scale(1.02);
        }
        .search-bar form {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-bar input, .search-bar button {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .search-bar input:focus, .search-bar button:hover {
            border-color: #003b95;
        }
        .search-bar button {
            background: #003b95;
            color: white;
            cursor: pointer;
        }
        .featured {
            padding: 40px 20px;
            text-align: center;
        }
        .featured h2 {
            font-size: 2.5em;
            color: #003b95;
            margin-bottom: 20px;
        }
        .hotel-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .hotel-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            overflow: hidden;
            width: 300px;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .hotel-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .hotel-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .hotel-card h3 {
            font-size: 1.5em;
            margin: 10px;
            color: #003b95;
        }
        .hotel-card p {
            margin: 5px 10px;
            font-size: 1em;
        }
        /* Responsiveness */
        @media (max-width: 768px) {
            .search-bar form {
                flex-direction: column;
                gap: 10px;
            }
            .hotel-grid {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Hilton Hotels</h1>
    </header>
    <div class="search-bar">
        <form action="listings.php" method="GET">
            <input type="text" name="location" placeholder="Destination (e.g., New York)" required>
            <input type="date" name="check_in" required>
            <input type="date" name="check_out" required>
            <button type="submit">Search Hotels</button>
        </form>
    </div>
    <div class="featured">
        <h2>Featured Hotels & Top-Rated Stays</h2>
        <div class="hotel-grid">
            <?php
            $sql = "SELECT * FROM hotels ORDER BY rating DESC LIMIT 3";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="hotel-card">';
                    echo '<img src="' . $row['image'] . '" alt="' . $row['name'] . '">';
                    echo '<h3>' . $row['name'] . '</h3>';
                    echo '<p>Location: ' . $row['location'] . '</p>';
                    echo '<p>Rating: ' . $row['rating'] . '/5</p>';
                    echo '<p>Amenities: ' . $row['amenities'] . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>No featured hotels available.</p>';
            }
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>in

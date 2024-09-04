<?php
include 'config.php';

// Initialize variables
$events = [];

// Prepare PDO statement to fetch all event details and image data
try {
    $stmt = $pdo->query("SELECT event_name, event_date, location, description, organizer_name, image_data FROM events");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching events: " . $e->getMessage();
}

// Close the database connection
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event list</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        body {
            background-color:#F5F5F5
        }
        .carousel-item img {
            height: 500px;
            object-fit: cover;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
            border-radius: 15px;
        }
        .navbar {
            background-color: #55679C;
            padding: 20px;
        }
        .navbar-brand, .nav-link {
            color: white !important;
            font-size: 20px;
            font-weight: bold;
        }
        .nav-link:hover {
            color: #7C93C3 !important;
        }
        .nav-link.active {
            color: #7C93C3 !important;
        }
        .nav-link {
            margin-right: 20px;
        }
        h1 {
            text-align: center;
            color:#55679C;
        }
        h2 {
            font-size:20px;
        }
        .event-item {
            margin-bottom: 20px;
            color:#424242;
        }
        .btn-login {
            background: #7C93C3 ;
            color: white;
            border: none;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s, box-shadow 0.3s;
        }

        .btn-login:hover {
            background: linear-gradient(90deg, #F5F5F5 0%, #7C93C3 100%);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-login:focus {
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Event-list</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Event</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About Us</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="btn btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">LOGIN</button>
                    </li>
                </ul>    
            </div>
        </div>
    </nav>
<H1>ALL EVENTS COMMINGS</H1>
    <div class="container mt-4">
        <?php if (!empty($events)) : ?>
            <?php foreach ($events as $event) : ?>
                <div class="event-item">
                    <div id="carouselExampleRide<?php echo $event['event_name']; ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                            // Display image if available
                            if (!empty($event['image_data'])) {
                                echo '<div class="carousel-item active">';
                                echo '<img src="data:image/jpeg;base64,' . base64_encode($event['image_data']) . '" class="d-block w-100" alt="Event Image">';
                                echo '</div>';
                            } else {
                                echo '<div class="carousel-item active">';
                                echo '<img src="path/to/default-image.jpg" class="d-block w-100" alt="Default Image">';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <h1><?php echo htmlspecialchars($event['event_name']); ?></h1>
                    <h2>Date: <?php echo htmlspecialchars($event['event_date']); ?></h2>
                    <h2>Location: <?php echo htmlspecialchars($event['location']); ?></h2>
                    <h2>Organizer Name: <?php echo htmlspecialchars($event['organizer_name']); ?></h2>
                    <p><?php echo htmlspecialchars($event['description']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                    <p class="mt-3">Don't have an account? <a href="register.php">Register here</a></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

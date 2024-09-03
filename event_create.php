<?php
// Database connection
include 'config.php';

// Initialize variables
$event_name = $event_date = $location = $description = $organizer_name = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $organizer_name = $_POST['organizer_name'] ?? '';

    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $image_path = uploadFile($_FILES['event_image']);
    }

    if ($event_name && $event_date && $location && $description && $organizer_name) {
        try {
            $sql = "INSERT INTO events (event_name, event_date, location, description, organizer_name" . ($image_path ? ", image_path" : "") . ") VALUES (?, ?, ?, ?, ?" . ($image_path ? ", ?" : "") . ")";
            $params = [
                htmlspecialchars($event_name),
                htmlspecialchars($event_date),
                htmlspecialchars($location),
                htmlspecialchars($description),
                htmlspecialchars($organizer_name)
            ];
            if ($image_path) {
                $params[] = htmlspecialchars($image_path);
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            header("Location: dashboard.php?status=success&action=create");
            exit;
        } catch (\PDOException $e) {
            echo "Error creating event: " . $e->getMessage();
        }
    } else {
        echo "All fields are required.";
    }
}

// Function to handle file upload
function uploadFile($file) {
    $target_dir = "uploads/";
    $file_name = time() . '_' . basename($file["name"]); // Use a timestamp to avoid file name conflicts
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    if ($file["size"] > 500000) { // 500KB limit
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        return false;
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file; // Return the path relative to the project root
        } else {
            echo "Sorry, there was an error uploading your file.";
            return false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Create New Event</h2>
            <form action="event_create.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="event_name">Event Name:</label>
                    <input type="text" id="event_name" name="event_name" required>
                </div>
                <div class="form-group">
                    <label for="event_date">Event Date:</label>
                    <input type="date" id="event_date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="organizer_name">Organizer Name:</label>
                    <input type="text" id="organizer_name" name="organizer_name" required>
                </div>
                <div class="form-group">
                    <label for="event_image">Event Image:</label>
                    <input type="file" id="event_image" name="event_image" accept="image/*">
                </div>
                <div class="form-group">
                    <button type="submit" class="submit-button">Create Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // JavaScript to handle modal display
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.querySelector('.modal');
            const closeButton = document.querySelector('.close-button');

            closeButton.addEventListener('click', function () {
                modal.style.display = 'none';
            });

            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Display the modal
            modal.style.display = 'block';
        });
    </script>
</body>
</html>
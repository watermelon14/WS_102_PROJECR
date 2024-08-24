<?php
include 'config.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $organizer_name = $_POST['organizer_name'];

    $sql = "UPDATE events SET event_name = ?, event_date = ?, location = ?, description = ?, organizer_name = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$event_name, $event_date, $location, $description, $organizer_name, $id])) {
        echo "Event updated successfully!";
    } else {
        echo "Error: Could not update event.";
    }
} else {
    $sql = "SELECT * FROM events WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
</head>
<body>
    <h1>Edit Event</h1>
    <form method="post" action="">
        <label for="event_name">Event Name:</label><br>
        <input type="text" id="event_name" name="event_name" value="<?= htmlspecialchars($event['event_name']); ?>" required><br>

        <label for="event_date">Event Date:</label><br>
        <input type="date" id="event_date" name="event_date" value="<?= htmlspecialchars($event['event_date']); ?>" required><br>

        <label for="location">Location:</label><br>
        <input type="text" id="location" name="location" value="<?= htmlspecialchars($event['location']); ?>" required><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description"><?= htmlspecialchars($event['description']); ?></textarea><br>

        <label for="organizer_name">Organizer Name:</label><br>
        <input type="text" id="organizer_name" name="organizer_name" value="<?= htmlspecialchars($event['organizer_name']); ?>" required><br>

        <input type="submit" value="Update Event">
    </form>
</body>
</html>
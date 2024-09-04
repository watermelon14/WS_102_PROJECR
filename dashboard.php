<?php
// Database connection
include 'config.php';

session_start();

if(!isset($_SESSION['username']) || !isset($_SESSION['password'])){
   header('location:index.php');
}
$event_name = $event_date = $location = $description = $organizer_name = '';
$id = 0;

// Create Event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $organizer_name = $_POST['organizer_name'] ?? '';

    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        // Get image details
        $image_data = file_get_contents($_FILES['event_image']['tmp_name']);
    } else {
        $image_data = null;
    }

    if ($event_name && $event_date && $location && $description && $organizer_name) {
        try {
            $sql = "INSERT INTO events (event_name, event_date, location, description, organizer_name" . ($image_data ? ", image_data" : "") . ") VALUES (?, ?, ?, ?, ?" . ($image_data ? ", ?" : "") . ")";
            $params = [
                htmlspecialchars($event_name),
                htmlspecialchars($event_date),
                htmlspecialchars($location),
                htmlspecialchars($description),
                htmlspecialchars($organizer_name)
            ];
            if ($image_data) {
                $params[] = $image_data;
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            header("Location: dashboard.php?status=success&action=create");
            exit;
        } catch (PDOException $e) {
            echo "Error creating event: " . $e->getMessage();
        }
    } else {
        echo "All fields are required.";
    }
}

// Edit Event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id'] ?? 0;
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $organizer_name = $_POST['organizer_name'] ?? '';

    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        // Get image details
        $image_data = file_get_contents($_FILES['event_image']['tmp_name']);
    } else {
        $image_data = null;
    }

    if ($id && $event_name && $event_date && $location && $description && $organizer_name) {
        try {
            $sql = "UPDATE events SET event_name = ?, event_date = ?, location = ?, description = ?, organizer_name = ?" . ($image_data ? ", image_data = ?" : "") . " WHERE id = ?";
            $params = [
                htmlspecialchars($event_name),
                htmlspecialchars($event_date),
                htmlspecialchars($location),
                htmlspecialchars($description),
                htmlspecialchars($organizer_name)
            ];
            if ($image_data) {
                $params[] = $image_data;
            }
            $params[] = (int)$id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            header("Location: dashboard.php?status=success&action=edit");
            exit;
        } catch (PDOException $e) {
            echo "Error updating event: " . $e->getMessage();
        }
    } else {
        echo "All fields are required.";
    }
}

// Delete Event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'] ?? 0;
    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([(int)$id]);
            header("Location: dashboard.php?status=success&action=delete");
            exit;
        } catch (PDOException $e) {
            echo "Error deleting event: " . $e->getMessage();
        }
    } else {
        echo "Invalid event ID.";
    }
}

// Fetch events for displaying
try {
    $stmt = $pdo->query("SELECT * FROM events");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching events: " . $e->getMessage();
    $events = [];
}

// Fetch event data for editing
if (isset($_GET['edit_id'])) {
    $id = (int)$_GET['edit_id'];
    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([(int)$id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($event) {
                $event_name = htmlspecialchars($event['event_name']);
                $event_date = htmlspecialchars($event['event_date']);
                $location = htmlspecialchars($event['location']);
                $description = htmlspecialchars($event['description']);
                $organizer_name = htmlspecialchars($event['organizer_name']);
                $image_data = $event['image_data']; // Binary data for image
            }
        } catch (PDOException $e) {
            echo "Error fetching event data: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="styles.css">
    <title>DASHBOARD</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const action = urlParams.get('action');

            if (status === 'success' && action === 'edit') {
                alert('Event updated successfully.');
                window.location.href = 'dashboard.php'; // Refresh page after alert
            }
        });
    </script>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-user'></i>
            <span class="text"><?php echo $_SESSION['username'] ?></span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="deceased-management.html">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Event Management</span>
                </a>
            </li>
           
        </ul>
        <ul class="side-menu">
            <li>
                <a href="logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Event Management</a>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
        </nav>

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Event Management</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Event Management</a></li>
                    </ul>
                </div>
            </div>

            <!-- Event Management Section -->
            <section class="event-management">
                <!-- Add Event Form -->
                <div class="add-event-form">
                    <h2><?php echo $id ? 'Edit Event' : 'Add New Event'; ?></h2>
                    <form id="event-form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $id ? 'edit' : 'create'; ?>">
                        <?php if ($id): ?>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <?php endif; ?>
                        <label for="event_name">Event Name:</label>
                        <input type="text" id="event_name" name="event_name" value="<?php echo $event_name; ?>" required>
                        <label for="event_date">Event Date:</label>
                        <input type="date" id="event_date" name="event_date" value="<?php echo $event_date; ?>" required>
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location" value="<?php echo $location; ?>" required>
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4" required><?php echo $description; ?></textarea>
                        <label for="organizer_name">Organizer Name:</label>
                        <input type="text" id="organizer_name" name="organizer_name" value="<?php echo $organizer_name; ?>" required>
                        <label for="event_image">Event Image:</label>
                        <input type="file" id="event_image" name="event_image" accept="image/*">
                        <button type="submit"><?php echo $id ? 'Update Event' : 'Add Event'; ?></button>
                    </form>
                </div>

                <div class="event-list">
                    <h2>Upcoming Events</h2>
                    <table id="event-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Organizer</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td><?php echo htmlspecialchars($event['description']); ?></td>
                                <td><?php echo htmlspecialchars($event['organizer_name']); ?></td>
								<td>
    <?php if (!empty($event['image_data'])): ?>
        <?php
        // Assuming the image data is stored as binary data
        $base64Image = base64_encode($event['image_data']);
        ?>
        <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($base64Image); ?>" width="100" alt="Event Image" style="border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
    <?php else: ?>
        <span style="color: #777; font-style: italic;">No Image</span>
    <?php endif; ?>
</td>

                                <td>
                                    <!-- Edit Link -->
                                    <a href="?edit_id=<?php echo $event['id']; ?>" class="btn-edit">Edit</a>
                                    <!-- Delete Form -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                        <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- Event Management Section -->
        </main>
    </section>
</body>
</html>


<!-- SIDEBAR SCRIPT -->
<script>
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});




// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})




</script>






 <!-- CSS -->
  <style>
	@import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap');

* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

a {
	text-decoration: none;
}

li {
	list-style: none;
}

:root {
	--poppins: 'Poppins', sans-serif;
	--lato: 'Lato', sans-serif;

	--light: #F9F9F9;
	--blue: #2169c7;
	--light-blue: #CFE8FF;
	--grey: #eee;
	--dark-grey: #AAAAAA;
	--dark: #342E37;
	--red: #DB504A;
	--yellow: #FFCE26;
	--light-yellow: #FFF2C6;
	--orange: #FD7238;
	--light-orange: #FFE0D3;
}

html {
	overflow-x: hidden;
}

body.dark {
	--light: #0C0C1E;
	--grey: #060714;
	--dark: #FBFBFB;
}

body {
	background: var(--grey);
	overflow-x: hidden;
}



/* Dropdown Styles */
.dropdown-menu {
    display: none;
    list-style: none;
    padding: 0;
    margin: 0;
    background: var(--light);
    border-radius: 8px;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: opacity 0.3s ease;
}

.dropdown-menu.show {
    display: block;
    opacity: 1;
}

.dropdown-menu li {
    padding: 12px 20px;
}

.dropdown-menu li a {
    color: var(--dark);
    display: block;
    padding: 8px 0;
    font-size: 14px;
}

.dropdown-menu li a:hover {
    background: var(--grey);
    color: var(--blue);
}


/* SIDEBAR */

#sidebar {
	position: fixed;
	top: 0;
	left: 0;
	width: 280px;
	height: 100%;
	background: var(--light);
	z-index: 2000;
	font-family: var(--lato);
	transition: .3s ease;
	overflow-x: hidden;
	scrollbar-width: none;
}


#sidebar::--webkit-scrollbar {
	display: none;
}
#sidebar.hide {
	width: 60px;
}
#sidebar .brand {
	font-size: 24px;
	font-weight: 700;
	height: 56px;
	display: flex;
	align-items: center;
	color: var(--blue);
	position: sticky;
	top: 0;
	left: 0;
	background: var(--light);
	z-index: 500;
	padding-bottom: 20px;
	box-sizing: content-box;
}
#sidebar .brand .bx {
	min-width: 60px;
	display: flex;
	justify-content: center;
}
#sidebar .side-menu {
	width: 100%;
	margin-top: 48px;
}
#sidebar .side-menu li {
	height: 48px;
	background: transparent;
	margin-left: 6px;
	border-radius: 48px 0 0 48px;
	padding: 4px;
}
#sidebar .side-menu li.active {
	background: var(--grey);
	position: relative;
}
#sidebar .side-menu li.active::before {
	content: '';
	position: absolute;
	width: 40px;
	height: 40px;
	border-radius: 50%;
	top: -40px;
	right: 0;
	box-shadow: 20px 20px 0 var(--grey);
	z-index: -1;
}
#sidebar .side-menu li.active::after {
	content: '';
	position: absolute;
	width: 40px;
	height: 40px;
	border-radius: 50%;
	bottom: -40px;
	right: 0;
	box-shadow: 20px -20px 0 var(--grey);
	z-index: -1;
}
#sidebar .side-menu li a {
	width: 100%;
	height: 100%;
	background: var(--light);
	display: flex;
	align-items: center;
	border-radius: 48px;
	font-size: 16px;
	color: var(--dark);
	white-space: nowrap;
	overflow-x: hidden;
}
#sidebar .side-menu.top li.active a {
	color: var(--blue);
}
#sidebar.hide .side-menu li a {
	width: calc(48px - (4px * 2));
	transition: width .3s ease;
}
#sidebar .side-menu li a.logout {
	color: var(--red);
}
#sidebar .side-menu.top li a:hover {
	color: var(--blue);
}
#sidebar .side-menu li a .bx {
	min-width: calc(60px  - ((4px + 6px) * 2));
	display: flex;
	justify-content: center;
}
/* SIDEBAR */





/* CONTENT */
#content {
	position: relative;
	width: calc(100% - 280px);
	left: 280px;
	transition: .3s ease;
}
#sidebar.hide ~ #content {
	width: calc(100% - 60px);
	left: 60px;
}




/* NAVBAR */
#content nav {
	height: 56px;
	background: var(--light);
	padding: 0 24px;
	display: flex;
	align-items: center;
	grid-gap: 24px;
	font-family: var(--lato);
	position: sticky;
	top: 0;
	left: 0;
	z-index: 1000;
}
#content nav::before {
	content: '';
	position: absolute;
	width: 40px;
	height: 40px;
	bottom: -40px;
	left: 0;
	border-radius: 50%;
	box-shadow: -20px -20px 0 var(--light);
}

/* NAVBAR */





/* MAIN */
#content main {
	width: 100%;
	padding: 36px 24px;
	font-family: var(--poppins);
	max-height: calc(100vh - 56px);
	overflow-y: auto;
}
#content main .head-title {
	display: flex;
	align-items: center;
	justify-content: space-between;
	grid-gap: 16px;
	flex-wrap: wrap;
}
#content main .head-title .left h1 {
	font-size: 36px;
	font-weight: 600;
	margin-bottom: 10px;
	color: var(--dark);
}
#content main .head-title .left .breadcrumb {
	display: flex;
	align-items: center;
	grid-gap: 16px;
}
#content main .head-title .left .breadcrumb li {
	color: var(--dark);
}
#content main .head-title .left .breadcrumb li a {
	color: var(--dark-grey);
	pointer-events: none;
}
#content main .head-title .left .breadcrumb li a.active {
	color: var(--blue);
	pointer-events: unset;
}


/* Event Management Section */
.event-management {
    margin-top: 20px;
}

.add-event-form,
.event-list {
    margin-bottom: 30px;
}

.add-event-form h2,
.event-list h2 {
    font-size: 24px;
    margin-bottom: 10px;
}

.add-event-form label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
}

.add-event-form input,
.add-event-form textarea {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid var(--grey);
    margin-bottom: 10px;
}

.add-event-form button {
    padding: 10px 20px;
    background: var(--blue);
    color: var(--light);
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.add-event-form button:hover {
    background: var(--dark);
}

.event-list table {
    width: 100%;
    border-collapse: collapse;
}

.event-list th,
.event-list td {
    padding: 10px;
    border: 1px solid var(--grey);
}

.event-list th {
    background: var(--light);
}

/* CONTENT */









@media screen and (max-width: 768px) {
	#sidebar {
		width: 200px;
	}

	#content {
		width: calc(100% - 60px);
		left: 200px;
	}

	#content nav .nav-link {
		display: none;
	}
}






@media screen and (max-width: 576px) {
	#content nav form .form-input input {
		display: none;
	}

	#content nav form .form-input button {
		width: auto;
		height: auto;
		background: transparent;
		border-radius: none;
		color: var(--dark);
	}

	#content nav form.show .form-input input {
		display: block;
		width: 100%;
	}
	#content nav form.show .form-input button {
		width: 36px;
		height: 100%;
		border-radius: 0 36px 36px 0;
		color: var(--light);
		background: var(--red);
	}

	#content nav form.show ~ .notification,
	#content nav form.show ~ .profile {
		display: none;
	}

	#content main .box-info {
		grid-template-columns: 1fr;
	}

	#content main .table-data .head {
		min-width: 420px;
	}
	#content main .table-data .order table {
		min-width: 420px;
	}
	#content main .table-data .todo .todo-list {
		min-width: 420px;
	}
}
.completed  img {
	width: 36px;
	height: 36px;
	border-radius: 50%;
	object-fit: cover;
}
/* Base button styles */
button {
    background-color: #4CAF50; /* Green background */
    border: none;
    color: white; /* White text */
    padding: 10px 20px; /* Padding */
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px; /* Rounded corners */
    transition: background-color 0.3s, transform 0.3s;
}

/* Button hover effects */
button:hover {
    background-color: #45a049; /* Darker green on hover */
    transform: scale(1.05); /* Slight zoom effect */
}

/* Specific styles for action buttons */
.btn-edit {
	height: 36px;
    background: var(--light-blue)
	
}

.btn-edit:hover {
    background: var(--light-blue)
}

.btn-delete {
    background: var(--red)
}

.btn-delete:hover {
	background: var(--red)
}

.add-event-form button {
    background-color: #4CAF50; /* Green background for add */
}

.add-event-form button:hover {
    background-color: #45a049; /* Darker green on hover */

#event-table {
        border-collapse: collapse;
        width: 100%;
    }
    #event-table th, #event-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    #event-table th {
        background-color: #f4f4f4;
    }
    #event-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    #event-table tr:hover {
        background-color: #f1f1f1;
    }	
}

  </style>
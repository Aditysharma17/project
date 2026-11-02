<?php
require_once "config.php";
session_start();

if (empty($_SESSION["eventhub_csrf"])) {
    $_SESSION["eventhub_csrf"] = bin2hex(random_bytes(32));
}

$loginError = "";
$actionMessage = "";
$actionType = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postedToken = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["eventhub_csrf"], $postedToken)) {
        $actionMessage = "Security check failed. Please refresh and try again.";
        $actionType = "error";
    } elseif (isset($_POST["login"])) {
        $password = $_POST["password"] ?? "";

        if (hash_equals(ADMIN_PASSWORD, $password)) {
            session_regenerate_id(true);
            $_SESSION["eventhub_admin"] = true;
            header("Location: admin.php");
            exit;
        } else {
            $loginError = "Incorrect password. Please try again.";
        }
    } elseif (!empty($_SESSION["eventhub_admin"]) && isset($_POST["logout"])) {
        $_SESSION = [];
        session_destroy();
        header("Location: admin.php");
        exit;
    } elseif (!empty($_SESSION["eventhub_admin"]) && isset($_POST["add_event"])) {
        $title = trim($_POST["title"] ?? "");
        $category = trim($_POST["category"] ?? "");
        $date = trim($_POST["date"] ?? "");
        $venue = trim($_POST["venue"] ?? "");
        $description = trim($_POST["description"] ?? "");

        if ($title === "" || $category === "" || $date === "" || $venue === "" || $description === "") {
            $actionMessage = "Please complete all event fields.";
            $actionType = "error";
        } else {
            $events = read_json_file(EVENTS_FILE);
            $ids = array_map(function($event) { return (int)($event["id"] ?? 0); }, $events);
            $newId = count($ids) ? max($ids) + 1 : 1;

            $events[] = [
                "id" => $newId,
                "title" => $title,
                "category" => $category,
                "date" => $date,
                "venue" => $venue,
                "description" => $description
            ];

            if (save_json_file(EVENTS_FILE, $events)) {
                $actionMessage = "Event added successfully.";
            } else {
                $actionMessage = "Could not save the event. Check folder permissions.";
                $actionType = "error";
            }
        }
    } elseif (!empty($_SESSION["eventhub_admin"]) && isset($_POST["delete_event"])) {
        $deleteId = (string)($_POST["event_id"] ?? "");
        $events = read_json_file(EVENTS_FILE);
        $before = count($events);
        $events = array_values(array_filter($events, function($event) use ($deleteId) {
            return (string)($event["id"] ?? "") !== $deleteId;
        }));

        if (count($events) < $before && save_json_file(EVENTS_FILE, $events)) {
            $actionMessage = "Event deleted. Existing registration records have been kept.";
        } else {
            $actionMessage = "Event was not found or could not be deleted.";
            $actionType = "error";
        }
    }
}

$isAdmin = !empty($_SESSION["eventhub_admin"]);
$events = read_json_file(EVENTS_FILE);
$registrations = read_json_file(REGISTRATIONS_FILE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | EventHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">Event<span>Hub</span></a>
    <nav><a href="index.php">View website</a></nav>
</header>

<?php if (!$isAdmin): ?>
    <main class="form-page">
        <div class="form-card admin-login">
            <p class="eyebrow dark-eyebrow">EVENTHUB CONTROL ROOM</p>
            <h1>Admin login</h1>
            <p class="muted">Sign in to manage campus events and view registrations.</p>
            <?php if ($loginError !== ""): ?><div class="notice error"><?php echo h($loginError); ?></div><?php endif; ?>
            <?php if ($actionMessage !== ""): ?><div class="notice <?php echo h($actionType); ?>"><?php echo h($actionMessage); ?></div><?php endif; ?>
            <form method="POST" class="stacked-form">
                <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION["eventhub_csrf"]); ?>">
                <label for="password">Admin password</label>
                <input id="password" type="password" name="password" placeholder="Enter admin password" required>
                <button class="button button-dark full-button" type="submit" name="login">Login →</button>
            </form>
            <p class="login-hint">Demo password is provided in the project instructions. Change it in config.php.</p>
        </div>
    </main>
<?php else: ?>
    <main class="admin-page">
        <div class="admin-heading">
            <div><p class="eyebrow dark-eyebrow">CONTROL ROOM</p><h1>Admin dashboard</h1><p class="muted">Manage events and check student registrations.</p></div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION["eventhub_csrf"]); ?>">
                <button class="button button-outline" name="logout" type="submit">Log out</button>
            </form>
        </div>

        <?php if ($actionMessage !== ""): ?><div class="notice <?php echo h($actionType); ?>"><?php echo h($actionMessage); ?></div><?php endif; ?>

        <div class="stats-row">
            <div class="stat-card"><strong><?php echo count($events); ?></strong><span>Events</span></div>
            <div class="stat-card"><strong><?php echo count($registrations); ?></strong><span>Registrations</span></div>
        </div>

        <section class="admin-panel">
            <h2>Add a new event</h2>
            <form method="POST" class="admin-event-form">
                <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION["eventhub_csrf"]); ?>">
                <input type="hidden" name="add_event" value="1">
                <div class="form-field"><label for="title">Event title</label><input id="title" name="title" maxlength="100" required></div>
                <div class="form-field"><label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Choose category</option>
                        <option>Hackathon</option><option>Workshop</option><option>Entrepreneurship</option><option>Seminar</option><option>Sports</option><option>Cultural</option>
                    </select>
                </div>
                <div class="form-field"><label for="date">Event date</label><input id="date" name="date" type="date" required></div>
                <div class="form-field"><label for="venue">Venue</label><input id="venue" name="venue" maxlength="100" required></div>
                <div class="form-field wide-field"><label for="description">Description</label><textarea id="description" name="description" rows="3" maxlength="500" required></textarea></div>
                <div class="wide-field"><button class="button button-dark" type="submit">Add event +</button></div>
            </form>
        </section>

        <section class="admin-panel">
            <h2>Manage events</h2>
            <?php if (!$events): ?><p class="muted">No events available.</p><?php endif; ?>
            <div class="table-wrap"><table>
                <thead><tr><th>Event</th><th>Category</th><th>Date</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo h($event["title"] ?? ""); ?></td>
                        <td><?php echo h($event["category"] ?? ""); ?></td>
                        <td><?php echo h($event["date"] ?? ""); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Delete this event?');">
                                <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION["eventhub_csrf"]); ?>">
                                <input type="hidden" name="event_id" value="<?php echo h($event["id"] ?? ""); ?>">
                                <button class="delete-button" type="submit" name="delete_event">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        </section>

        <section class="admin-panel">
            <h2>Student registrations</h2>
            <?php if (!$registrations): ?>
                <p class="muted">No registrations yet. Test the registration form from the homepage.</p>
            <?php else: ?>
                <div class="table-wrap"><table>
                    <thead><tr><th>Student</th><th>Email</th><th>Roll number</th><th>Event</th><th>Registered at</th></tr></thead>
                    <tbody>
                    <?php foreach (array_reverse($registrations) as $registration): ?>
                        <tr>
                            <td><?php echo h($registration["name"] ?? ""); ?></td>
                            <td><?php echo h($registration["email"] ?? ""); ?></td>
                            <td><?php echo h($registration["student_id"] ?? "—"); ?></td>
                            <td><?php echo h($registration["event_title"] ?? ""); ?></td>
                            <td><?php echo h($registration["registered_at"] ?? ""); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        </section>
    </main>
<?php endif; ?>
<footer class="site-footer compact-footer"><p>EventHub · Admin area</p></footer>
</body>
</html>

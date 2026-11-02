<?php
require_once "config.php";

$events = read_json_file(EVENTS_FILE);
$eventId = $_POST["event_id"] ?? $_GET["event_id"] ?? "";
$selectedEvent = null;

foreach ($events as $event) {
    if ((string)($event["id"] ?? "") === (string)$eventId) {
        $selectedEvent = $event;
        break;
    }
}

$message = "";
$messageType = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$selectedEvent) {
        $message = "Sorry, this event could not be found.";
        $messageType = "error";
    } else {
        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $studentId = trim($_POST["student_id"] ?? "");

        if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter your name and a valid email address.";
            $messageType = "error";
        } else {
            $registrations = read_json_file(REGISTRATIONS_FILE);
            $alreadyRegistered = false;

            foreach ($registrations as $registration) {
                if (
                    (string)($registration["event_id"] ?? "") === (string)$eventId &&
                    strtolower((string)($registration["email"] ?? "")) === strtolower($email)
                ) {
                    $alreadyRegistered = true;
                    break;
                }
            }

            if ($alreadyRegistered) {
                $message = "This email address is already registered for this event.";
                $messageType = "error";
            } else {
                $registrations[] = [
                    "id" => count($registrations) ? max(array_column($registrations, "id")) + 1 : 1,
                    "event_id" => $selectedEvent["id"],
                    "event_title" => $selectedEvent["title"],
                    "name" => $name,
                    "email" => $email,
                    "student_id" => $studentId,
                    "registered_at" => date("Y-m-d H:i:s")
                ];

                if (save_json_file(REGISTRATIONS_FILE, $registrations)) {
                    $message = "Registration successful! Your details have been saved.";
                } else {
                    $message = "We could not save your registration. Please try again.";
                    $messageType = "error";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | EventHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">Event<span>Hub</span></a>
    <nav><a href="index.php">← Back to events</a><a href="admin.php">Admin</a></nav>
</header>

<main class="form-page">
    <div class="form-card">
        <p class="eyebrow dark-eyebrow">SAVE YOUR SPOT</p>
        <h1>Event registration</h1>

        <?php if ($message !== ""): ?>
            <div class="notice <?php echo h($messageType); ?>"><?php echo h($message); ?></div>
        <?php endif; ?>

        <?php if ($selectedEvent): ?>
            <div class="selected-event">
                <span class="category-pill"><?php echo h($selectedEvent["category"] ?? "Event"); ?></span>
                <h2><?php echo h($selectedEvent["title"]); ?></h2>
                <p><?php echo h($selectedEvent["date"] ?? "Date to be announced"); ?> · <?php echo h($selectedEvent["venue"] ?? "Venue to be announced"); ?></p>
            </div>

            <form method="POST" action="register.php" class="stacked-form">
                <input type="hidden" name="event_id" value="<?php echo h($eventId); ?>">
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" maxlength="80" placeholder="Enter your full name" required>
                <label for="email">College or personal email</label>
                <input id="email" name="email" type="email" maxlength="120" placeholder="you@example.com" required>
                <label for="student_id">Student roll number <span class="optional">(optional)</span></label>
                <input id="student_id" name="student_id" type="text" maxlength="40" placeholder="Enter your roll number">
                <button class="button button-dark full-button" type="submit">Submit registration →</button>
            </form>
        <?php else: ?>
            <p>This event link is invalid or the event has been removed.</p>
            <a class="button button-dark" href="index.php">Return to events</a>
        <?php endif; ?>
    </div>
</main>
<footer class="site-footer compact-footer"><p>EventHub · Campus events made simple</p></footer>
</body>
</html>

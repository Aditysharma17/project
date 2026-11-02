<?php
// For your college demo, change this password before showing the project.
define("ADMIN_PASSWORD", "EventHub@123");

define("EVENTS_FILE", __DIR__ . "/events.json");
define("REGISTRATIONS_FILE", __DIR__ . "/registrations.json");

function read_json_file($file) {
    if (!file_exists($file)) {
        return [];
    }

    $content = file_get_contents($file);
    $data = json_decode($content, true);

    return is_array($data) ? $data : [];
}

function save_json_file($file, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        return false;
    }

    return file_put_contents($file, $json . PHP_EOL, LOCK_EX) !== false;
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

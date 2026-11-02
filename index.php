<?php
require_once "config.php";
$events = read_json_file(EVENTS_FILE);
$categories = [];

foreach ($events as $event) {
    if (!empty($event["category"])) {
        $categories[] = $event["category"];
    }
}
$categories = array_values(array_unique($categories));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub | Campus Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">Event<span>Hub</span></a>
    <nav>
        <a href="index.php">Home</a>
        <a href="#events">Explore Events</a>
        <a href="admin.php">Admin</a>
    </nav>
</header>

<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">YOUR CAMPUS. YOUR COMMUNITY.</p>
        <h1>Discover what’s<br><span>happening on campus.</span></h1>
        <p class="hero-copy">Find hackathons, workshops, seminars, sports and cultural events — all in one place.</p>
        <a class="button button-light" href="#events">Explore events <span aria-hidden="true">→</span></a>
    </div>
    <div class="hero-stamp">
        <span>LEARN</span><span>· CONNECT</span><span>· CREATE</span>
    </div>
</section>

<main>
    <section class="section-heading" id="events">
        <div>
            <p class="eyebrow dark-eyebrow">CAMPUS CALENDAR</p>
            <h2>Upcoming events</h2>
            <p class="muted">Choose an event and reserve your place.</p>
        </div>
        <div class="event-count"><?php echo count($events); ?> <span>events listed</span></div>
    </section>

    <section class="filters" aria-label="Event filters">
        <input type="search" id="eventSearch" placeholder="Search by event name or venue..." aria-label="Search events">
        <select id="categoryFilter" aria-label="Filter by category">
            <option value="all">All categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo h(strtolower($category)); ?>"><?php echo h($category); ?></option>
            <?php endforeach; ?>
        </select>
    </section>

    <section class="event-grid" id="eventGrid">
        <?php if (count($events) === 0): ?>
            <div class="empty-state">No events have been added yet. Please check again soon.</div>
        <?php endif; ?>

        <?php foreach ($events as $event): ?>
            <article class="event-card"
                data-title="<?php echo h(strtolower($event["title"] ?? "")); ?>"
                data-category="<?php echo h(strtolower($event["category"] ?? "")); ?>"
                data-venue="<?php echo h(strtolower($event["venue"] ?? "")); ?>">
                <div class="card-topline">
                    <span class="category-pill"><?php echo h($event["category"] ?? "Event"); ?></span>
                    <span class="event-number">#<?php echo h($event["id"] ?? ""); ?></span>
                </div>
                <h3><?php echo h($event["title"] ?? "Untitled event"); ?></h3>
                <p class="event-description"><?php echo h($event["description"] ?? ""); ?></p>
                <div class="event-meta">
                    <span>▦ <?php echo h($event["date"] ?? "Date to be announced"); ?></span>
                    <span>⌖ <?php echo h($event["venue"] ?? "Venue to be announced"); ?></span>
                </div>
                <a class="button button-dark full-button" href="register.php?event_id=<?php echo urlencode((string)($event["id"] ?? "")); ?>">Register now <span aria-hidden="true">→</span></a>
            </article>
        <?php endforeach; ?>
    </section>
    <p class="no-results" id="noResults" hidden>No matching events found. Try another search or category.</p>
</main>

<footer class="site-footer">
    <a class="brand footer-brand" href="index.php">Event<span>Hub</span></a>
    <p>One place for every campus opportunity.</p>
    <p class="footer-small">College mini-project · Built with HTML, CSS, PHP and JSON</p>
</footer>
<script src="script.js"></script>
</body>
</html>

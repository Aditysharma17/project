EVENTHUB - COLLEGE MINI PROJECT
================================

TECHNOLOGIES
- HTML: page structure
- CSS: website design and responsive layout
- PHP: reads event data, handles registration and admin actions
- JSON: stores event and registration records
- JavaScript: search and category filter (small frontend enhancement)

NO MYSQL IS REQUIRED FOR THIS VERSION.

INSTALLATION ON WINDOWS USING XAMPP
1. Extract the EventHub folder from the ZIP.
2. Copy the complete EventHub folder into:
   C:\xampp\htdocs\
3. Open XAMPP Control Panel.
4. Start Apache. MySQL is not required.
5. Open Chrome and visit:
   http://localhost/EventHub/

ADMIN DASHBOARD
1. Visit:
   http://localhost/EventHub/admin.php
2. Demo password:
   EventHub@123
3. Change the password in config.php before presenting the project.
   Find: define("ADMIN_PASSWORD", "EventHub@123");
   Replace the password inside the quotes with your own.

HOW TO TEST
1. Open the homepage.
2. Search for an event or choose a category.
3. Click Register now on an event.
4. Enter a name and valid email, then submit.
5. Open the Admin page and log in.
6. See the registration in Student registrations.
7. Add an event from the admin form.
8. Return to the homepage and confirm the new event appears.
9. Delete an event from Manage events and confirm it disappears.

IMPORTANT
- This is a learning/demo project for running locally on your own computer.
- JSON-file storage is suitable for a small classroom demonstration, not a large public website with many simultaneous users.
- The admin password is stored in config.php for simplicity. Do not publish this demo online as-is.
- Back up events.json and registrations.json before experimenting.
- If Apache does not start, check whether another application is using port 80/443.

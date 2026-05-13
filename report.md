# Full-Stack Web Portfolio Project Report

## Project Overview
This project is a dynamic, full-stack web portfolio designed to showcase professional skills in modern web development. It integrates frontend technologies (HTML5, CSS3, JavaScript) with server-side logic (PHP, MySQL) to create an interactive and data-driven user experience.

## Technical Implementation

### 1. Frontend Development (HTML5, CSS3, JavaScript)
- **Semantic HTML & Layout:** The interface is built using semantic HTML5 tags (`<header>`, `<main>`, `<section>`, `<footer>`). The layout utilizes modern CSS Flexbox and Grid systems for a fully responsive design that adapts to all screen sizes.
- **Design System & Styling:** A custom design system was implemented using CSS Variables (`:root`), ensuring consistent branding, typography (Inter font), and color schemes.
- **Dark Mode / Light Mode:** A dynamic theme toggle was implemented using JavaScript. User preferences are persistently stored using the browser's `localStorage` API.
- **Client-Side Validation:** The contact form incorporates strict JavaScript validation (checking for empty fields, valid email regex, and minimum message length) before any server requests are made, improving UX and reducing server load.

### 2. Backend Development (PHP & MySQL)
- **Database Architecture:** A relational MySQL database (`portfolio_db`) was designed with three core tables: `projects` (for dynamic portfolio items), `contacts` (for storing user messages), and `admin_users` (for secure authentication).
- **Secure Connections:** Database interactions are handled via PHP Data Objects (PDO) using Prepared Statements, effectively preventing SQL Injection attacks.
- **AJAX Integration (Fetch API):** The portfolio projects on the homepage are fetched dynamically from the database using JavaScript's asynchronous `fetch()` API. This allows the content to load dynamically without refreshing the page, meeting the advanced interactivity requirement.
- **Contact Form Processing:** Form submissions are securely processed via PHP and saved to the database. The submission is handled asynchronously (AJAX), providing immediate feedback to the user without a page reload.

### 3. State Management & Admin Dashboard
- **Authentication:** A secure Admin Login portal was created. Passwords in the database are hashed using PHP's robust `password_hash()` algorithm.
- **Sessions and Cookies:** Secure access to the `dashboard.php` is maintained using PHP `$_SESSION`. Additionally, a "Remember Me" functionality demonstrates the practical use of PHP `$_COOKIE`.
- **Content Management:** Through the dashboard, the authenticated admin can dynamically add new projects to the database and view submitted contact messages.

## Conclusion
This project successfully integrates all required technologies to deliver a robust, secure, and visually appealing web application. The consistent commit history on GitHub reflects a structured, step-by-step development lifecycle from UI design to complex backend integration.

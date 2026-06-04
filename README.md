# The Career Key - Admin Panel

## Project Overview
The **Admin Panel of The Career Key Website for VCOT** is a web-based administrative dashboard designed to manage, analyze, and oversee personality assessment records based on the RIASEC model. It provides role-based access control, detailed statistics, and comprehensive data management capabilities.

**Developed by:** Vidhush Thamilchelvan, Amirda (Pvt) Ltd.

## Technology Stack
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS), Chart.js (for data visualization), FontAwesome (for icons).
- **Backend:** PHP 8.3 (Vanilla PHP).
- **Database:** MySQL 9.1 (accessed via PHP PDO).
- **Architecture:** Component-based structure with centralized routing through `index.php`.

## Directory Structure
- `actions/`: Contains PHP scripts that act as API endpoints and handle form submissions, CRUD operations, and authentication logic (e.g., `fetch.php`, `login_auth.php`, `records-paginated.php`).
- `assets/`: Stores static assets such as images and icons.
- `components/`: Reusable UI components including modals (confirm, alert, password change) and sidebars.
- `layouts/`: Master layout files that define the structure of the application (header, footer, sidebar, topbar).
- `pages/`: Main application views loaded dynamically into the layout (dashboard, records, staffs, profile).
- `styles/`: CSS stylesheets for the application's design system.
- Root Files:
  - `index.php`: The main entry point that aggregates layouts and pages.
  - `login.php`: Authentication interface.
  - `db_config.php` & `db_connection.php`: Database configuration and PDO connection setup.

## Database Schema
The database (`amirrpcb_ckey2507`) consists of three primary tables:

1. **`ckey_results`**: Stores the RIASEC assessment results.
   - Key fields: `rec_id`, `name`, `nic`, `log_no`, `score_r`, `score_i`, `score_a`, `score_s`, `score_e`, `score_c`, `created_at`, `notes`.
   - Primary Key: Composite key (`nic`, `log_no`).

2. **`ckey_roles`**: Defines system roles and their specific permissions.
   - Key fields: `role_id`, `role_name`, `crud_staff`, `crud_admins`, `update_role`, `view_results`, `crud_results`.
   - Default Roles: `superadmin` (full access), `admin` (manage staff and results), `staff` (view-only access).

3. **`ckey_staffs`**: Stores staff credentials and profile information.
   - Key fields: `staff_id`, `username`, `fname`, `lname`, `nic`, `email`, `role` (Foreign Key referencing `ckey_roles.role_name`), `password_hash`, `status` (active/inactive).

## Core Features
1. **Role-Based Access Control (RBAC):**
   - Granular permissions based on the user's role (Superadmin, Admin, Staff).
   - Secures API endpoints and dynamically renders UI elements (e.g., disabling delete buttons for staff).
2. **Interactive Dashboard:**
   - Visualizes key metrics (Total Records, Active Staffs).
   - Utilizes Chart.js to display the most and least preferred personality categories across all assessments.
   - Features robust date filtering (Today, This Week, This Month, This Year, Custom Range) that dynamically updates charts and tables via AJAX.
3. **Record Management:**
   - **Tabbed Interface:** View individual assessment attempts (Records) or grouped by individual (Records by Person).
   - **Server-Side Pagination:** Efficiently loads large datasets using offset and limit.
   - **Real-time Search:** Search records by NIC or Name.
4. **Staff Management:**
   - Admins and Superadmins can add new staff members, update their roles, and toggle their account status (active/inactive).
5. **Profile Management:**
   - Authenticated users can update their personal information and securely change their passwords.

## Security Practices
- **Password Hashing:** Uses PHP's native `password_hash()` (bcrypt) for secure credential storage.
- **PDO Prepared Statements:** Prevents SQL injection attacks across all database queries.
- **Session Management:** Secure session handling for authentication state and user validation.
- **XSS Prevention:** Utilizes HTML escaping (`escHtml` function in JavaScript) when rendering user-submitted data to the DOM.

## Setup Instructions
1. Import the SQL dump (`amirrpcb_ckey2507.sql`) into your MySQL database server.
2. Update `db_config.php` with your database credentials (Host, Username, Password, Database Name).
3. Ensure the server runs PHP 8.0+ and the PDO MySQL extension is enabled.
4. Access the application via a local web server (e.g., WAMP/XAMPP) by navigating to the project directory.

Default Superadmin credentials:
- Username: `sauser0152`
- Password: *(Set dynamically via registration/database seed)*

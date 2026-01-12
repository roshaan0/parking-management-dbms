\# University Parking Management System



\## Project Description



This project is a web-based Campus Parking Management System designed to automate and streamline the process of parking slot reservation and vehicle management within a university environment. It features a role-based architecture that provides specific functionalities for Administrators, Faculty, Students, and Security personnel.



The system addresses common campus parking issues by allowing users to register vehicles, check slot availability in real-time, and reserve parking spaces, while giving security staff the tools to monitor authorized vehicles.



\## Features



\* \*\*Multi-Role Authentication:\*\* Secure login and registration system with separate dashboards for different user types.

\* \*\*Admin Dashboard:\*\* Manage users, oversee system settings, and generate usage reports.

\* \*\*Student \& Faculty Portals:\*\* Capabilities to register vehicles, view available parking slots, and request reservations.

\* \*\*Security Interface:\*\* Tools for security personnel to verify vehicle authorizations and manage real-time parking flow.

\* \*\*Slot Management:\*\* Automated tracking of reserved versus available parking spaces.

\* \*\*Reporting:\*\* Generation of parking statistics and logs.



\## Technology Stack



\* \*\*Backend:\*\* PHP

\* \*\*Database:\*\* MySQL

\* \*\*Dependency Management:\*\* Composer



\## Prerequisites



Before running this project, ensure you have the following installed:



\* \*\*XAMPP/WAMP/MAMP:\*\* A local server environment containing Apache and MySQL.

\* \*\*Composer:\*\* A dependency manager for PHP.

\* \*\*Web Browser:\*\* Chrome, Edge, or Firefox for testing.



\## Installation and Setup



\### 1. Clone the Repository



Clone the project to your local machine inside your server's root directory (e.g., `C:\\xampp\\htdocs\\` for XAMPP).



```bash

git clone https://github.com/YOUR\_USERNAME/YOUR\_REPO\_NAME.git



```



\### 2. Install Dependencies



This project uses Composer to manage dependencies. Open a terminal in the project root directory and run:



```bash

composer install



```



This will generate the `vendor` folder required for the application to run.



\### 3. Database Configuration



1\. Open your database management tool (e.g., phpMyAdmin).

2\. Create a new database named \*\*`bu\_cpms`\*\*.

3\. Import the provided SQL file located at \*\*`bu\_cpms.sql`\*\* into the newly created database.



\### 4. Database Connection



The database connection settings are located in `db.php`. By default, they are configured for a standard XAMPP environment:



\* \*\*Server:\*\* localhost

\* \*\*Username:\*\* root

\* \*\*Password:\*\* (empty)

\* \*\*Database:\*\* bu\_cpms



If your local environment uses a password, please update `db.php` accordingly.



\## Usage



1\. Start the \*\*Apache\*\* and \*\*MySQL\*\* modules in your control panel (XAMPP).

2\. Open your web browser and navigate to:

`http://localhost/bu\_parking/`

3\. Log in using the credentials stored in the database or register a new account to access the respective dashboard.



\## Project Structure



\* `src/`: Contains core application logic and classes.

\* `vendor/`: Third-party libraries managed by Composer.

\* `\*.php`: Frontend and backend logic files for various dashboards (Admin, Student, Faculty, Security).

\* `bu\_cpms.sql`: Database import file.

\* `db.php`: Database connection configuration.



\## License



This project is open-source and available for educational purposes.


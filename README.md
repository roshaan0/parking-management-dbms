# University Parking Management System

## Project Description

The University Parking Management System is a web-based application designed to automate and streamline parking operations within a university campus. The system provides a structured, role-based environment that supports Administrators, Students, Faculty members, and Security personnel, ensuring efficient parking slot allocation and vehicle monitoring.

By digitizing vehicle registration, slot reservation, and authorization checks, the system reduces congestion, improves security, and enhances overall campus parking management.

---

## Features

* **Multi-Role Authentication**
  Secure login and registration with separate dashboards for Admin, Student, Faculty, and Security roles.

* **Admin Dashboard**
  Manage users, oversee system operations, and monitor parking usage.

* **Student and Faculty Portals**
  Register vehicles, view real-time parking slot availability, and request parking reservations.

* **Security Interface**
  Verify authorized vehicles and monitor parking activity across the campus.

* **Slot Management System**
  Automated tracking of available, reserved, and occupied parking slots.

* **Reporting and Logs**
  Generation of parking records and system activity logs for administrative review.

---

## Technology Stack

* **Backend:** PHP
* **Database:** MySQL
* **Dependency Management:** Composer
* **Server Environment:** Apache (XAMPP/WAMP/MAMP)

---

## Prerequisites

Before running this project, ensure the following are installed:

* A local server environment such as **XAMPP**, **WAMP**, or **MAMP** (Apache and MySQL)
* **Composer** for PHP dependency management
* A modern web browser (Chrome, Edge, or Firefox)

---

## Installation and Setup

### 1. Clone the Repository

Clone the project into your local server root directory.

Example for XAMPP:

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git
```

Place the project inside:

```
C:/xampp/htdocs/
```

---

### 2. Install Dependencies

Navigate to the project root directory and run:

```bash
composer install
```

This command will generate the `vendor` directory required for the application to function.

---

### 3. Database Setup

1. Open your database management tool (e.g., phpMyAdmin).
2. Create a new database named:

```
bu_cpms
```

3. Import the SQL file:

```
bu_cpms.sql
```

into the newly created database.

---

### 4. Database Configuration

Database connection settings are located in `db.php`.

Default configuration for XAMPP:

* Server: `localhost`
* Username: `root`
* Password: *(empty)*
* Database: `bu_cpms`

If your environment uses a different configuration, update `db.php` accordingly.

---

## Usage

1. Start **Apache** and **MySQL** from your server control panel.
2. Open your web browser and navigate to:

```
http://localhost/bu_parking/
```

3. Log in using existing credentials from the database or register a new account to access the appropriate dashboard.

---

## Project Structure

```
University-Parking-Management-System/
│
├── src/            # Core application logic and classes
├── vendor/         # Composer-managed dependencies
├── *.php           # Role-based dashboards and backend logic
├── bu_cpms.sql     # Database schema and sample data
├── db.php          # Database connection configuration
└── README.md
```

---

## License

This project is open-source and intended for educational and academic use, particularly for demonstrating web development concepts and database-driven system design.

# Basic CRUD Application

## Project Overview

This project is a simple Blog Application developed using PHP and MySQL. It demonstrates CRUD (Create, Read, Update, Delete) operations along with basic user authentication.

## Features

* User Registration
* User Login
* User Logout
* Create Blog Posts
* View Blog Posts
* Edit Blog Posts
* Delete Blog Posts
* Password Hashing
* Session Management

## Technologies Used

* PHP
* MySQL
* HTML
* CSS
* XAMPP

## Database Setup

1. Create a database named `blog`.
2. Create the following tables:

### Users Table

```sql
CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    password VARCHAR(255)
);
```

### Posts Table

```sql
CREATE TABLE posts(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Project Structure

```text
blog-project/
│
├── config/
│   └── database.php
│
├── auth/
│   ├── register.php
│   ├── login.php
│   └── logout.php
│
├── posts/
│   ├── add_post.php
│   ├── edit_post.php
│   └── delete_post.php
│
├── index.php
└── blog.sql
```

## How to Run

1. Install XAMPP.
2. Start Apache and MySQL.
3. Import the database into phpMyAdmin.
4. Copy the project folder to the `htdocs` directory.
5. Open your browser and visit:

```text
http://localhost/blog-project
```

## CRUD Operations

### Create

Add new blog posts.

### Read

View all blog posts.

### Update

Edit existing blog posts.

### Delete

Remove blog posts from the database.

## Output

The application allows users to register, log in, manage blog posts, and securely store user credentials using password hashing.

## Author

Naveen Kumar Dasari


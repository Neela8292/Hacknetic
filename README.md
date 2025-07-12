ODOO HACKATHON -
PROBLEM STATEMENT

Skill Swap Platform 
Overview: 
Develop a Skill Swap Platform — a mini application that enables users to list their skills and 
request others in return 

Team Leader Name:  Udumula Neela Lohitha Susmitha Reddy       
Email Id       :  susmithareddyudumula@gmail.com

Team Members      
1.Bikki Bindu Venkata Priya      
2.Mahendra Kumar       
3.Murahari Akhilesh  

A web application designed to connect individuals who want to exchange skills. Users can create profiles, list skills they offer and skills they want to learn, browse other users, and send swap requests. The platform facilitates learning and knowledge sharing within a community.

## Features

*   *User Authentication:* Secure user registration and login.
*   *User Profiles:* Users can manage their personal information, location, availability, and profile visibility.
*   *Skill Management:* Users can add and remove skills they offer (with proficiency levels) and skills they want to learn (with desired levels).
*   *User Browsing & Search:* Browse public profiles and search for users based on skills they offer or their location.
*   *Swap Request System:* Send and receive skill swap requests between users.
*   *Email Notifications:* Recipients receive email notifications for new swap requests, with direct links to accept or reject them.
*   *Dashboard:* A personalized dashboard for users to manage their skills and track incoming/outgoing swap requests.

## Technologies Used

*   *Backend:* PHP (with PDO for database interaction)
*   *Database:* MySQL
*   *Frontend:* HTML, CSS, JavaScript
*   *Email Sending:* PHPMailer (for reliable SMTP email delivery)

## Setup Instructions

To get this project up and running on your local machine, follow these steps:
### Prerequisites

*   *Web Server with PHP & MySQL:* XAMPP, WAMP, MAMP, or a similar environment. This project assumes a local setup where the project folder is directly accessible via localhost.
*   *PHPMailer Library:*
    1.  Download PHPMailer from its [GitHub repository](https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip).
    2.  Extract the downloaded ZIP file.
    3.  Copy the src folder from the extracted PHPMailer directory into your project's classes/PHPMailer/ directory. So, you should have files like classes/PHPMailer/src/PHPMailer.php.

### 1. Project Placement

1.  Place the entire skill-swap-platform project folder into your web server's document root (e.g., C:\xampp\htdocs\ for XAMPP).

### 2. Database Setup

1.  *Start MySQL:* Ensure your MySQL server is running (via XAMPP Control Panel or similar).
2.  *Access phpMyAdmin:* Open your web browser and go to http://localhost/phpmyadmin/.
3.  *Execute SQL Scripts (in order):*
    *   Click on the SQL tab in phpMyAdmin.
    *   **a. scripts/database-setup.sql:**
        *   Copy the entire content of scripts/database-setup.sql.
        *   Paste it into the SQL text area and click Go. This will create the skill_swap_platform database and all its tables.
        *   After execution, select the skill_swap_platform database from the left sidebar.
    *   **b. scripts/seed-data.sql:**
        *   With skill_swap_platform selected, click the SQL tab again.
        *   Copy the entire content of scripts/seed-data.sql.
        *   Paste it into the SQL text area and click Go. This will populate initial skills and an admin user.
    *   **c. scripts/create-sample-users.sql:**
        *   With skill_swap_platform selected, click the SQL tab again
        *   Copy the entire content of scripts/create-sample-users.sql.
        *   Paste it into the SQL text area and click Go. This will add sample users and their associated skills.

### 3. Email Configuration

1.  Open config/email.php in your project.
2.  *Update the following constants* with your SMTP server details. For testing, you can use a Gmail account (you'll need to generate an "App Password" if you have 2-Step Verification enabled for your Google account).

    \\\`php
    define('SMTP_HOST', 'smtp.gmail.com'); // e.g., 'smtp.gmail.com'    
    define('SMTP_USERNAME', 'your_email@gmail.com'); // Your full email address    
    define('SMTP_PASSWORD', 'your_app_password_or_regular_password'); // Your App Password for Gmail, or regular password     
    define('SMTP_PORT', 587); // 587 for TLS, 465 for SSL   
    define('SMTP_ENCRYPTION', 'tls'); // 'tls' or 'ssl'     
       
    define('MAIL_FROM_EMAIL', 'no-reply@skillswap.com'); // Sender email    
    define('MAIL_FROM_NAME', 'SkillSwap Platform'); // Sender name    
    \\\`

    *Security Note:* For production environments, avoid hardcoding credentials. Use environment variables or a secure configuration management system.

### 4. Running the Application
1.  Open your web browser and navigate to: http://localhost/skill-swap-platform/

## Usage

1.  *Register:* Create a new user account via register.php.
2.  *Login:* Log in with your new account or the default admin user (admin/admin123).
3.  *Dashboard:* After logging in, you'll be redirected to your dashboard (dashboard.php) where you can manage your skills.
4.  *Profile:* Update your profile information on profile.php.
5.  *Browse Users:* Go to browse.php to find other users. Use the search bar to filter by skill or location.
6.  *Send Swap Request:* On a user's profile or from the browse page, click "Request Swap" to send a request. The recipient will receive an email notification.
7.  *Manage Requests:* Check your dashboard (dashboard.php) to see incoming and outgoing swap requests. You can accept or reject requests sent to you.

## Project Structure

`
skill-swap-platform/   
├── assets/      
│   ├── css/    
│   │   └── style.css    
│   └── js/      
│       └── main.js     
├── classes/     
│   ├── PHPMailer/  (PHPMailer library files)    
│   │   └── src/    
│   │       ├── Exception.php    
│   │       ├── PHPMailer.php
│   │       └── SMTP.php     
│   ├── Skill.php       
│   ├── SwapRequest.php      
│   └── User.php      
├── config/     
│   ├── database.php    
│   ├── email.php       
│   └── session.php       
├── scripts/         
│   ├── create-sample-users.sql      
│   ├── database-setup.sql        
│   └── seed-data.sql      
├── admin.php (if implemented)      
├── browse.php         
├── dashboard.php     
├── index.php    
├── login.php    
├── logout.php     
├── profile.php    
├── register.php   
└── user-profile.php   


## License
This project is open-sourced under the MIT License.

# Bloggy - A Blog Management System By Ayoub Toujani
Project Overview
Bloggy is a blog management system created as a test project. The project showcases my skills in building a web application using Symfony, implementing CRUD functionalities, basic authentication, and form validation. The application allows authors to create, update, delete, and view articles.

Features
CRUD Operations for Articles: Authors can create, update, delete, and view articles.
Authentication: Basic authentication using the Symfony session interface.
Form Validation: Ensures no null values in the forms, with appropriate error messages.
Video Demonstration
A video demonstration of the project is included in the root directory of the project. This video shows the setup process, basic functionalities, and how to use the application.
https://we.tl/t-8d6d4XHso4

Installation and Setup
Follow these steps to set up and run the project on your local machine.

Prerequisites
PHP 8.0 or higher
Composer
Symfony CLI
MySQL or another database server

Step 1: Clone the Repository
git clone <your-repo-url>
cd <your-repo-directory>

Step 2: install or update your composer 
composer install or composer update 

Step 3: Configure Environment Variables
Create a .env.local file in the root directory of the project and set the necessary environment variables. Here is an example configuration:
DATABASE_URL="mysql://root:@127.0.0.1:3306/test_blog_bd"

the data base that i used is already exist inside the repository under the name : test_blog.sql you can use it 

or you can continue with creating a new data base 

Step 4: Set Up the Database
Create the database and run migrations to set up the schema:

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

Step 5: Run the Development Server
symfony server:start
Usage
Authentication

Log in using email and password .Of course there is a veridication in each field whether in the sign up or the login form. The authentication system uses the Symfony session interface for managing user sessions.

Creating an Article

Navigate to the "Create" button in the Home page.
Fill in the article title and content.
Click the "ajouter" button to create the article.

you cant create an empty article also here 

Updating an Article

Navigate to "My Articles" to see a list of articles created by the logged-in author.
Click on the "Update" button next to the article you want to update.
Edit the article title and content.
Click the "Update" button to save the changes.

Deleting an Article
Navigate to "My Articles".
Click on the "Delete" button next to the article you want to delete.

Viewing Articles
Navigate to the "Home" page to see a list of all articles.

Code Overview
Entities
Auteur: Represents the authors who can create and manage articles.
Article: Represents the blog articles created by authors.
Controllers
MainController: Handles CRUD operations for articles,and authentification.

Security
The project includes basic protection against SQL injection by using Doctrine ORM for database interactions. This ensures that all queries are parameterized and prevents direct SQL injection attacks.

Troubleshooting
If you encounter any issues while setting up or running the project, consider the following:

Ensure all environment variables are correctly set in your .env.local file.
Verify that your database server is running and accessible.
Check that all required PHP extensions are installed and enabled.
Review the Symfony logs for any error messages (var/log/dev.log).
Contributing
If you wish to contribute to the project, please fork the repository and create a pull request with your changes. Ensure that your code adheres to the project's coding standards and includes appropriate tests.

License
This project is licensed under the MIT License. See the LICENSE file for details.

Contact
If you have any questions or need further assistance, feel free to reach out to me at [toujaniayoub808@gmail.com].

Thank you for using Bloggy! We hope you enjoy the application.

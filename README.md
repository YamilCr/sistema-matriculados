## installation  
## 1.Clone GitHub repo for this project locally
If the project is hosted on github, we can use git on your local computer to clone it from github onto your local computer.

    git clone (https://github.com/YamilCr/sistema-matriculados.git)
## 2. cd into your project

    cd directorio/de/la/carpeta
    
## 3. Install Composer Dependencies
Whenever you clone a new Laravel project you must now install all of the project dependencies. This is what actually installs Laravel itself, among other necessary packages to get started.

When we run composer, it checks the composer.json file which is submitted to the github repo and lists all of the composer (PHP) packages that your repo requires. Because these packages are constantly changing, the source code is generally not submitted to github, but instead we let composer handle these updates. So to install all this source code we run composer with the following command.

    composer install

## 4. Install NPM Dependencies
   Just like how we must install composer packages to move forward, we must also install necessary NPM packages to move forward. This will install Vue.js, Bootstrap.css, Lodash, and Laravel Mix.

This is just like step 4, where we installed the composer PHP packages, but this is installing the Javascript (or Node) packages required. The list of packages that a repo requires is listed in the packages.json file which is submitted to the github repo. Just like in step 4, we do not commit the source code for these packages to version control (github) and instead we let NPM handle it.

    npm install

or if you prefer yarn (as i do)

    yarn  
## 5. Create a copy of your .env file
.env files are not generally committed to source control for security reasons. But there is a .env.example which is a template of the .env file that the project expects us to have. So we will make a copy of the .env.example file and create a .env file that we can start to fill out to do things like database configuration in the next few steps.

    cp .env.example .env

This will create a copy of the .env.example file in your project and name the copy simply .env.

## 6 Create an empty database for our application
Create an empty database for your project using the database tools you prefer (My favorite is SequelPro for mac). In our example we created a database called “test”. Just create an empty database here, the exact steps will depend on your system setup.

## 7. In the .env file, add database information to allow Laravel to connect to the database
We will want to allow Laravel to connect to the database that you just created in the previous step. To do this, we must add the connection credentials in the .env file and Laravel will handle the connection from there.

In the .env file fill in the DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD options to match the credentials of the database you just created. This will allow us to run migrations and seed the database in the next step.

## 8. Migrate the database
Once your credentials are in the .env file, now you can migrate your database.

php artisan migrate
It’s not a bad idea to check your database to make sure everything migrated the way you expected.

## 9. Seed the database

    php artisan db:seed

## 10. Open the app in your browser
    

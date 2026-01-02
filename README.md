# LogiSync
_Laravel and VueJs based team management application_

![Intro Preview](https://github.com/OverkillViper/Logisync/blob/main/public/images/intro.png "Intro Preview")

## Features
- Multi-role based authentication
  - Supported roles: Admin / Trainer / Trainee
- Training Management Dashboard
  - Projects with upcoming deadlines
  - Today's attendance of trainees
  - Trainees on leave in current month
- Project Management
  - Manage project assignees, reviewers and deadlines
  - Individual project progress tracking
  - Deadline extension and project complettion request
  - Manage project comments
- Trainee Evaluation
  - Perform trainee evaluation on different criteria (set by Admin)
  - Evaluate by batch, see evaluation individually
- Trainee Attendance
  - Punch in and out daily attendance
  - See todays attendance. Late attendance marked automatically
  - See attendance trand of individual trainee
- Trainee Leave Management
  - Mark trainee absense as Leave/Holiday
  - See trainee leave history
 
## How to install
### Pre-requisites
- PHP 8.2 or higher
- Composer
- Node.js (>= 16) & NPM

### Install Backend Libraries & Framework
1. Install PHP dependencies and prepare the environment:
```
# Install dependencies
composer install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

2. Configure the Database: Open the `.env` file and update your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

3. Run Migrations
```
php artisan migrate
```

### Install Frontend Libraries and Framework
1. Install node dependencies
```
npm install
npm run build
```

### Seed existing data
Run the following command to populate existing data
```
php artisan db:seed
```

## Running the Application
1. Run the following command to start the application
```
php artisan serve --host 0.0.0.0 --port 8000
```

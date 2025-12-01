# Child Health Monitoring System (CHMS)

A comprehensive web-based platform designed to monitor child health, growth, and development from birth. The system enables mothers to track their children's health records and allows healthcare professionals to provide timely interventions based on monthly assessments.

## Features

### For Mothers
- Add and manage child profiles
- View growth trends (weight and height charts)
- Track immunization schedules
- Receive health alerts and notifications
- Access health records and doctor's notes

### For Doctors/Healthcare Professionals
- View all registered children
- Add and update health records
- Monitor growth patterns and nutrition status
- Generate alerts for health deviations
- Track vaccination schedules
- Comprehensive dashboard with statistics

## Technology Stack

- **Backend:** PHP 7.4+ with PDO for database operations
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, TailwindCSS (via CDN), JavaScript
- **Charts:** Chart.js for growth visualization
- **Server:** Apache (via XAMPP)

## Installation Instructions

### Prerequisites
- XAMPP (or WAMP/LAMP/MAMP)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Edge, Safari)

### Step-by-Step Installation

#### 1. Install XAMPP
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP (default installation path: `C:\xampp`)
3. Start Apache and MySQL services from XAMPP Control Panel

#### 2. Setup Project Files
1. Copy the entire `CHMS` folder to `C:\xampp\htdocs\`
   - Final path should be: `C:\xampp\htdocs\CHMS\`

#### 3. Create Database
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on "Import" tab
3. Click "Choose File" and select `C:\xampp\htdocs\CHMS\database.sql`
4. Click "Go" to import the database
   - This will create the `chms_db` database with all tables and sample data

**Alternative method:**
1. Open phpMyAdmin
2. Click "SQL" tab
3. Copy the entire content of `database.sql` file
4. Paste it into the SQL window
5. Click "Go"

#### 4. Configure Database Connection
The database configuration is already set for default XAMPP installation:
- **Host:** localhost
- **Database:** chms_db
- **Username:** root
- **Password:** (empty)

If you have different database credentials, edit `src/config/database.php`:
```php
private $host = "localhost";
private $db_name = "chms_db";
private $username = "root";  // Change if needed
private $password = "";       // Change if needed
```

#### 5. Access the Application
1. Open your web browser
2. Navigate to: `http://localhost/CHMS/login.php`
3. Use the demo credentials below to login

## Demo Credentials

### Doctor Account
- **Email:** sarah.johnson@chms.com
- **Password:** password123

### Mother Account
- **Email:** mary.williams@email.com
- **Password:** password123

Additional accounts available in the database:
- jennifer.davis@email.com / password123 (Mother)
- patricia.martinez@email.com / password123 (Mother)
- michael.chen@chms.com / password123 (Doctor)

## Project Structure

```
CHMS/
│
├── assets/                    # Static assets
│   ├── css/                   # Custom CSS (TailwindCSS via CDN)
│   ├── js/                    # JavaScript files
│   │   ├── app.js            # Form validation and utilities
│   │   └── charts.js         # Chart.js helper functions
│   └── images/               # Image assets
│
├── includes/                  # Reusable components
│   └── navbar.php            # Navigation bar
│
├── src/                       # Backend source code
│   ├── config/               # Configuration files
│   │   ├── database.php      # Database connection
│   │   ├── session.php       # Session management
│   │   └── helpers.php       # Helper functions
│   │
│   ├── controllers/          # Controllers
│   │   ├── auth_controller.php       # Authentication
│   │   └── child_controller.php      # Child CRUD operations
│   │
│   └── models/               # Data models
│       ├── User.php          # User model
│       ├── Child.php         # Child model
│       ├── HealthRecord.php  # Health record model
│       ├── Alert.php         # Alert model
│       └── Immunization.php  # Immunization model
│
├── database.sql              # Database schema and sample data
├── index.php                 # Mother dashboard (home page)
├── login.php                 # Login page
├── register.php              # Registration page
├── add_child.php            # Add child form
├── child_profile.php        # Child profile and growth charts
├── doctor_dashboard.php     # Doctor dashboard
└── README.md                # This file
```

## Database Schema

### Tables

1. **users** - Stores user accounts (mothers and doctors)
2. **children** - Child profiles
3. **health_records** - Monthly health check-up records
4. **alerts** - Health alerts and notifications
5. **immunizations** - Vaccination records and schedules

### Relationships
- `children.mother_id` → `users.user_id`
- `health_records.child_id` → `children.child_id`
- `alerts.child_id` → `children.child_id`
- `immunizations.child_id` → `children.child_id`

## Key Features Implementation

### Security
- Password hashing using PHP `password_hash()` with bcrypt
- Prepared statements for all database queries (SQL injection prevention)
- Session-based authentication
- Role-based access control (Mother vs Doctor)
- Input sanitization and validation

### Data Monitoring
- Automatic growth tracking (weight and height)
- BMI calculation
- Growth trend visualization with Chart.js
- Alert generation for health deviations

### User Interface
- Responsive design (mobile, tablet, desktop)
- TailwindCSS for modern styling
- Interactive charts and graphs
- Flash messages for user feedback

## Usage Guide

### For Mothers

1. **Register/Login**
   - Create an account or login with existing credentials
   - Select "Mother/Guardian" as role during registration

2. **Add Child Profile**
   - Click "Add Child" from navigation
   - Enter child's name, date of birth, and gender
   - Optionally add initial birth weight and height

3. **View Child Profile**
   - Click on any child card on the dashboard
   - View growth charts, health records, immunizations, and alerts

4. **Monitor Health**
   - Check dashboard regularly for alerts
   - Track upcoming vaccinations
   - Review doctor's notes from checkups

### For Doctors

1. **Login**
   - Use doctor credentials to access doctor dashboard

2. **View All Children**
   - Access comprehensive list of all registered children
   - Search and filter children

3. **Add Health Records**
   - Click on a child profile
   - Click "Add Health Record"
   - Enter weight, height, nutrition status, vaccinations, and notes

4. **Monitor Alerts**
   - View pending alerts on dashboard
   - Track overdue vaccinations
   - Identify children requiring attention

## Troubleshooting

### Database Connection Errors
- Verify MySQL is running in XAMPP Control Panel
- Check database credentials in `src/config/database.php`
- Ensure database `chms_db` exists in phpMyAdmin

### Page Not Found (404)
- Verify project is in `C:\xampp\htdocs\CHMS\`
- Check Apache is running in XAMPP Control Panel
- Access via `http://localhost/CHMS/` not just `http://localhost/`

### Login Issues
- Clear browser cookies and cache
- Use exact demo credentials (case-sensitive)
- Check if session is enabled in PHP (`php.ini`)

### Charts Not Displaying
- Check browser console for JavaScript errors
- Ensure internet connection (Chart.js loads from CDN)
- Verify child has health records in database

## Development Notes

### Adding New Features
1. Create model in `src/models/`
2. Create controller in `src/controllers/`
3. Create view/page in root directory
4. Update navigation in `includes/navbar.php`

### Database Changes
1. Modify `database.sql` with new schema
2. Update relevant model class
3. Re-import database or run ALTER queries

## Future Enhancements

- [ ] Mobile application (Android/iOS)
- [ ] Email notifications for alerts and appointments
- [ ] PDF report generation
- [ ] WHO growth chart integration
- [ ] Multi-language support
- [ ] SMS reminders for vaccinations
- [ ] Advanced analytics and reporting
- [ ] Appointment scheduling system
- [ ] Community health education modules

## License

This project is developed for educational and healthcare purposes.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review PHP error logs in `C:\xampp\htdocs\CHMS\error.log`
3. Check Apache error logs in `C:\xampp\apache\logs\error.log`

## Credits

- **Framework:** PHP, MySQL
- **UI Framework:** TailwindCSS
- **Charts:** Chart.js
- **Icons:** Heroicons (via TailwindCSS)

---

**Version:** 1.0.0  
**Last Updated:** December 1, 2025  
**Developed for:** Child Health Monitoring Initiative

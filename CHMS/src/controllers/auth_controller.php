<?php
/**
 * Authentication Controller
 * Handles login, registration, and logout
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/User.php';

// Handle different actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'login':
            handleLogin();
            break;
        case 'register':
            handleRegister();
            break;
        case 'logout':
            handleLogout();
            break;
        default:
            setFlashMessage('Invalid action', 'error');
            redirect('/CHMS/login.php');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'logout') {
        handleLogout();
    }
}

/**
 * Handle user login
 */
function handleLogin() {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        setFlashMessage('Email and password are required', 'error');
        redirect('/CHMS/login.php');
        return;
    }

    if (!validateEmail($email)) {
        setFlashMessage('Invalid email format', 'error');
        redirect('/CHMS/login.php');
        return;
    }

    // Attempt login
    $user = new User();
    $result = $user->login($email, $password);

    if ($result) {
        // Login successful
        loginUser($result);
        setFlashMessage('Welcome back, ' . $result['name'] . '!', 'success');
        
        // Redirect based on role
        if ($result['role'] === 'doctor') {
            redirect('/CHMS/doctor_dashboard.php');
        } else {
            redirect('/CHMS/index.php');
        }
    } else {
        setFlashMessage('Invalid email or password', 'error');
        redirect('/CHMS/login.php');
    }
}

/**
 * Handle user registration
 */
function handleRegister() {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $role = sanitize($_POST['role'] ?? 'mother');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        setFlashMessage('Name, email, and password are required', 'error');
        redirect('/CHMS/register.php');
        return;
    }

    if (!validateEmail($email)) {
        setFlashMessage('Invalid email format', 'error');
        redirect('/CHMS/register.php');
        return;
    }

    if (!empty($phone) && !validatePhone($phone)) {
        setFlashMessage('Invalid phone number format', 'error');
        redirect('/CHMS/register.php');
        return;
    }

    if ($password !== $confirmPassword) {
        setFlashMessage('Passwords do not match', 'error');
        redirect('/CHMS/register.php');
        return;
    }

    if (strlen($password) < 8) {
        setFlashMessage('Password must be at least 8 characters long', 'error');
        redirect('/CHMS/register.php');
        return;
    }

    if (!in_array($role, ['mother', 'doctor'])) {
        setFlashMessage('Invalid role selected', 'error');
        redirect('/CHMS/register.php');
        return;
    }

    // Check if email already exists
    $user = new User();
    if ($user->emailExists($email)) {
        setFlashMessage('Email already registered', 'error');
        redirect('/CHMS/register.php');
        return;
    }

    // Create new user
    $user->name = $name;
    $user->email = $email;
    $user->phone = $phone;
    $user->role = $role;
    $user->password = $password;

    if ($user->create()) {
        setFlashMessage('Registration successful! Please login.', 'success');
        redirect('/CHMS/login.php');
    } else {
        setFlashMessage('Registration failed. Please try again.', 'error');
        redirect('/CHMS/register.php');
    }
}

/**
 * Handle user logout
 */
function handleLogout() {
    logoutUser();
    setFlashMessage('You have been logged out successfully', 'success');
    redirect('/CHMS/login.php');
}
?>

<?php
/**
 * Child Controller
 * Handles child CRUD operations
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Child.php';
require_once __DIR__ . '/../models/HealthRecord.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            handleCreate();
            break;
        case 'update':
            handleUpdate();
            break;
        case 'delete':
            handleDelete();
            break;
        default:
            setFlashMessage('Invalid action', 'error');
            redirect('/CHMS/index.php');
    }
}

/**
 * Handle create child
 */
function handleCreate() {
    requireRole('mother');
    
    $name = sanitize($_POST['name'] ?? '');
    $dob = sanitize($_POST['dob'] ?? '');
    $gender = sanitize($_POST['gender'] ?? '');
    $weight = $_POST['weight'] ?? null;
    $height = $_POST['height'] ?? null;

    // Validate inputs
    if (empty($name) || empty($dob) || empty($gender)) {
        setFlashMessage('All required fields must be filled', 'error');
        redirect('/CHMS/add_child.php');
        return;
    }

    if (!validateDate($dob)) {
        setFlashMessage('Invalid date format', 'error');
        redirect('/CHMS/add_child.php');
        return;
    }

    if (!in_array($gender, ['male', 'female'])) {
        setFlashMessage('Invalid gender selected', 'error');
        redirect('/CHMS/add_child.php');
        return;
    }

    // Create child
    $child = new Child();
    $child->mother_id = getCurrentUserId();
    $child->name = $name;
    $child->dob = $dob;
    $child->gender = $gender;

    if ($child->create()) {
        // If initial health data provided, create first health record
        if (!empty($weight) && !empty($height)) {
            $healthRecord = new HealthRecord();
            $healthRecord->child_id = $child->child_id;
            $healthRecord->weight = $weight;
            $healthRecord->height = $height;
            $healthRecord->nutrition_status = 'Normal';
            $healthRecord->vaccinations = '';
            $healthRecord->doctor_notes = 'Initial birth record';
            $healthRecord->record_date = $dob;
            $healthRecord->create();
        }

        setFlashMessage('Child profile created successfully!', 'success');
        redirect('/CHMS/child_profile.php?id=' . $child->child_id);
    } else {
        setFlashMessage('Failed to create child profile', 'error');
        redirect('/CHMS/add_child.php');
    }
}

/**
 * Handle update child
 */
function handleUpdate() {
    $childId = (int)($_POST['child_id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $dob = sanitize($_POST['dob'] ?? '');
    $gender = sanitize($_POST['gender'] ?? '');

    if ($childId <= 0) {
        setFlashMessage('Invalid child ID', 'error');
        redirect('/CHMS/index.php');
        return;
    }

    // Check ownership
    $childModel = new Child();
    $userRole = getCurrentUserRole();
    
    if ($userRole === 'mother') {
        if (!$childModel->isOwnedByMother($childId, getCurrentUserId())) {
            setFlashMessage('Unauthorized access', 'error');
            redirect('/CHMS/index.php');
            return;
        }
    }

    // Validate inputs
    if (empty($name) || empty($dob) || empty($gender)) {
        setFlashMessage('All fields are required', 'error');
        redirect('/CHMS/child_profile.php?id=' . $childId);
        return;
    }

    // Update child
    $childModel->child_id = $childId;
    $childModel->name = $name;
    $childModel->dob = $dob;
    $childModel->gender = $gender;

    if ($childModel->update()) {
        setFlashMessage('Child profile updated successfully!', 'success');
    } else {
        setFlashMessage('Failed to update child profile', 'error');
    }

    redirect('/CHMS/child_profile.php?id=' . $childId);
}

/**
 * Handle delete child
 */
function handleDelete() {
    $childId = (int)($_POST['child_id'] ?? 0);

    if ($childId <= 0) {
        setFlashMessage('Invalid child ID', 'error');
        redirect('/CHMS/index.php');
        return;
    }

    // Check ownership
    $childModel = new Child();
    $userRole = getCurrentUserRole();
    
    if ($userRole === 'mother') {
        if (!$childModel->isOwnedByMother($childId, getCurrentUserId())) {
            setFlashMessage('Unauthorized access', 'error');
            redirect('/CHMS/index.php');
            return;
        }
    }

    // Delete child
    $childModel->child_id = $childId;
    if ($childModel->delete()) {
        setFlashMessage('Child profile deleted successfully', 'success');
    } else {
        setFlashMessage('Failed to delete child profile', 'error');
    }

    redirect('/CHMS/index.php');
}
?>

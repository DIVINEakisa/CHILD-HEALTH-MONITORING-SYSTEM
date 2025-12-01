<?php
/**
 * Helper Functions
 * Child Health Monitoring System (CHMS)
 */

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    // Basic validation for phone numbers
    return preg_match('/^[+]?[0-9\s\-()]+$/', $phone);
}

/**
 * Calculate age in months
 * @param string $dob Date of birth (Y-m-d format)
 * @return int
 */
function calculateAgeInMonths($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $interval = $birthDate->diff($today);
    return ($interval->y * 12) + $interval->m;
}

/**
 * Calculate age string (e.g., "2 years 3 months")
 * @param string $dob Date of birth (Y-m-d format)
 * @return string
 */
function calculateAgeString($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $interval = $birthDate->diff($today);
    
    $years = $interval->y;
    $months = $interval->m;
    
    if ($years == 0) {
        return $months . " month" . ($months != 1 ? "s" : "");
    } elseif ($months == 0) {
        return $years . " year" . ($years != 1 ? "s" : "");
    } else {
        return $years . " year" . ($years != 1 ? "s" : "") . " " . 
               $months . " month" . ($months != 1 ? "s" : "");
    }
}

/**
 * Format date for display
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date)) return '';
    $dateTime = new DateTime($date);
    return $dateTime->format($format);
}

/**
 * Calculate BMI for children
 * @param float $weight Weight in kg
 * @param float $height Height in meters
 * @return float
 */
function calculateBMI($weight, $height) {
    if ($height <= 0) return 0;
    return round($weight / ($height * $height), 2);
}

/**
 * Assess growth status based on weight and height
 * This is a simplified assessment. Real-world implementation should use WHO growth charts
 * @param float $weight
 * @param float $height
 * @param int $ageMonths
 * @param string $gender
 * @return string
 */
function assessGrowthStatus($weight, $height, $ageMonths, $gender) {
    $bmi = calculateBMI($weight, $height);
    
    // Simplified thresholds (should use WHO growth charts in production)
    if ($bmi < 14) {
        return 'Underweight';
    } elseif ($bmi >= 14 && $bmi < 18) {
        return 'Normal';
    } elseif ($bmi >= 18 && $bmi < 20) {
        return 'Overweight';
    } else {
        return 'Obese';
    }
}

/**
 * Check if vaccination is overdue
 * @param string $nextDueDate
 * @return bool
 */
function isVaccinationOverdue($nextDueDate) {
    if (empty($nextDueDate)) return false;
    $dueDate = new DateTime($nextDueDate);
    $today = new DateTime();
    return $dueDate < $today;
}

/**
 * Generate unique ID
 * @return string
 */
function generateUniqueId() {
    return uniqid('chms_', true);
}

/**
 * JSON response helper
 * @param bool $success
 * @param mixed $data
 * @param string $message
 */
function jsonResponse($success, $data = null, $message = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit();
}

/**
 * Redirect helper
 * @param string $url
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Get base URL
 * @return string
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    return "$protocol://$host/CHMS";
}

/**
 * Debug helper - only use in development
 * @param mixed $data
 */
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * Generate alert based on health metrics
 * @param int $childId
 * @param float $weight
 * @param float $height
 * @param int $ageMonths
 * @param string $gender
 * @return array|null
 */
function generateHealthAlert($childId, $weight, $height, $ageMonths, $gender) {
    $status = assessGrowthStatus($weight, $height, $ageMonths, $gender);
    
    if ($status === 'Underweight') {
        return [
            'child_id' => $childId,
            'alert_type' => 'underweight',
            'message' => "Child is underweight. Nutritional assessment recommended."
        ];
    } elseif ($status === 'Overweight' || $status === 'Obese') {
        return [
            'child_id' => $childId,
            'alert_type' => 'overweight',
            'message' => "Child is overweight. Dietary consultation recommended."
        ];
    }
    
    return null;
}

/**
 * Validate date format
 * @param string $date
 * @param string $format
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Get avatar initials
 * @param string $name
 * @return string
 */
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return substr($initials, 0, 2);
}
?>

<?php
/**
 * Add Mother Health Record Page
 * Allows doctors to add health records for mothers
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/helpers.php';
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/MotherHealthRecord.php';

requireLogin();
requireRole('doctor'); // Only doctors can add mother health records

$userId = getCurrentUserId();
$userName = getCurrentUserName();

// Get mother_id from URL
$motherId = (int)($_GET['mother_id'] ?? 0);

// Fetch mother details
$userModel = new User();
$userModel->user_id = $motherId;
$mother = $userModel->readOne();

if (!$mother || $mother['role'] !== 'mother') {
    setFlashMessage('Invalid mother selected', 'error');
    redirect('/CHMS/index.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $record = new MotherHealthRecord();
    
    $record->mother_id = $motherId;
    $record->record_type = $_POST['record_type'] ?? 'general';
    $record->record_date = $_POST['record_date'] ?? date('Y-m-d');
    $record->weight = !empty($_POST['weight']) ? $_POST['weight'] : null;
    $record->blood_pressure = !empty($_POST['blood_pressure']) ? $_POST['blood_pressure'] : null;
    $record->hemoglobin = !empty($_POST['hemoglobin']) ? $_POST['hemoglobin'] : null;
    $record->blood_sugar = !empty($_POST['blood_sugar']) ? $_POST['blood_sugar'] : null;
    $record->pregnancy_week = !empty($_POST['pregnancy_week']) ? $_POST['pregnancy_week'] : null;
    $record->delivery_date = !empty($_POST['delivery_date']) ? $_POST['delivery_date'] : null;
    $record->delivery_type = !empty($_POST['delivery_type']) ? $_POST['delivery_type'] : null;
    $record->complications = $_POST['complications'] ?? null;
    $record->medications = $_POST['medications'] ?? null;
    $record->doctor_notes = $_POST['doctor_notes'] ?? null;
    $record->next_checkup_date = !empty($_POST['next_checkup_date']) ? $_POST['next_checkup_date'] : null;

    if ($record->create()) {
        setFlashMessage('Health record added successfully', 'success');
        redirect('/CHMS/mother_profile.php?id=' . $motherId);
    } else {
        setFlashMessage('Failed to add health record', 'error');
    }
}

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Mother Health Record - CHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if ($flashMessage): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $flashMessage['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'; ?>">
                <?php echo htmlspecialchars($flashMessage['message']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Add Health Record</h1>
                <p class="mt-2 text-sm text-gray-600">
                    For: <span class="font-medium text-gray-900"><?php echo htmlspecialchars($mother['name']); ?></span>
                </p>
            </div>

            <form method="POST" id="healthRecordForm" class="space-y-6">
                <!-- Record Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Record Type *</label>
                    <select name="record_type" id="record_type" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="general">General Health</option>
                        <option value="prenatal">Prenatal (During Pregnancy)</option>
                        <option value="postnatal">Postnatal (After Delivery)</option>
                    </select>
                </div>

                <!-- Record Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Record Date *</label>
                    <input type="date" name="record_date" required value="<?php echo date('Y-m-d'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Basic Vitals -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                        <input type="number" name="weight" step="0.01" min="0" max="200" placeholder="e.g., 65.5"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                        <input type="text" name="blood_pressure" placeholder="e.g., 120/80" pattern="[0-9]{2,3}/[0-9]{2,3}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Format: systolic/diastolic (e.g., 120/80)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hemoglobin (g/dL)</label>
                        <input type="number" name="hemoglobin" step="0.01" min="0" max="20" placeholder="e.g., 12.5"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Blood Sugar (mg/dL)</label>
                        <input type="number" name="blood_sugar" step="0.01" min="0" max="500" placeholder="e.g., 95.0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Prenatal-specific fields (hidden by default) -->
                <div id="prenatalFields" class="hidden space-y-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-blue-900 mb-3">Prenatal Information</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pregnancy Week</label>
                            <input type="number" name="pregnancy_week" min="1" max="42" placeholder="e.g., 20"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Postnatal-specific fields (hidden by default) -->
                <div id="postnatalFields" class="hidden space-y-6">
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-purple-900 mb-3">Postnatal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                                <input type="date" name="delivery_date"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Type</label>
                                <select name="delivery_type"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select type</option>
                                    <option value="normal">Normal Delivery</option>
                                    <option value="cesarean">Cesarean (C-Section)</option>
                                    <option value="assisted">Assisted Delivery</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Complications / Concerns</label>
                    <textarea name="complications" rows="3" placeholder="Any complications or health concerns..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                    <textarea name="medications" rows="3" placeholder="List current medications..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Doctor's Notes</label>
                    <textarea name="doctor_notes" rows="4" placeholder="Enter clinical observations and recommendations..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <!-- Next Checkup -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Next Checkup Date</label>
                    <input type="date" name="next_checkup_date" min="<?php echo date('Y-m-d'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="mother_profile.php?id=<?php echo $motherId; ?>" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Add Health Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show/hide fields based on record type
        const recordTypeSelect = document.getElementById('record_type');
        const prenatalFields = document.getElementById('prenatalFields');
        const postnatalFields = document.getElementById('postnatalFields');

        recordTypeSelect.addEventListener('change', function() {
            const recordType = this.value;
            
            if (recordType === 'prenatal') {
                prenatalFields.classList.remove('hidden');
                postnatalFields.classList.add('hidden');
            } else if (recordType === 'postnatal') {
                prenatalFields.classList.add('hidden');
                postnatalFields.classList.remove('hidden');
            } else {
                prenatalFields.classList.add('hidden');
                postnatalFields.classList.add('hidden');
            }
        });

        // Form validation
        document.getElementById('healthRecordForm').addEventListener('submit', function(e) {
            const weight = document.querySelector('[name="weight"]').value;
            const bloodPressure = document.querySelector('[name="blood_pressure"]').value;
            const hemoglobin = document.querySelector('[name="hemoglobin"]').value;
            const bloodSugar = document.querySelector('[name="blood_sugar"]').value;

            if (!weight && !bloodPressure && !hemoglobin && !bloodSugar) {
                e.preventDefault();
                alert('Please enter at least one vital measurement (weight, blood pressure, hemoglobin, or blood sugar)');
                return false;
            }
        });
    </script>
</body>
</html>

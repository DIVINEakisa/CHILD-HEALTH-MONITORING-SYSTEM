<?php
/**
 * Child Profile Page
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/helpers.php';
require_once __DIR__ . '/src/models/Child.php';
require_once __DIR__ . '/src/models/HealthRecord.php';
require_once __DIR__ . '/src/models/Immunization.php';
require_once __DIR__ . '/src/models/Alert.php';

requireLogin();

$childId = (int)($_GET['id'] ?? 0);
if ($childId <= 0) {
    setFlashMessage('Invalid child ID', 'error');
    redirect('/CHMS/index.php');
}

$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();

// Get child data
$childModel = new Child();
$childModel->child_id = $childId;
$child = $childModel->readOne();

if (!$child) {
    setFlashMessage('Child not found', 'error');
    redirect('/CHMS/index.php');
}

// Check access
if ($userRole === 'mother' && $child['mother_id'] != $userId) {
    setFlashMessage('Unauthorized access', 'error');
    redirect('/CHMS/index.php');
}

// Get health records, immunizations, alerts
$healthRecordModel = new HealthRecord();
$healthRecords = $healthRecordModel->readByChild($childId);
$growthTrend = $healthRecordModel->getGrowthTrend($childId);

$immunizationModel = new Immunization();
$immunizations = $immunizationModel->readByChild($childId);

$alertModel = new Alert();
$alerts = $alertModel->readAll(null, $childId);

$flashMessage = getFlashMessage();
$ageString = calculateAgeString($child['dob']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($child['name']); ?> - Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if ($flashMessage): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $flashMessage['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'; ?>">
                <?php echo htmlspecialchars($flashMessage['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Child Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-20 w-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                        <?php echo strtoupper(substr($child['name'], 0, 1)); ?>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($child['name']); ?></h1>
                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                            <span><?php echo ucfirst($child['gender']); ?></span>
                            <span>•</span>
                            <span><?php echo $ageString; ?> old</span>
                            <span>•</span>
                            <span>Born: <?php echo formatDate($child['dob']); ?></span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Mother: <?php echo htmlspecialchars($child['mother_name']); ?></p>
                    </div>
                </div>
                <?php if ($userRole === 'doctor'): ?>
                    <a href="add_health_record.php?child_id=<?php echo $childId; ?>" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Add Health Record
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Growth Charts -->
                <?php if (!empty($growthTrend)): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Growth Trends</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Weight Trend</h3>
                                <canvas id="weightChart"></canvas>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Height Trend</h3>
                                <canvas id="heightChart"></canvas>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Health Records -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Health Records</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($healthRecords)): ?>
                            <p class="text-sm text-gray-500">No health records yet</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($healthRecords as $record): ?>
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900"><?php echo formatDate($record['record_date']); ?></p>
                                                <div class="mt-2 grid grid-cols-3 gap-4">
                                                    <div>
                                                        <p class="text-xs text-gray-500">Weight</p>
                                                        <p class="text-sm font-semibold text-gray-900"><?php echo $record['weight']; ?> kg</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500">Height</p>
                                                        <p class="text-sm font-semibold text-gray-900"><?php echo $record['height']; ?> m</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500">Nutrition Status</p>
                                                        <p class="text-sm font-semibold text-gray-900"><?php echo $record['nutrition_status']; ?></p>
                                                    </div>
                                                </div>
                                                <?php if (!empty($record['doctor_notes'])): ?>
                                                    <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                                        <p class="text-xs text-gray-500">Doctor's Notes</p>
                                                        <p class="text-sm text-gray-700 mt-1"><?php echo htmlspecialchars($record['doctor_notes']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Immunization Records -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Immunizations</h2>
                        <?php if ($userRole === 'doctor'): ?>
                            <a href="add_immunization.php?child_id=<?php echo $childId; ?>" 
                               class="text-sm text-blue-600 hover:text-blue-800">Add</a>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <?php if (empty($immunizations)): ?>
                            <p class="text-sm text-gray-500">No immunization records</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($immunizations as $imm): ?>
                                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                        <p class="text-sm font-medium text-green-900"><?php echo htmlspecialchars($imm['vaccine_name']); ?></p>
                                        <p class="text-xs text-green-700 mt-1">Given: <?php echo formatDate($imm['date_given']); ?></p>
                                        <?php if ($imm['next_due_date']): ?>
                                            <p class="text-xs text-green-600 mt-1">Next due: <?php echo formatDate($imm['next_due_date']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Alerts -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Alerts</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($alerts)): ?>
                            <p class="text-sm text-gray-500">No alerts</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($alerts as $alert): ?>
                                    <div class="p-3 <?php echo $alert['status'] === 'pending' ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200'; ?> border rounded-lg">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="text-sm font-medium <?php echo $alert['status'] === 'pending' ? 'text-red-800' : 'text-gray-700'; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $alert['alert_type'])); ?>
                                                </p>
                                                <p class="text-xs <?php echo $alert['status'] === 'pending' ? 'text-red-600' : 'text-gray-500'; ?> mt-1">
                                                    <?php echo htmlspecialchars($alert['message']); ?>
                                                </p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $alert['status'] === 'pending' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'; ?>">
                                                <?php echo ucfirst($alert['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/charts.js"></script>
    <script>
        // Growth chart data
        const growthData = <?php echo json_encode($growthTrend); ?>;
        
        if (growthData && growthData.length > 0) {
            const dates = growthData.map(item => item.record_date);
            const weights = growthData.map(item => parseFloat(item.weight));
            const heights = growthData.map(item => parseFloat(item.height));

            // Weight Chart
            createLineChart('weightChart', dates, weights, 'Weight (kg)', 'rgb(59, 130, 246)');

            // Height Chart
            createLineChart('heightChart', dates, heights, 'Height (m)', 'rgb(16, 185, 129)');
        }
    </script>
</body>
</html>

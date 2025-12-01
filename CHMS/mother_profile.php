<?php
/**
 * Mother Profile Page
 * Displays mother's health records and history
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/helpers.php';
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/MotherHealthRecord.php';
require_once __DIR__ . '/src/models/Child.php';

requireLogin();

$motherId = (int)($_GET['id'] ?? 0);
if ($motherId <= 0) {
    setFlashMessage('Invalid mother ID', 'error');
    redirect('/CHMS/index.php');
}

$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();

// Get mother data
$userModel = new User();
$userModel->user_id = $motherId;
$mother = $userModel->readOne();

if (!$mother || $mother['role'] !== 'mother') {
    setFlashMessage('Mother not found', 'error');
    redirect('/CHMS/index.php');
}

// Check access - mothers can only see their own profile
if ($userRole === 'mother' && $motherId != $userId) {
    setFlashMessage('Unauthorized access', 'error');
    redirect('/CHMS/index.php');
}

// Get mother's health records
$healthRecordModel = new MotherHealthRecord();
$healthRecords = $healthRecordModel->readByMother($motherId);
$healthTrend = $healthRecordModel->getHealthTrend($motherId, 12);

// Get mother's children
$childModel = new Child();
$children = $childModel->readAll($motherId);

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($mother['name']); ?> - Health Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .record-card {
            transition: all 0.2s ease;
        }
        .record-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
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

        <!-- Mother Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-20 w-20 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                        <?php echo strtoupper(substr($mother['name'], 0, 1)); ?>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($mother['name']); ?></h1>
                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                            <span><?php echo htmlspecialchars($mother['email']); ?></span>
                            <?php if (!empty($mother['phone'])): ?>
                                <span>•</span>
                                <span><?php echo htmlspecialchars($mother['phone']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ($userRole === 'doctor'): ?>
                    <a href="add_mother_health_record.php?mother_id=<?php echo $motherId; ?>" 
                       class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Add Health Record
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Health Trends -->
                <?php if (!empty($healthTrend) && count($healthTrend) > 1): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Health Trends</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Weight Trend</h3>
                                <canvas id="weightChart"></canvas>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Hemoglobin Trend</h3>
                                <canvas id="hemoglobinChart"></canvas>
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
                                    <div class="record-card border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900"><?php echo formatDate($record['record_date']); ?></p>
                                                <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full 
                                                    <?php 
                                                        $colors = [
                                                            'prenatal' => 'bg-blue-100 text-blue-800',
                                                            'postnatal' => 'bg-purple-100 text-purple-800',
                                                            'general' => 'bg-green-100 text-green-800'
                                                        ];
                                                        echo $colors[$record['record_type']] ?? 'bg-gray-100 text-gray-800';
                                                    ?>">
                                                    <?php echo ucfirst($record['record_type']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-3">
                                            <?php if ($record['weight']): ?>
                                                <div>
                                                    <p class="text-xs text-gray-500">Weight</p>
                                                    <p class="text-sm font-semibold text-gray-900"><?php echo $record['weight']; ?> kg</p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($record['blood_pressure']): ?>
                                                <div>
                                                    <p class="text-xs text-gray-500">Blood Pressure</p>
                                                    <p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($record['blood_pressure']); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($record['hemoglobin']): ?>
                                                <div>
                                                    <p class="text-xs text-gray-500">Hemoglobin</p>
                                                    <p class="text-sm font-semibold text-gray-900"><?php echo $record['hemoglobin']; ?> g/dL</p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($record['blood_sugar']): ?>
                                                <div>
                                                    <p class="text-xs text-gray-500">Blood Sugar</p>
                                                    <p class="text-sm font-semibold text-gray-900"><?php echo $record['blood_sugar']; ?> mg/dL</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($record['pregnancy_week']): ?>
                                            <div class="mb-2">
                                                <p class="text-xs text-gray-500">Pregnancy Week</p>
                                                <p class="text-sm font-semibold text-blue-900">Week <?php echo $record['pregnancy_week']; ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($record['delivery_date']): ?>
                                            <div class="mb-2 grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-xs text-gray-500">Delivery Date</p>
                                                    <p class="text-sm font-semibold text-purple-900"><?php echo formatDate($record['delivery_date']); ?></p>
                                                </div>
                                                <?php if ($record['delivery_type']): ?>
                                                    <div>
                                                        <p class="text-xs text-gray-500">Delivery Type</p>
                                                        <p class="text-sm font-semibold text-purple-900"><?php echo ucfirst($record['delivery_type']); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['complications'])): ?>
                                            <div class="mt-3 p-3 bg-red-50 rounded-lg">
                                                <p class="text-xs font-medium text-red-700">Complications</p>
                                                <p class="text-sm text-red-800 mt-1"><?php echo htmlspecialchars($record['complications']); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['medications'])): ?>
                                            <div class="mt-3 p-3 bg-yellow-50 rounded-lg">
                                                <p class="text-xs font-medium text-yellow-700">Medications</p>
                                                <p class="text-sm text-yellow-800 mt-1"><?php echo htmlspecialchars($record['medications']); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($record['doctor_notes'])): ?>
                                            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                                <p class="text-xs font-medium text-blue-700">Doctor's Notes</p>
                                                <p class="text-sm text-blue-800 mt-1"><?php echo htmlspecialchars($record['doctor_notes']); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($record['next_checkup_date']): ?>
                                            <div class="mt-3 flex items-center text-sm text-gray-600">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Next checkup: <?php echo formatDate($record['next_checkup_date']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Children -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Children</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($children)): ?>
                            <p class="text-sm text-gray-500">No children registered</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($children as $child): ?>
                                    <a href="child_profile.php?id=<?php echo $child['child_id']; ?>" 
                                       class="block p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                                        <p class="text-sm font-medium text-blue-900"><?php echo htmlspecialchars($child['name']); ?></p>
                                        <p class="text-xs text-blue-700 mt-1">
                                            <?php echo ucfirst($child['gender']); ?> • 
                                            <?php echo calculateAgeString($child['dob']); ?> old
                                        </p>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($userRole === 'mother'): ?>
                            <a href="add_child.php" 
                               class="mt-4 block text-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                Add New Child
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Latest Stats -->
                <?php 
                $latestRecord = !empty($healthRecords) ? $healthRecords[0] : null;
                if ($latestRecord): 
                ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Latest Vitals</h2>
                        <div class="space-y-3">
                            <?php if ($latestRecord['weight']): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Weight</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo $latestRecord['weight']; ?> kg</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($latestRecord['blood_pressure']): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">BP</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($latestRecord['blood_pressure']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($latestRecord['hemoglobin']): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Hemoglobin</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo $latestRecord['hemoglobin']; ?> g/dL</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($latestRecord['blood_sugar']): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Blood Sugar</span>
                                    <span class="text-sm font-semibold text-gray-900"><?php echo $latestRecord['blood_sugar']; ?> mg/dL</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500">Last updated: <?php echo formatDate($latestRecord['record_date']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="assets/js/charts.js"></script>
    <script>
        // Health trend data
        const healthTrendData = <?php echo json_encode($healthTrend); ?>;
        
        if (healthTrendData && healthTrendData.length > 1) {
            const dates = healthTrendData.map(item => item.record_date);
            const weights = healthTrendData.map(item => item.weight ? parseFloat(item.weight) : null);
            const hemoglobinLevels = healthTrendData.map(item => item.hemoglobin ? parseFloat(item.hemoglobin) : null);

            // Weight Chart
            if (weights.some(w => w !== null)) {
                createLineChart('weightChart', dates, weights, 'Weight (kg)', 'rgb(147, 51, 234)');
            }

            // Hemoglobin Chart
            if (hemoglobinLevels.some(h => h !== null)) {
                createLineChart('hemoglobinChart', dates, hemoglobinLevels, 'Hemoglobin (g/dL)', 'rgb(239, 68, 68)');
            }
        }
    </script>
</body>
</html>

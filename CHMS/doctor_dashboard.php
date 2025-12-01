<?php
/**
 * Doctor Dashboard
 * Child Health Monitoring System
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/helpers.php';
require_once __DIR__ . '/src/models/Child.php';
require_once __DIR__ . '/src/models/HealthRecord.php';
require_once __DIR__ . '/src/models/Alert.php';
require_once __DIR__ . '/src/models/Immunization.php';
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/MotherHealthRecord.php';

requireRole('doctor');

$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();

// Get statistics
$childModel = new Child();
$healthRecordModel = new HealthRecord();
$alertModel = new Alert();
$immunizationModel = new Immunization();
$userModel = new User();
$motherHealthModel = new MotherHealthRecord();

$childStats = $childModel->getStatistics();
$healthStats = $healthRecordModel->getStatistics();
$alertStats = $alertModel->getStatistics();
$immunizationStats = $immunizationModel->getStatistics();
$motherStats = $userModel->getStatistics();
$motherHealthStats = $motherHealthModel->getStatistics();

// Get recent data
$recentChildren = $childModel->readAll();
$childrenList = [];
$count = 0;
while ($row = $recentChildren->fetch(PDO::FETCH_ASSOC)) {
    if ($count < 10) {
        $childrenList[] = $row;
    }
    $count++;
}

$recentRecords = $healthRecordModel->readAll(10);
$pendingAlerts = $alertModel->readAll('pending');
$overdueVaccinations = $immunizationModel->getOverdue();

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - CHMS</title>
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

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Doctor Dashboard</h1>
            <p class="mt-2 text-gray-600">Monitor and manage children's health records</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Children</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $childStats['total_children'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Mothers</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $motherStats['mother'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Health Records</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $healthStats['total_records'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Alerts</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $alertStats['pending'] ?? 0; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Overdue Vaccines</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($overdueVaccinations); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="all_children.php" class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 hover:shadow-lg transition text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold">View All Children</h3>
                        <p class="mt-2 text-blue-100">Monitor and manage child health records</p>
                    </div>
                    <svg class="h-10 w-10 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </a>

            <a href="all_mothers.php" class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-6 hover:shadow-lg transition text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold">View All Mothers</h3>
                        <p class="mt-2 text-purple-100">Manage mother health records</p>
                    </div>
                    <svg class="h-10 w-10 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Children -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Children</h2>
                        <a href="all_children.php" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Child Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mother</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($childrenList as $child): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($child['name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600"><?php echo $child['age_months']; ?> months</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600"><?php echo ucfirst($child['gender']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600"><?php echo htmlspecialchars($child['mother_name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="child_profile.php?id=<?php echo $child['child_id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Health Records -->
                <div class="bg-white rounded-lg shadow mt-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Health Records</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($recentRecords)): ?>
                            <p class="text-sm text-gray-500">No recent records</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($recentRecords as $record): ?>
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($record['child_name']); ?></h3>
                                                <p class="text-xs text-gray-500 mt-1"><?php echo formatDate($record['record_date']); ?></p>
                                                <div class="mt-2 flex space-x-4 text-xs">
                                                    <span class="text-gray-600">Weight: <?php echo $record['weight']; ?> kg</span>
                                                    <span class="text-gray-600">Height: <?php echo $record['height']; ?> m</span>
                                                </div>
                                            </div>
                                            <a href="child_profile.php?id=<?php echo $record['child_id']; ?>" 
                                               class="text-sm text-blue-600 hover:text-blue-800">View</a>
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
                <!-- Pending Alerts -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Pending Alerts</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($pendingAlerts)): ?>
                            <p class="text-sm text-gray-500">No pending alerts</p>
                        <?php else: ?>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                <?php foreach ($pendingAlerts as $alert): ?>
                                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="h-5 w-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($alert['child_name']); ?></p>
                                                <p class="text-xs text-red-600 mt-1"><?php echo htmlspecialchars($alert['message']); ?></p>
                                                <a href="child_profile.php?id=<?php echo $alert['child_id']; ?>" 
                                                   class="text-xs text-red-700 hover:text-red-900 mt-1 inline-block">View →</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Overdue Vaccinations -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Overdue Vaccinations</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($overdueVaccinations)): ?>
                            <p class="text-sm text-gray-500">No overdue vaccinations</p>
                        <?php else: ?>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                <?php foreach ($overdueVaccinations as $vaccination): ?>
                                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-sm font-medium text-yellow-900"><?php echo htmlspecialchars($vaccination['child_name']); ?></p>
                                        <p class="text-xs text-yellow-700 mt-1"><?php echo htmlspecialchars($vaccination['vaccine_name']); ?></p>
                                        <p class="text-xs text-yellow-600 mt-1">
                                            Overdue by <?php echo $vaccination['days_overdue']; ?> day<?php echo $vaccination['days_overdue'] != 1 ? 's' : ''; ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Main Dashboard - Mother View
 * Child Health Monitoring System
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/config/helpers.php';
require_once __DIR__ . '/src/models/Child.php';
require_once __DIR__ . '/src/models/Alert.php';
require_once __DIR__ . '/src/models/Immunization.php';

// Redirect to home page if not logged in
if (!isLoggedIn()) {
    redirect('/CHMS/home.php');
}

requireLogin();

$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();

// Get data
$childModel = new Child();
$alertModel = new Alert();
$immunizationModel = new Immunization();

if ($userRole === 'mother') {
    $children = $childModel->getChildrenWithLatestRecords($userId);
    $alerts = $alertModel->readAll('pending', null, $userId);
    $upcomingVaccinations = $immunizationModel->getUpcoming($userId, 30);
} else {
    // Redirect doctors to their dashboard
    redirect('/CHMS/doctor_dashboard.php');
}

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Message -->
        <?php if ($flashMessage): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $flashMessage['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'; ?>">
                <?php echo htmlspecialchars($flashMessage['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Welcome, <?php echo htmlspecialchars($userName); ?></h1>
            <p class="mt-2 text-gray-600">Monitor your children's health and growth</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Children</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($children); ?></p>
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
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($alerts); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Upcoming Vaccinations</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($upcomingVaccinations); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Children List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">My Children</h2>
                        <a href="add_child.php" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Add Child
                        </a>
                    </div>
                    <div class="p-6">
                        <?php if (empty($children)): ?>
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No children added</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding your child's profile</p>
                                <div class="mt-6">
                                    <a href="add_child.php" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Add First Child
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($children as $child): ?>
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="h-12 w-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                                    <?php echo strtoupper(substr($child['name'], 0, 1)); ?>
                                                </div>
                                                <div class="ml-4">
                                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($child['name']); ?></h3>
                                                    <p class="text-sm text-gray-600">
                                                        <?php echo ucfirst($child['gender']); ?> • 
                                                        <?php echo calculateAgeString($child['dob']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="child_profile.php?id=<?php echo $child['child_id']; ?>" 
                                               class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                                View Profile
                                            </a>
                                        </div>
                                        <?php if (isset($child['weight'])): ?>
                                            <div class="mt-4 grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                                                <div>
                                                    <p class="text-xs text-gray-500">Latest Weight</p>
                                                    <p class="text-sm font-semibold text-gray-900"><?php echo $child['weight']; ?> kg</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Latest Height</p>
                                                    <p class="text-sm font-semibold text-gray-900"><?php echo $child['height']; ?> m</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Last Checkup</p>
                                                    <p class="text-sm font-semibold text-gray-900"><?php echo formatDate($child['record_date']); ?></p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="mt-4 text-sm text-gray-500 italic">No health records yet</div>
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
                <!-- Alerts -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Alerts</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($alerts)): ?>
                            <p class="text-sm text-gray-500">No pending alerts</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach (array_slice($alerts, 0, 5) as $alert): ?>
                                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="h-5 w-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($alert['child_name']); ?></p>
                                                <p class="text-xs text-red-600 mt-1"><?php echo htmlspecialchars($alert['message']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($alerts) > 5): ?>
                                <a href="alerts.php" class="mt-4 block text-sm text-blue-600 hover:text-blue-800">View all alerts →</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upcoming Vaccinations -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Upcoming Vaccinations</h2>
                    </div>
                    <div class="p-6">
                        <?php if (empty($upcomingVaccinations)): ?>
                            <p class="text-sm text-gray-500">No upcoming vaccinations</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach (array_slice($upcomingVaccinations, 0, 5) as $vaccination): ?>
                                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                        <p class="text-sm font-medium text-green-900"><?php echo htmlspecialchars($vaccination['child_name']); ?></p>
                                        <p class="text-xs text-green-700 mt-1"><?php echo htmlspecialchars($vaccination['vaccine_name']); ?></p>
                                        <p class="text-xs text-green-600 mt-1">Due: <?php echo formatDate($vaccination['next_due_date']); ?></p>
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

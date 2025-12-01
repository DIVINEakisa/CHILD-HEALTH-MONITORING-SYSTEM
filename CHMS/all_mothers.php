<?php
/**
 * All Mothers Page
 * Displays list of all mothers with quick access to their health records
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/helpers.php';
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/MotherHealthRecord.php';
require_once __DIR__ . '/src/models/Child.php';

requireLogin();

$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();

// Get all mothers
$userModel = new User();
$mothersResult = $userModel->readAll('mother');
$mothers = $mothersResult->fetchAll(PDO::FETCH_ASSOC);

// Get child count for each mother
$childModel = new Child();
$healthRecordModel = new MotherHealthRecord();

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Mothers - CHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">All Mothers</h1>
            <p class="mt-2 text-sm text-gray-600">View and manage mother health records</p>
        </div>

        <?php if (empty($mothers)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500">No mothers registered in the system yet.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($mothers as $mother): ?>
                    <?php
                        $childrenResult = $childModel->readAll($mother['user_id']);
                        $childCount = count($childrenResult);
                        $latestRecord = $healthRecordModel->getLatestRecord($mother['user_id']);
                    ?>
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                    <?php echo strtoupper(substr($mother['name'], 0, 1)); ?>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($mother['name']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($mother['email']); ?></p>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4">
                                <?php if (!empty($mother['phone'])): ?>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Phone:</span> <?php echo htmlspecialchars($mother['phone']); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Children:</span> <?php echo $childCount; ?>
                                </p>
                                <?php if ($latestRecord): ?>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Last Checkup:</span> 
                                        <?php echo formatDate($latestRecord['record_date']); ?>
                                    </p>
                                    <?php if ($latestRecord['next_checkup_date']): ?>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Next Checkup:</span> 
                                            <?php echo formatDate($latestRecord['next_checkup_date']); ?>
                                        </p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-sm text-red-600 font-medium">No health records yet</p>
                                <?php endif; ?>
                            </div>

                            <div class="flex space-x-2">
                                <a href="mother_profile.php?id=<?php echo $mother['user_id']; ?>" 
                                   class="flex-1 px-4 py-2 bg-purple-600 text-white text-center text-sm rounded-lg hover:bg-purple-700 transition">
                                    View Profile
                                </a>
                                <?php if ($userRole === 'doctor'): ?>
                                    <a href="add_mother_health_record.php?mother_id=<?php echo $mother['user_id']; ?>" 
                                       class="px-4 py-2 bg-white border border-purple-600 text-purple-600 text-sm rounded-lg hover:bg-purple-50 transition">
                                        Add Record
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
/**
 * All Children - View all registered children
 * Available for doctors to see all children in the system
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/helpers.php';
require_once __DIR__ . '/src/models/Child.php';
require_once __DIR__ . '/src/models/HealthRecord.php';

requireRole('doctor');

$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();

// Get search and filter parameters
$searchTerm = $_GET['search'] ?? '';
$genderFilter = $_GET['gender'] ?? '';
$ageFilter = $_GET['age'] ?? '';

// Get all children
$childModel = new Child();

if (!empty($searchTerm)) {
    $children = $childModel->search($searchTerm);
} else {
    $stmt = $childModel->readAll();
    $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Apply filters
if (!empty($genderFilter)) {
    $children = array_filter($children, function($child) use ($genderFilter) {
        return $child['gender'] === $genderFilter;
    });
}

if (!empty($ageFilter)) {
    $children = array_filter($children, function($child) use ($ageFilter) {
        $months = $child['age_months'];
        switch($ageFilter) {
            case '0-6': return $months <= 6;
            case '7-12': return $months >= 7 && $months <= 12;
            case '13-24': return $months >= 13 && $months <= 24;
            case '25+': return $months >= 25;
            default: return true;
        }
    });
}

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Children - CHMS</title>
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

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">All Children</h1>
            <p class="mt-2 text-gray-600">Browse and manage all registered children in the system</p>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-600">Total Children</div>
                <div class="text-2xl font-bold text-gray-900 mt-2"><?php echo count($children); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-600">Male</div>
                <div class="text-2xl font-bold text-blue-600 mt-2">
                    <?php echo count(array_filter($children, fn($c) => $c['gender'] === 'male')); ?>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-600">Female</div>
                <div class="text-2xl font-bold text-pink-600 mt-2">
                    <?php echo count(array_filter($children, fn($c) => $c['gender'] === 'female')); ?>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-600">Avg Age</div>
                <div class="text-2xl font-bold text-green-600 mt-2">
                    <?php 
                    $totalMonths = array_sum(array_column($children, 'age_months'));
                    $avgMonths = !empty($children) ? round($totalMonths / count($children)) : 0;
                    echo $avgMonths . ' mo';
                    ?>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search by Name</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>"
                           placeholder="Enter child name..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select id="gender" name="gender"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="male" <?php echo $genderFilter === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $genderFilter === 'female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age Range</label>
                    <select id="age" name="age"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Ages</option>
                        <option value="0-6" <?php echo $ageFilter === '0-6' ? 'selected' : ''; ?>>0-6 months</option>
                        <option value="7-12" <?php echo $ageFilter === '7-12' ? 'selected' : ''; ?>>7-12 months</option>
                        <option value="13-24" <?php echo $ageFilter === '13-24' ? 'selected' : ''; ?>>13-24 months</option>
                        <option value="25+" <?php echo $ageFilter === '25+' ? 'selected' : ''; ?>>25+ months</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Apply Filters
                    </button>
                    <a href="all_children.php" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Children List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">
                    Children List (<?php echo count($children); ?>)
                </h2>
                <a href="reports.php" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                    View Reports
                </a>
            </div>

            <?php if (empty($children)): ?>
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No children found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Child</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mother</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($children as $child): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                                <?php echo strtoupper(substr($child['name'], 0, 1)); ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($child['name']); ?></div>
                                                <div class="text-xs text-gray-500">ID: <?php echo $child['child_id']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $child['gender'] === 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'; ?>">
                                            <?php echo ucfirst($child['gender']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo calculateAgeString($child['dob']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo formatDate($child['dob']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($child['mother_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo htmlspecialchars($child['mother_email']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="child_profile.php?id=<?php echo $child['child_id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900">View Profile</a>
                                        <a href="add_health_record.php?child_id=<?php echo $child['child_id']; ?>" 
                                           class="text-green-600 hover:text-green-900">Add Record</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

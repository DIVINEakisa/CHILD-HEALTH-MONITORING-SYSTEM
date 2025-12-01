<?php
/**
 * Add Child Form
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/helpers.php';

requireRole('mother');

$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Child - CHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/navbar.php'; ?>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Add New Child</h1>
            <p class="mt-2 text-gray-600">Enter your child's information to start monitoring their health</p>
        </div>

        <?php if ($flashMessage): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $flashMessage['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'; ?>">
                <?php echo htmlspecialchars($flashMessage['message']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="src/controllers/child_controller.php" method="POST" class="space-y-6">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Child's Full Name *</label>
                    <input type="text" id="name" name="name" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                    <input type="date" id="dob" name="dob" required max="<?php echo date('Y-m-d'); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                    <select id="gender" name="gender" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Initial Health Information (Optional)</h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700">Birth Weight (kg)</label>
                            <input type="number" id="weight" name="weight" step="0.01" min="0" max="20"
                                   placeholder="e.g., 3.2"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700">Birth Height (m)</label>
                            <input type="number" id="height" name="height" step="0.01" min="0" max="2"
                                   placeholder="e.g., 0.50"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-6">
                    <a href="index.php" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 transition">
                        Add Child
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Reports - Vaccination and Health Reports
 * Shows vaccination coverage, missing vaccines, and health statistics
 */

require_once __DIR__ . '/src/config/session.php';
require_once __DIR__ . '/src/config/helpers.php';
require_once __DIR__ . '/src/models/Child.php';
require_once __DIR__ . '/src/models/Immunization.php';
require_once __DIR__ . '/src/models/HealthRecord.php';

requireRole('doctor');

$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();

// Get report type
$reportType = $_GET['type'] ?? 'vaccination';

// Get all children
$childModel = new Child();
$immunizationModel = new Immunization();
$healthRecordModel = new HealthRecord();

$stmt = $childModel->readAll();
$allChildren = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vaccination report data
$vaccinationData = [];
$vaccineTypes = [
    'BCG' => 'BCG',
    'Hepatitis B' => 'Hepatitis B',
    'DPT' => 'DPT',
    'Polio' => 'Polio',
    'Measles' => 'Measles',
    'MMR' => 'MMR'
];

foreach ($allChildren as $child) {
    $childId = $child['child_id'];
    $immunizations = $immunizationModel->readByChild($childId);
    
    $givenVaccines = [];
    foreach ($immunizations as $imm) {
        foreach ($vaccineTypes as $key => $vaccine) {
            if (stripos($imm['vaccine_name'], $key) !== false) {
                $givenVaccines[$key] = true;
            }
        }
    }
    
    $vaccinationData[] = [
        'child' => $child,
        'given_vaccines' => $givenVaccines,
        'total_vaccines' => count($givenVaccines),
        'immunizations' => $immunizations
    ];
}

// Calculate statistics
$totalVaccinesGiven = array_sum(array_column($vaccinationData, 'total_vaccines'));
$averageVaccinesPerChild = !empty($vaccinationData) ? round($totalVaccinesGiven / count($vaccinationData), 1) : 0;

// Children without specific vaccines
$childrenMissingVaccines = [];
foreach ($vaccineTypes as $key => $vaccine) {
    $missing = array_filter($vaccinationData, function($data) use ($key) {
        return !isset($data['given_vaccines'][$key]);
    });
    $childrenMissingVaccines[$key] = $missing;
}

// Health records statistics
$childrenWithRecords = 0;
$childrenWithoutRecords = 0;
foreach ($allChildren as $child) {
    $records = $healthRecordModel->readByChild($child['child_id']);
    if (!empty($records)) {
        $childrenWithRecords++;
    } else {
        $childrenWithoutRecords++;
    }
}

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - CHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="no-print">
        <?php include 'includes/navbar.php'; ?>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if ($flashMessage): ?>
            <div class="no-print mb-6 p-4 rounded-lg <?php echo $flashMessage['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'; ?>">
                <?php echo htmlspecialchars($flashMessage['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Health Reports</h1>
                <p class="mt-2 text-gray-600">Vaccination coverage and health monitoring statistics</p>
            </div>
            <button onclick="window.print()" class="no-print px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Print Report
            </button>
        </div>

        <!-- Report Type Selector -->
        <div class="no-print mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex space-x-4">
                    <a href="?type=vaccination" 
                       class="px-4 py-2 rounded-lg <?php echo $reportType === 'vaccination' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Vaccination Report
                    </a>
                    <a href="?type=health" 
                       class="px-4 py-2 rounded-lg <?php echo $reportType === 'health' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Health Records Report
                    </a>
                </div>
            </div>
        </div>

        <?php if ($reportType === 'vaccination'): ?>
            <!-- Vaccination Report -->
            
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Total Children</div>
                    <div class="text-2xl font-bold text-gray-900 mt-2"><?php echo count($allChildren); ?></div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Total Vaccines Given</div>
                    <div class="text-2xl font-bold text-green-600 mt-2"><?php echo $totalVaccinesGiven; ?></div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Avg per Child</div>
                    <div class="text-2xl font-bold text-blue-600 mt-2"><?php echo $averageVaccinesPerChild; ?></div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Vaccine Types</div>
                    <div class="text-2xl font-bold text-purple-600 mt-2"><?php echo count($vaccineTypes); ?></div>
                </div>
            </div>

            <!-- Vaccination Coverage Chart -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Vaccination Coverage by Type</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <canvas id="vaccinationChart"></canvas>
                    </div>
                    <div class="space-y-3">
                        <?php foreach ($vaccineTypes as $key => $vaccine): 
                            $given = count($allChildren) - count($childrenMissingVaccines[$key]);
                            $percentage = !empty($allChildren) ? round(($given / count($allChildren)) * 100) : 0;
                        ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700"><?php echo $vaccine; ?></span>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-600"><?php echo $given; ?>/<?php echo count($allChildren); ?></span>
                                    <span class="text-sm font-semibold text-blue-600"><?php echo $percentage; ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Children Who Received Vaccines -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">✅ Children with Vaccinations</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Child Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vaccines Given</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Vaccine</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($vaccinationData as $data): ?>
                                <?php if (!empty($data['immunizations'])): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($data['child']['name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo calculateAgeString($data['child']['dob']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <div class="flex flex-wrap gap-1">
                                                <?php foreach ($data['given_vaccines'] as $vaccine => $status): ?>
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full"><?php echo $vaccine; ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php 
                                            $lastVaccine = end($data['immunizations']);
                                            echo $lastVaccine ? formatDate($lastVaccine['date_given']) : 'N/A';
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm no-print">
                                            <a href="child_profile.php?id=<?php echo $data['child']['child_id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Children Missing Vaccines -->
            <?php foreach ($vaccineTypes as $key => $vaccine): ?>
                <?php if (!empty($childrenMissingVaccines[$key])): ?>
                    <div class="bg-white rounded-lg shadow mb-8">
                        <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                            <h2 class="text-xl font-semibold text-red-900">
                                ❌ Children Missing <?php echo $vaccine; ?> Vaccine
                                <span class="text-sm font-normal text-red-700">(<?php echo count($childrenMissingVaccines[$key]); ?> children)</span>
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Child Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mother</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase no-print">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($childrenMissingVaccines[$key] as $data): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($data['child']['name']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                <?php echo calculateAgeString($data['child']['dob']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                <?php echo ucfirst($data['child']['gender']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                <?php echo htmlspecialchars($data['child']['mother_name']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                <?php echo htmlspecialchars($data['child']['mother_email']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm no-print">
                                                <a href="child_profile.php?id=<?php echo $data['child']['child_id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        <?php else: ?>
            <!-- Health Records Report -->
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Total Children</div>
                    <div class="text-2xl font-bold text-gray-900 mt-2"><?php echo count($allChildren); ?></div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">With Health Records</div>
                    <div class="text-2xl font-bold text-green-600 mt-2"><?php echo $childrenWithRecords; ?></div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Without Records</div>
                    <div class="text-2xl font-bold text-red-600 mt-2"><?php echo $childrenWithoutRecords; ?></div>
                </div>
            </div>

            <!-- Children with Health Records -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">✅ Children with Health Records</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Child Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Records</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Checkup</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($allChildren as $child): 
                                $records = $healthRecordModel->readByChild($child['child_id']);
                                if (!empty($records)):
                                    $lastRecord = $records[0];
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($child['name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo calculateAgeString($child['dob']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo count($records); ?> record<?php echo count($records) != 1 ? 's' : ''; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo formatDate($lastRecord['record_date']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm no-print">
                                        <a href="child_profile.php?id=<?php echo $child['child_id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            <?php endif; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Children without Health Records -->
            <?php 
            $childrenNoRecords = array_filter($allChildren, function($child) use ($healthRecordModel) {
                $records = $healthRecordModel->readByChild($child['child_id']);
                return empty($records);
            });
            ?>
            <?php if (!empty($childrenNoRecords)): ?>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                        <h2 class="text-xl font-semibold text-red-900">
                            ❌ Children Without Health Records
                            <span class="text-sm font-normal text-red-700">(<?php echo count($childrenNoRecords); ?> children)</span>
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Child Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mother</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase no-print">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($childrenNoRecords as $child): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($child['name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo calculateAgeString($child['dob']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo ucfirst($child['gender']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo htmlspecialchars($child['mother_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo htmlspecialchars($child['mother_email']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm no-print">
                                            <a href="add_health_record.php?child_id=<?php echo $child['child_id']; ?>" 
                                               class="text-green-600 hover:text-green-900">Add Record</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="assets/js/charts.js"></script>
    <script>
        <?php if ($reportType === 'vaccination'): ?>
            // Vaccination coverage chart
            const vaccineLabels = <?php echo json_encode(array_values($vaccineTypes)); ?>;
            const vaccineData = [
                <?php foreach ($vaccineTypes as $key => $vaccine): 
                    $given = count($allChildren) - count($childrenMissingVaccines[$key]);
                    echo $given . ',';
                endforeach; ?>
            ];
            const totalChildren = <?php echo count($allChildren); ?>;

            const ctx = document.getElementById('vaccinationChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: vaccineLabels,
                        datasets: [{
                            label: 'Children Vaccinated',
                            data: vaccineData,
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: totalChildren,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        <?php endif; ?>
    </script>
</body>
</html>

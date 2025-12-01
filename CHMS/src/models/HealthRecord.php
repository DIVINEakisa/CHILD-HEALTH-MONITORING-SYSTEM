<?php
/**
 * Health Record Model
 * Handles health record database operations
 */

require_once __DIR__ . '/../config/database.php';

class HealthRecord {
    private $conn;
    private $table = 'health_records';

    public $record_id;
    public $child_id;
    public $weight;
    public $height;
    public $nutrition_status;
    public $vaccinations;
    public $doctor_notes;
    public $record_date;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create new health record
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (child_id, weight, height, nutrition_status, vaccinations, doctor_notes, record_date) 
                  VALUES (:child_id, :weight, :height, :nutrition_status, :vaccinations, :doctor_notes, :record_date)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':child_id', $this->child_id);
        $stmt->bindParam(':weight', $this->weight);
        $stmt->bindParam(':height', $this->height);
        $stmt->bindParam(':nutrition_status', $this->nutrition_status);
        $stmt->bindParam(':vaccinations', $this->vaccinations);
        $stmt->bindParam(':doctor_notes', $this->doctor_notes);
        $stmt->bindParam(':record_date', $this->record_date);

        if ($stmt->execute()) {
            $this->record_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read health record by ID
     * @return array|false
     */
    public function readOne() {
        $query = "SELECT hr.*, c.name as child_name, c.dob, c.gender,
                         u.name as mother_name
                  FROM " . $this->table . " hr
                  LEFT JOIN children c ON hr.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE hr.record_id = :record_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':record_id', $this->record_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Read all health records for a child
     * @param int $childId
     * @return array
     */
    public function readByChild($childId) {
        $query = "SELECT hr.*, c.name as child_name, c.dob, c.gender
                  FROM " . $this->table . " hr
                  LEFT JOIN children c ON hr.child_id = c.child_id
                  WHERE hr.child_id = :child_id
                  ORDER BY hr.record_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Read all health records
     * @param int|null $limit
     * @return array
     */
    public function readAll($limit = null) {
        $query = "SELECT hr.*, c.name as child_name, c.dob, c.gender,
                         u.name as mother_name
                  FROM " . $this->table . " hr
                  LEFT JOIN children c ON hr.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  ORDER BY hr.record_date DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update health record
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET weight = :weight, 
                      height = :height, 
                      nutrition_status = :nutrition_status,
                      vaccinations = :vaccinations,
                      doctor_notes = :doctor_notes,
                      record_date = :record_date
                  WHERE record_id = :record_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':weight', $this->weight);
        $stmt->bindParam(':height', $this->height);
        $stmt->bindParam(':nutrition_status', $this->nutrition_status);
        $stmt->bindParam(':vaccinations', $this->vaccinations);
        $stmt->bindParam(':doctor_notes', $this->doctor_notes);
        $stmt->bindParam(':record_date', $this->record_date);
        $stmt->bindParam(':record_id', $this->record_id);

        return $stmt->execute();
    }

    /**
     * Delete health record
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE record_id = :record_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':record_id', $this->record_id);
        return $stmt->execute();
    }

    /**
     * Get latest health record for a child
     * @param int $childId
     * @return array|false
     */
    public function getLatestRecord($childId) {
        $query = "SELECT hr.*, c.name as child_name, c.dob, c.gender
                  FROM " . $this->table . " hr
                  LEFT JOIN children c ON hr.child_id = c.child_id
                  WHERE hr.child_id = :child_id
                  ORDER BY hr.record_date DESC
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get growth trend data for charts
     * @param int $childId
     * @return array
     */
    public function getGrowthTrend($childId) {
        $query = "SELECT record_date, weight, height
                  FROM " . $this->table . "
                  WHERE child_id = :child_id
                  ORDER BY record_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get records by date range
     * @param int $childId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getRecordsByDateRange($childId, $startDate, $endDate) {
        $query = "SELECT hr.*, c.name as child_name
                  FROM " . $this->table . " hr
                  LEFT JOIN children c ON hr.child_id = c.child_id
                  WHERE hr.child_id = :child_id
                    AND hr.record_date BETWEEN :start_date AND :end_date
                  ORDER BY hr.record_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get health statistics
     * @return array
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_records,
                    COUNT(DISTINCT child_id) as children_monitored,
                    AVG(weight) as avg_weight,
                    AVG(height) as avg_height
                  FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if record exists for date
     * @param int $childId
     * @param string $date
     * @param int|null $excludeRecordId
     * @return bool
     */
    public function recordExistsForDate($childId, $date, $excludeRecordId = null) {
        $query = "SELECT record_id FROM " . $this->table . " 
                  WHERE child_id = :child_id AND record_date = :record_date";
        
        if ($excludeRecordId) {
            $query .= " AND record_id != :record_id";
        }
        
        $query .= " LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->bindParam(':record_date', $date);
        
        if ($excludeRecordId) {
            $stmt->bindParam(':record_id', $excludeRecordId);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>

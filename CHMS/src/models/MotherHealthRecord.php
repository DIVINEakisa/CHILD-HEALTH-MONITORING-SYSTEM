<?php
/**
 * Mother Health Record Model
 * Handles mother health record database operations
 */

require_once __DIR__ . '/../config/database.php';

class MotherHealthRecord {
    private $conn;
    private $table = 'mother_health_records';

    public $record_id;
    public $mother_id;
    public $record_type;
    public $record_date;
    public $weight;
    public $blood_pressure;
    public $hemoglobin;
    public $blood_sugar;
    public $pregnancy_week;
    public $delivery_date;
    public $delivery_type;
    public $complications;
    public $medications;
    public $doctor_notes;
    public $next_checkup_date;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create new mother health record
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (mother_id, record_type, record_date, weight, blood_pressure, 
                   hemoglobin, blood_sugar, pregnancy_week, delivery_date, 
                   delivery_type, complications, medications, doctor_notes, 
                   next_checkup_date) 
                  VALUES (:mother_id, :record_type, :record_date, :weight, 
                          :blood_pressure, :hemoglobin, :blood_sugar, :pregnancy_week, 
                          :delivery_date, :delivery_type, :complications, :medications, 
                          :doctor_notes, :next_checkup_date)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':mother_id', $this->mother_id);
        $stmt->bindParam(':record_type', $this->record_type);
        $stmt->bindParam(':record_date', $this->record_date);
        $stmt->bindParam(':weight', $this->weight);
        $stmt->bindParam(':blood_pressure', $this->blood_pressure);
        $stmt->bindParam(':hemoglobin', $this->hemoglobin);
        $stmt->bindParam(':blood_sugar', $this->blood_sugar);
        $stmt->bindParam(':pregnancy_week', $this->pregnancy_week);
        $stmt->bindParam(':delivery_date', $this->delivery_date);
        $stmt->bindParam(':delivery_type', $this->delivery_type);
        $stmt->bindParam(':complications', $this->complications);
        $stmt->bindParam(':medications', $this->medications);
        $stmt->bindParam(':doctor_notes', $this->doctor_notes);
        $stmt->bindParam(':next_checkup_date', $this->next_checkup_date);

        if ($stmt->execute()) {
            $this->record_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read one health record by ID
     * @return array|false
     */
    public function readOne() {
        $query = "SELECT mhr.*, u.name AS mother_name, u.email, u.phone 
                  FROM " . $this->table . " mhr
                  JOIN users u ON mhr.mother_id = u.user_id
                  WHERE mhr.record_id = :record_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':record_id', $this->record_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Read all health records for a specific mother
     * @param int $motherId
     * @return array
     */
    public function readByMother($motherId) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE mother_id = :mother_id 
                  ORDER BY record_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mother_id', $motherId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Read all health records with mother information
     * @param string|null $recordType Filter by record type
     * @param int|null $motherId Filter by mother ID
     * @return array
     */
    public function readAll($recordType = null, $motherId = null) {
        $query = "SELECT mhr.*, u.name AS mother_name, u.email, u.phone 
                  FROM " . $this->table . " mhr
                  JOIN users u ON mhr.mother_id = u.user_id
                  WHERE 1=1";

        if ($recordType) {
            $query .= " AND mhr.record_type = :record_type";
        }

        if ($motherId) {
            $query .= " AND mhr.mother_id = :mother_id";
        }

        $query .= " ORDER BY mhr.record_date DESC";

        $stmt = $this->conn->prepare($query);

        if ($recordType) {
            $stmt->bindParam(':record_type', $recordType);
        }

        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
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
                  SET record_type = :record_type,
                      record_date = :record_date,
                      weight = :weight,
                      blood_pressure = :blood_pressure,
                      hemoglobin = :hemoglobin,
                      blood_sugar = :blood_sugar,
                      pregnancy_week = :pregnancy_week,
                      delivery_date = :delivery_date,
                      delivery_type = :delivery_type,
                      complications = :complications,
                      medications = :medications,
                      doctor_notes = :doctor_notes,
                      next_checkup_date = :next_checkup_date
                  WHERE record_id = :record_id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':record_type', $this->record_type);
        $stmt->bindParam(':record_date', $this->record_date);
        $stmt->bindParam(':weight', $this->weight);
        $stmt->bindParam(':blood_pressure', $this->blood_pressure);
        $stmt->bindParam(':hemoglobin', $this->hemoglobin);
        $stmt->bindParam(':blood_sugar', $this->blood_sugar);
        $stmt->bindParam(':pregnancy_week', $this->pregnancy_week);
        $stmt->bindParam(':delivery_date', $this->delivery_date);
        $stmt->bindParam(':delivery_type', $this->delivery_type);
        $stmt->bindParam(':complications', $this->complications);
        $stmt->bindParam(':medications', $this->medications);
        $stmt->bindParam(':doctor_notes', $this->doctor_notes);
        $stmt->bindParam(':next_checkup_date', $this->next_checkup_date);
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
     * Get latest health record for a mother
     * @param int $motherId
     * @return array|false
     */
    public function getLatestRecord($motherId) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE mother_id = :mother_id 
                  ORDER BY record_date DESC 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mother_id', $motherId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get health trend data for charts
     * @param int $motherId
     * @param int $limit Number of recent records
     * @return array
     */
    public function getHealthTrend($motherId, $limit = 12) {
        $query = "SELECT record_date, weight, blood_pressure, hemoglobin, blood_sugar, record_type
                  FROM " . $this->table . " 
                  WHERE mother_id = :mother_id 
                  ORDER BY record_date ASC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mother_id', $motherId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get mothers with upcoming checkups
     * @param int $daysAhead Number of days to look ahead
     * @return array
     */
    public function getUpcomingCheckups($daysAhead = 7) {
        $query = "SELECT mhr.*, u.name AS mother_name, u.email, u.phone 
                  FROM " . $this->table . " mhr
                  JOIN users u ON mhr.mother_id = u.user_id
                  WHERE mhr.next_checkup_date IS NOT NULL 
                    AND mhr.next_checkup_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                  ORDER BY mhr.next_checkup_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $daysAhead, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics for mother health records
     * @return array
     */
    public function getStatistics() {
        $query = "SELECT 
                    record_type,
                    COUNT(*) as count,
                    COUNT(DISTINCT mother_id) as unique_mothers
                  FROM " . $this->table . "
                  GROUP BY record_type";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $stats = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[$row['record_type']] = [
                'count' => $row['count'],
                'unique_mothers' => $row['unique_mothers']
            ];
        }

        return $stats;
    }
}
?>

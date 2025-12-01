<?php
/**
 * Immunization Model
 * Handles immunization database operations
 */

require_once __DIR__ . '/../config/database.php';

class Immunization {
    private $conn;
    private $table = 'immunizations';

    public $immunization_id;
    public $child_id;
    public $vaccine_name;
    public $date_given;
    public $next_due_date;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create new immunization record
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (child_id, vaccine_name, date_given, next_due_date) 
                  VALUES (:child_id, :vaccine_name, :date_given, :next_due_date)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':child_id', $this->child_id);
        $stmt->bindParam(':vaccine_name', $this->vaccine_name);
        $stmt->bindParam(':date_given', $this->date_given);
        $stmt->bindParam(':next_due_date', $this->next_due_date);

        if ($stmt->execute()) {
            $this->immunization_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read immunization by ID
     * @return array|false
     */
    public function readOne() {
        $query = "SELECT i.*, c.name as child_name, c.dob,
                         u.name as mother_name
                  FROM " . $this->table . " i
                  LEFT JOIN children c ON i.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE i.immunization_id = :immunization_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':immunization_id', $this->immunization_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Read all immunizations for a child
     * @param int $childId
     * @return array
     */
    public function readByChild($childId) {
        $query = "SELECT i.*, c.name as child_name
                  FROM " . $this->table . " i
                  LEFT JOIN children c ON i.child_id = c.child_id
                  WHERE i.child_id = :child_id
                  ORDER BY i.date_given DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Read all immunizations
     * @return array
     */
    public function readAll() {
        $query = "SELECT i.*, c.name as child_name, c.dob,
                         u.name as mother_name
                  FROM " . $this->table . " i
                  LEFT JOIN children c ON i.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  ORDER BY i.date_given DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update immunization
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET vaccine_name = :vaccine_name, 
                      date_given = :date_given, 
                      next_due_date = :next_due_date
                  WHERE immunization_id = :immunization_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':vaccine_name', $this->vaccine_name);
        $stmt->bindParam(':date_given', $this->date_given);
        $stmt->bindParam(':next_due_date', $this->next_due_date);
        $stmt->bindParam(':immunization_id', $this->immunization_id);

        return $stmt->execute();
    }

    /**
     * Delete immunization
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE immunization_id = :immunization_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':immunization_id', $this->immunization_id);
        return $stmt->execute();
    }

    /**
     * Get upcoming immunizations
     * @param int|null $motherId
     * @param int $daysAhead
     * @return array
     */
    public function getUpcoming($motherId = null, $daysAhead = 30) {
        $query = "SELECT i.*, c.name as child_name, c.dob,
                         u.name as mother_name, u.email as mother_email,
                         DATEDIFF(i.next_due_date, CURDATE()) as days_until_due
                  FROM " . $this->table . " i
                  LEFT JOIN children c ON i.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE i.next_due_date IS NOT NULL
                    AND i.next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)";
        
        if ($motherId) {
            $query .= " AND c.mother_id = :mother_id";
        }
        
        $query .= " ORDER BY i.next_due_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $daysAhead, PDO::PARAM_INT);
        
        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get overdue immunizations
     * @param int|null $motherId
     * @return array
     */
    public function getOverdue($motherId = null) {
        $query = "SELECT i.*, c.name as child_name, c.dob,
                         u.name as mother_name, u.email as mother_email,
                         DATEDIFF(CURDATE(), i.next_due_date) as days_overdue
                  FROM " . $this->table . " i
                  LEFT JOIN children c ON i.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE i.next_due_date IS NOT NULL
                    AND i.next_due_date < CURDATE()";
        
        if ($motherId) {
            $query .= " AND c.mother_id = :mother_id";
        }
        
        $query .= " ORDER BY i.next_due_date ASC";

        $stmt = $this->conn->prepare($query);
        
        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get immunization history for a child
     * @param int $childId
     * @return array
     */
    public function getHistory($childId) {
        $query = "SELECT *
                  FROM " . $this->table . "
                  WHERE child_id = :child_id
                  ORDER BY date_given DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get immunization statistics
     * @return array
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_immunizations,
                    COUNT(DISTINCT child_id) as children_immunized,
                    COUNT(CASE WHEN next_due_date < CURDATE() THEN 1 END) as overdue_count,
                    COUNT(CASE WHEN next_due_date >= CURDATE() THEN 1 END) as upcoming_count
                  FROM " . $this->table . "
                  WHERE next_due_date IS NOT NULL";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if vaccine already given
     * @param int $childId
     * @param string $vaccineName
     * @return bool
     */
    public function vaccineExists($childId, $vaccineName) {
        $query = "SELECT immunization_id FROM " . $this->table . " 
                  WHERE child_id = :child_id AND vaccine_name = :vaccine_name LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->bindParam(':vaccine_name', $vaccineName);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>

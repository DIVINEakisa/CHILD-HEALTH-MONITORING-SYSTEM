<?php
/**
 * Child Model
 * Handles child-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class Child {
    private $conn;
    private $table = 'children';

    public $child_id;
    public $mother_id;
    public $name;
    public $dob;
    public $gender;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create new child
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (mother_id, name, dob, gender) 
                  VALUES (:mother_id, :name, :dob, :gender)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':mother_id', $this->mother_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':dob', $this->dob);
        $stmt->bindParam(':gender', $this->gender);

        if ($stmt->execute()) {
            $this->child_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read child by ID
     * @return array|false
     */
    public function readOne() {
        $query = "SELECT c.*, u.name as mother_name, u.email as mother_email, u.phone as mother_phone
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE c.child_id = :child_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $this->child_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Read all children
     * @param int|null $motherId Filter by mother ID
     * @return PDOStatement
     */
    public function readAll($motherId = null) {
        $query = "SELECT c.*, u.name as mother_name, u.email as mother_email,
                         TIMESTAMPDIFF(MONTH, c.dob, CURDATE()) as age_months
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.mother_id = u.user_id";
        
        if ($motherId) {
            $query .= " WHERE c.mother_id = :mother_id";
        }
        
        $query .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
        }
        
        $stmt->execute();
        return $stmt;
    }

    /**
     * Update child
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, 
                      dob = :dob, 
                      gender = :gender 
                  WHERE child_id = :child_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':dob', $this->dob);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':child_id', $this->child_id);

        return $stmt->execute();
    }

    /**
     * Delete child
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE child_id = :child_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $this->child_id);
        return $stmt->execute();
    }

    /**
     * Get children with latest health records
     * @param int|null $motherId
     * @return array
     */
    public function getChildrenWithLatestRecords($motherId = null) {
        $query = "SELECT c.*, 
                         u.name as mother_name,
                         TIMESTAMPDIFF(MONTH, c.dob, CURDATE()) as age_months,
                         hr.weight, hr.height, hr.nutrition_status, hr.record_date
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  LEFT JOIN (
                      SELECT child_id, weight, height, nutrition_status, record_date
                      FROM health_records hr1
                      WHERE record_date = (
                          SELECT MAX(record_date) 
                          FROM health_records hr2 
                          WHERE hr2.child_id = hr1.child_id
                      )
                  ) hr ON c.child_id = hr.child_id";
        
        if ($motherId) {
            $query .= " WHERE c.mother_id = :mother_id";
        }
        
        $query .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get child statistics
     * @param int|null $motherId
     * @return array
     */
    public function getStatistics($motherId = null) {
        $query = "SELECT 
                    COUNT(*) as total_children,
                    SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) as male_count,
                    SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) as female_count,
                    AVG(TIMESTAMPDIFF(MONTH, dob, CURDATE())) as avg_age_months
                  FROM " . $this->table;
        
        if ($motherId) {
            $query .= " WHERE mother_id = :mother_id";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Search children by name
     * @param string $searchTerm
     * @param int|null $motherId
     * @return array
     */
    public function search($searchTerm, $motherId = null) {
        $query = "SELECT c.*, u.name as mother_name,
                         TIMESTAMPDIFF(MONTH, c.dob, CURDATE()) as age_months
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE c.name LIKE :search_term";
        
        if ($motherId) {
            $query .= " AND c.mother_id = :mother_id";
        }
        
        $query .= " ORDER BY c.name";

        $stmt = $this->conn->prepare($query);
        $searchParam = "%$searchTerm%";
        $stmt->bindParam(':search_term', $searchParam);
        
        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if mother owns this child
     * @param int $childId
     * @param int $motherId
     * @return bool
     */
    public function isOwnedByMother($childId, $motherId) {
        $query = "SELECT child_id FROM " . $this->table . " 
                  WHERE child_id = :child_id AND mother_id = :mother_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':child_id', $childId);
        $stmt->bindParam(':mother_id', $motherId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>

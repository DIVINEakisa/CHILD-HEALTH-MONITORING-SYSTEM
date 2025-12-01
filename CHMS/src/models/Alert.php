<?php
/**
 * Alert Model
 * Handles alert database operations
 */

require_once __DIR__ . '/../config/database.php';

class Alert {
    private $conn;
    private $table = 'alerts';

    public $alert_id;
    public $child_id;
    public $alert_type;
    public $message;
    public $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create new alert
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (child_id, alert_type, message, status) 
                  VALUES (:child_id, :alert_type, :message, :status)";

        $stmt = $this->conn->prepare($query);

        $this->status = $this->status ?? 'pending';

        $stmt->bindParam(':child_id', $this->child_id);
        $stmt->bindParam(':alert_type', $this->alert_type);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->alert_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read alert by ID
     * @return array|false
     */
    public function readOne() {
        $query = "SELECT a.*, c.name as child_name, c.dob,
                         u.name as mother_name, u.email as mother_email
                  FROM " . $this->table . " a
                  LEFT JOIN children c ON a.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE a.alert_id = :alert_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':alert_id', $this->alert_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Read all alerts
     * @param string|null $status Filter by status
     * @param int|null $childId Filter by child
     * @param int|null $motherId Filter by mother
     * @return array
     */
    public function readAll($status = null, $childId = null, $motherId = null) {
        $query = "SELECT a.*, c.name as child_name, c.dob,
                         u.name as mother_name, u.email as mother_email
                  FROM " . $this->table . " a
                  LEFT JOIN children c ON a.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE 1=1";
        
        if ($status) {
            $query .= " AND a.status = :status";
        }
        if ($childId) {
            $query .= " AND a.child_id = :child_id";
        }
        if ($motherId) {
            $query .= " AND c.mother_id = :mother_id";
        }
        
        $query .= " ORDER BY a.created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        if ($childId) {
            $stmt->bindParam(':child_id', $childId);
        }
        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update alert status
     * @return bool
     */
    public function updateStatus() {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status 
                  WHERE alert_id = :alert_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':alert_id', $this->alert_id);

        return $stmt->execute();
    }

    /**
     * Delete alert
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE alert_id = :alert_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':alert_id', $this->alert_id);
        return $stmt->execute();
    }

    /**
     * Get pending alerts count
     * @param int|null $motherId
     * @return int
     */
    public function getPendingCount($motherId = null) {
        $query = "SELECT COUNT(*) as count
                  FROM " . $this->table . " a";
        
        if ($motherId) {
            $query .= " LEFT JOIN children c ON a.child_id = c.child_id
                       WHERE a.status = 'pending' AND c.mother_id = :mother_id";
        } else {
            $query .= " WHERE a.status = 'pending'";
        }

        $stmt = $this->conn->prepare($query);
        
        if ($motherId) {
            $stmt->bindParam(':mother_id', $motherId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Resolve alert
     * @return bool
     */
    public function resolve() {
        $this->status = 'resolved';
        return $this->updateStatus();
    }

    /**
     * Get alerts by type
     * @param string $alertType
     * @param string $status
     * @return array
     */
    public function getByType($alertType, $status = 'pending') {
        $query = "SELECT a.*, c.name as child_name, c.dob,
                         u.name as mother_name
                  FROM " . $this->table . " a
                  LEFT JOIN children c ON a.child_id = c.child_id
                  LEFT JOIN users u ON c.mother_id = u.user_id
                  WHERE a.alert_type = :alert_type 
                    AND a.status = :status
                  ORDER BY a.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':alert_type', $alertType);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get alert statistics
     * @return array
     */
    public function getStatistics() {
        $query = "SELECT 
                    status,
                    COUNT(*) as count
                  FROM " . $this->table . "
                  GROUP BY status";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $stats = ['pending' => 0, 'resolved' => 0];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[$row['status']] = (int)$row['count'];
        }
        
        return $stats;
    }

    /**
     * Delete old resolved alerts
     * @param int $daysOld
     * @return int Number of deleted alerts
     */
    public function deleteOldResolved($daysOld = 30) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE status = 'resolved' 
                    AND created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $daysOld, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
?>

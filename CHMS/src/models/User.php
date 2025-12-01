<?php
/**
 * User Model
 * Handles user-related database operations
 */

require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = 'users';

    public $user_id;
    public $name;
    public $email;
    public $phone;
    public $role;
    public $password;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create new user
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, phone, role, password) 
                  VALUES (:name, :email, :phone, :role, :password)";

        $stmt = $this->conn->prepare($query);

        // Hash password
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Read user by ID
     * @return array|false
     */
    public function readOne() {
        $query = "SELECT user_id, name, email, phone, role, created_at 
                  FROM " . $this->table . " 
                  WHERE user_id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Read all users
     * @param string|null $role Filter by role
     * @return PDOStatement
     */
    public function readAll($role = null) {
        $query = "SELECT user_id, name, email, phone, role, created_at 
                  FROM " . $this->table;
        
        if ($role) {
            $query .= " WHERE role = :role";
        }
        
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        
        $stmt->execute();
        return $stmt;
    }

    /**
     * Update user
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, 
                      email = :email, 
                      phone = :phone 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    /**
     * Update password
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword($newPassword) {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    /**
     * Delete user
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        return $stmt->execute();
    }

    /**
     * Login - verify credentials
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function login($email, $password) {
        $query = "SELECT user_id, name, email, phone, role, password 
                  FROM " . $this->table . " 
                  WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Remove password from result
            return $user;
        }

        return false;
    }

    /**
     * Check if email exists
     * @param string $email
     * @param int|null $excludeUserId Exclude this user ID from check
     * @return bool
     */
    public function emailExists($email, $excludeUserId = null) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE email = :email";
        
        if ($excludeUserId) {
            $query .= " AND user_id != :user_id";
        }
        
        $query .= " LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($excludeUserId) {
            $stmt->bindParam(':user_id', $excludeUserId);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Get user statistics
     * @return array
     */
    public function getStatistics() {
        $query = "SELECT 
                    role,
                    COUNT(*) as count
                  FROM " . $this->table . "
                  GROUP BY role";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $stats = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[$row['role']] = $row['count'];
        }
        
        return $stats;
    }
}
?>

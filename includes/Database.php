<?php
/**
 * Database class for Song Lyrics Platform
 * Handles MySQLi database connections and operations
 */

class Database {
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    
    public function __construct() {
        $this->host = DB_HOST;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->database = DB_NAME;
        
        $this->connect();
    }
    
    /**
     * Establish database connection
     */
    private function connect() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
        
        // Set charset
        $this->connection->set_charset(DB_CHARSET);
    }
    
    /**
     * Get the MySQLi connection object
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Execute a prepared statement with parameters
     */
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        return $stmt;
    }
    
    /**
     * Fetch all results from a query
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }
    
    /**
     * Fetch single result from a query
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data;
    }
    
    /**
     * Execute an insert/update/delete query and return affected rows
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        
        return $affected_rows;
    }
    
    /**
     * Get the last inserted ID
     */
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }
    
    /**
     * Escape string for safe SQL usage
     */
    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        $this->connection->autocommit(false);
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        $this->connection->commit();
        $this->connection->autocommit(true);
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        $this->connection->rollback();
        $this->connection->autocommit(true);
    }
    
    /**
     * Close database connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Destructor - close connection
     */
    public function __destruct() {
        $this->close();
    }
}
?>

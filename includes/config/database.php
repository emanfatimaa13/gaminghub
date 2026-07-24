<?php
// Database configuration and connection

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'gaming_store');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create connection
function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to UTF-8
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (Exception $e) {
        die("Database Connection Error: " . $e->getMessage());
    }
}

// Execute query with prepared statement
function executeQuery($sql, $params = [], $types = "") {
    $conn = getConnection();
    
    try {
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Bind parameters if any
        if (!empty($params)) {
            if (empty($types)) {
                // Auto-detect types
                $types = "";
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= "i";
                    } elseif (is_float($param) || is_double($param)) {
                        $types .= "d";
                    } else {
                        $types .= "s";
                    }
                }
            }
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        // For SELECT queries, return the result set
        if ($result) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
            $conn->close();
            return $data;
        }
        
        // For INSERT/UPDATE/DELETE, return affected rows or last insert ID
        if ($stmt->affected_rows > 0) {
            $lastId = $stmt->insert_id;
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            $conn->close();
            return ['affected_rows' => $affectedRows, 'insert_id' => $lastId];
        }
        
        $stmt->close();
        $conn->close();
        return true;
        
    } catch (Exception $e) {
        $conn->close();
        throw $e;
    }
}

// Get single record
function getSingleRecord($sql, $params = [], $types = "") {
    $result = executeQuery($sql, $params, $types);
    return !empty($result) ? $result[0] : null;
}

// Get count
function getCount($sql, $params = [], $types = "") {
    $result = executeQuery($sql, $params, $types);
    return !empty($result) ? (int)$result[0]['count'] : 0;
}

// Escape string for safe SQL
function escapeString($string) {
    $conn = getConnection();
    $escaped = $conn->real_escape_string($string);
    $conn->close();
    return $escaped;
}
?>
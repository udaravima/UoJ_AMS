<?php
/**
 * NFC Class
 * Handles NFC card registration and student lookup
 */
class NFC
{
    private $conn;
    private $table = 'uoj_student_nfc';
    private $stdTable = 'uoj_student';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Register a card to a student
     * Overwrites if student already has a card, or if card is assigned to someone else?
     * Ideally, one card -> one student. One student -> one card (or multiple).
     * Let's enforce unique NFC UID.
     */
    public function registerCard($stdId, $nfcUid)
    {
        // Check if card is already registered
        if ($this->getStudentByNFC($nfcUid)) {
            return ['success' => false, 'message' => 'Card is already registered to a student.'];
        }

        // Check if student exists
        $checkUser = "SELECT std_id FROM {$this->stdTable} WHERE std_id = ?";
        $stmt = $this->conn->prepare($checkUser);
        $stmt->bind_param('i', $stdId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows == 0) {
            return ['success' => false, 'message' => 'Student not found.'];
        }

        // Register card
        // Optional: Remove old cards for this student if we strictly want 1 card per student
        // $this->revokeCardByStudent($stdId);

        $query = "INSERT INTO {$this->table} (std_id, nfc_uid) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('is', $stdId, $nfcUid);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Card registered successfully.'];
        } else {
            return ['success' => false, 'message' => 'Database error.'];
        }
    }

    /**
     * Get student ID by NFC UID
     */
    public function getStudentByNFC($nfcUid)
    {
        $query = "SELECT s.*, n.nfc_uid 
                  FROM {$this->table} n
                  INNER JOIN {$this->stdTable} s ON n.std_id = s.std_id
                  WHERE n.nfc_uid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $nfcUid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Revoke a card
     */
    public function revokeCard($nfcUid)
    {
        $query = "DELETE FROM {$this->table} WHERE nfc_uid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $nfcUid);
        return $stmt->execute();
    }

    /**
     * Get all registered cards
     */
    public function getAllCards()
    {
        $query = "SELECT s.std_fullname, s.std_index, n.nfc_uid, n.assigned_at
                  FROM {$this->table} n
                  INNER JOIN {$this->stdTable} s ON n.std_id = s.std_id
                  ORDER BY n.assigned_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }
}

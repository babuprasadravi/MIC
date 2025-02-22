<?php
// db is in the same directory
require_once 'config.php';

class AttendanceSessionPopulator
{
    private $conn;
    private $logFile;

    public function __construct($conn)
    {
        if (!$conn) {
            throw new Exception("Database connection is required");
        }
        $this->conn = $conn;
        $this->logFile = __DIR__ . '/logs/attendance_populator.log';
    }

    private function logMessage($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        // file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    public function populate()
    {
        try {
            $this->conn->begin_transaction();

            $currentDate = date('Y-m-d');
            $dayOfWeek = date('l'); // Gets day name (Monday, Tuesday, etc.)

            // Get timetable entries that are valid for today based on class advisors
            $query = "
                SELECT DISTINCT 
                    t.timetable_id,
                    t.semester,
                    ca.start_date,
                    ca.end_date
                FROM timetable t
                JOIN class_advisors ca ON 
                    t.batch = ca.batch AND 
                    t.semester = ca.semester AND 
                    t.section = ca.section AND 
                    t.academic_year = ca.academic_year
                WHERE 
                    t.day = ? AND t.is_active = 1
                    AND ? BETWEEN ca.start_date AND ca.end_date 
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ss', $dayOfWeek, $currentDate);
            $stmt->execute();
            $result = $stmt->get_result();

            $insertCount = 0;

            // Prepare insert statement
            $insertQuery = "
                INSERT INTO attendance_session 
                (timetable_id, class_date, semester, attendance_status)
                VALUES (?, ?, ?, 0)
            ";
            $insertStmt = $this->conn->prepare($insertQuery);

            while ($row = $result->fetch_assoc()) {
                // Check if session already exists
                $checkQuery = "
                    SELECT 1 FROM attendance_session 
                    WHERE timetable_id = ? AND class_date = ?
                ";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->bind_param('is', $row['timetable_id'], $currentDate);
                $checkStmt->execute();

                if ($checkStmt->get_result()->num_rows === 0) {
                    $insertStmt->bind_param(
                        'isi',
                        $row['timetable_id'],
                        $currentDate,
                        $row['semester']
                    );
                    $insertStmt->execute();
                    $insertCount++;
                }
            }

            $this->conn->commit();
            $this->logMessage("Successfully inserted $insertCount attendance sessions for $currentDate");

            return [
                'status' => 'success',
                'message' => "Successfully inserted $insertCount attendance sessions",
                'date' => $currentDate
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->logMessage("Error: " . $e->getMessage());

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'date' => $currentDate
            ];
        }
    }

    public function updateOldSessions()
    {
        try {
            $this->conn->begin_transaction();

            $twoDaysAgo = date('Y-m-d', strtotime('-2 days'));
            $yesterday = date('Y-m-d', strtotime('-1 day'));

            // Update sessions that are still unmarked (status=0) from 2 or more days ago
            $query = "
                UPDATE attendance_session 
                SET attendance_status = 4
                WHERE class_date <= ?
                AND class_date < ?
                AND attendance_status = 0
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ss', $twoDaysAgo, $yesterday);
            $stmt->execute();

            $updatedCount = $stmt->affected_rows;

            $this->conn->commit();
            $this->logMessage("Updated $updatedCount old unmarked sessions to status 4");

            return [
                'status' => 'success',
                'message' => "Updated $updatedCount old unmarked sessions",
                'date' => date('Y-m-d')
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->logMessage("Error updating old sessions: " . $e->getMessage());

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'date' => date('Y-m-d')
            ];
        }
    }

    public function updateExpiredExamComponents()
    {
        try {
            $this->conn->begin_transaction();

            $currentDate = date('Y-m-d');

            // Update exam components where deadline has passed
            $query = "
                UPDATE exam_components 
                SET status = 'locked'
                WHERE deadline_date < ? and status != 'approved'
                AND status != 'locked'
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('s', $currentDate);
            $stmt->execute();

            $updatedCount = $stmt->affected_rows;

            $this->conn->commit();
            $this->logMessage("Locked $updatedCount expired exam components");

            return [
                'status' => 'success',
                'message' => "Locked $updatedCount expired exam components",
                'date' => $currentDate
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->logMessage("Error locking expired components: " . $e->getMessage());

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'date' => $currentDate
            ];
        }
    }
}

// Execute the script
$populator = new AttendanceSessionPopulator($conn);

// First populate today's sessions
$populateResult = $populator->populate();

// Then update old unmarked sessions
$updateResult = $populator->updateOldSessions();

// Then update expired exam components
$lockResult = $populator->updateExpiredExamComponents();

// Output combined results
echo json_encode([
    'populate' => $populateResult,
    'update' => $updateResult,
    'lock_expired' => $lockResult
], JSON_PRETTY_PRINT);

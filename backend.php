<?php
require_once 'config.php';


// Get department from session
if (!isset($_SESSION['user'])) {
    require_once 'session.php';
}

// Check if action is set
if (!isset($_POST['action']) && !isset($_GET['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'];

// Set the content type for the response
// header('Content-Type: application/json');


// Function to get advisor data
function getAdvisorData($erp_conn)
{
    $faculty_uid = $_SESSION['user']['uid'];
    $department = $_SESSION['user']['dept'];

    $query = "SELECT ca.*, CONCAT(f.name, '/', f.design) as advisor_name 
            FROM class_advisors ca 
            JOIN faculty f ON ca.faculty_id = f.uid 
            WHERE ca.faculty_id = ? 
            ORDER BY ca.advisor_id DESC, ca.academic_year DESC, ca.semester DESC";

    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("i", $faculty_uid);

    if (!$stmt->execute()) {
        return json_encode([
            'status' => 'error',
            'message' => 'Database query failed'
        ]);
    }

    $result = $stmt->get_result();
    $records = [];

    while ($row = $result->fetch_assoc()) {
        $row['department'] = $department;
        // Format dates for display
        $row['start_date'] = date('d-m-Y', strtotime($row['start_date']));
        $row['end_date'] = date('d-m-Y', strtotime($row['end_date']));
        $records[] = $row;
    }

    // Make sure to return only the JSON encoded data
    header('Content-Type: application/json');
    return json_encode([
        'status' => 'success',
        'records' => $records
    ]);
}

// Function to get departments
function getDepartments($erp_conn)
{
    $query = "SELECT DISTINCT dept FROM faculty WHERE dept IS NOT NULL AND dept != '' ORDER BY dept";
    $result = $erp_conn->query($query);

    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row['dept'];
    }

    return [
        'status' => 'success',
        'departments' => $departments
    ];
}

function getFacultyByDepartment($erp_conn)
{
    $department = $_POST['department'];

    $query = "SELECT uid, name, design FROM faculty WHERE dept = ? ORDER BY name";
    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("s", $department);

    if (!$stmt->execute()) {
        return [
            'status' => 'error',
            'message' => 'Database query failed'
        ];
    }

    $result = $stmt->get_result();
    $faculty = [];

    while ($row = $result->fetch_assoc()) {
        $faculty[] = [
            'id' => $row['uid'],
            'name' => $row['name'],
            'designation' => $row['design']
        ];
    }

    return [
        'status' => 'success',
        'faculty' => $faculty
    ];
}

// Function to get sections
function getSections($erp_conn)
{

    $batch = $_GET['batch'];
    $dept = $_SESSION['user']['dept'];

    try {
        $query = "SELECT DISTINCT section FROM student WHERE dept = ? AND ayear = ? AND section IS NOT NULL ORDER BY section";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ss", $dept, $batch);

        if (!$stmt->execute()) {
            return [
                'status' => 'error',
                'message' => 'Database query failed'
            ];
        }

        $result = $stmt->get_result();
        $sections = [];

        while ($row = $result->fetch_assoc()) {
            $sections[] = $row['section'];
        }

        return [
            'status' => 'success',
            'sections' => $sections
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database error'
        ];
    }
}
// Function to get unique batches in the hod page
function getUniqueBatches($erp_conn)
{
    $dept = $_SESSION['user']['dept'];
    $query = "SELECT DISTINCT ayear FROM student WHERE dept = ? ORDER BY ayear DESC";
    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("s", $dept);

    if (!$stmt->execute()) {
        return [
            'status' => 'error',
            'message' => 'Database query failed'
        ];
    }

    $result = $stmt->get_result();
    $batches = [];

    while ($row = $result->fetch_assoc()) {
        $batches[] = $row['ayear'];
    }

    return [
        'status' => 'success',
        'batches' => $batches
    ];
}

function getUniqueSections($erp_conn)
{
    if (!isset($_GET['batch']) || !isset($_SESSION['user']['dept'])) {
        return [
            'status' => 'error',
            'message' => 'Missing required parameters'
        ];
    }

    $batch = $_GET['batch'];
    $dept = $_SESSION['user']['dept'];

    try {
        $query = "SELECT DISTINCT section FROM student WHERE dept = ? AND ayear = ? AND section IS NOT NULL ORDER BY section";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ss", $dept, $batch);

        if (!$stmt->execute()) {
            return [
                'status' => 'error',
                'message' => 'Database query failed'
            ];
        }

        $result = $stmt->get_result();
        $sections = [];

        while ($row = $result->fetch_assoc()) {
            $sections[] = $row['section'];
        }

        return [
            'status' => 'success',
            'sections' => $sections
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database error'
        ];
    }
}

// Function to get faculty members
function getFacultyMembers($erp_conn)
{
    if (!isset($_SESSION['user']['dept'])) {
        return [
            'status' => 'error',
            'message' => 'Department not found in session'
        ];
    }

    $dept = $_SESSION['user']['dept'];
    try {
        $query = "SELECT uid, name, design FROM faculty WHERE dept = ? AND (design = 'Associate Professor' OR design = 'Assistant Professor') ORDER BY name";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("s", $dept);

        if (!$stmt->execute()) {
            return [
                'status' => 'error',
                'message' => 'Database query failed'
            ];
        }

        $result = $stmt->get_result();
        $faculty = [];

        while ($row = $result->fetch_assoc()) {
            $faculty[] = [
                'uid' => $row['uid'],
                'name' => $row['name'],
                'design' => $row['design']
            ];
        }

        return [
            'status' => 'success',
            'faculty' => $faculty
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

function assignAdvisor($erp_conn)
{
    $faculty_id = $_POST['faculty_id'];
    $batch = $_POST['batch'];
    $academic_year = $_POST['academic_year'];
    $semester = $_POST['semester'];
    $section = $_POST['section'];
    $sem_start_date = $_POST['sem_start_date'];
    $sem_end_date = $_POST['sem_end_date'];

    try {
        // First check if this faculty is already assigned to any class in the same semester
        $checkQuery = "SELECT COUNT(*) as count FROM class_advisors 
                      WHERE faculty_id = ? AND academic_year = ? 
                      ";
        $stmt = $erp_conn->prepare($checkQuery);
        $stmt->bind_param("is", $faculty_id, $academic_year,);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            return [
                'status' => 'error',
                'message' => 'This faculty is already assigned as advisor for another class'
            ];
        }

        // Check if this class already has an active advisor
        $checkClassQuery = "SELECT ca.faculty_id, f.dept, f.name FROM class_advisors ca
                            JOIN faculty f ON ca.faculty_id = f.uid
                            WHERE batch = ? AND section = ? AND academic_year = ? 
                            AND semester = ? ";
        $stmt = $erp_conn->prepare($checkClassQuery);
        $stmt->bind_param("sssi", $batch, $section, $academic_year, $semester);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // Class already has an active advisor, check if the departments match
            $existingFacultyId = $row['faculty_id'];
            $existingDept = $row['dept'];
            $existingFacultyName = $row['name'];
            // Get the new faculty's department
            $facultyDeptQuery = "SELECT dept FROM faculty WHERE uid = ?";
            $stmt = $erp_conn->prepare($facultyDeptQuery);
            $stmt->bind_param("i", $faculty_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $newFacultyDeptRow = $result->fetch_assoc();

            if ($newFacultyDeptRow) {
                $newFacultyDept = $newFacultyDeptRow['dept'];

                if ($existingDept === $newFacultyDept) {
                    // Departments match, return error message
                    return [
                        'status' => 'error',
                        'message' => 'This class is already assigned to : ' . $existingFacultyName . ' - ' . $existingDept
                    ];
                }
            }
        }

        // If all validations pass, insert the new advisor
        $insertQuery = "INSERT INTO class_advisors 
                       (faculty_id, batch, section, academic_year, semester, start_date, end_date) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $erp_conn->prepare($insertQuery);
        $stmt->bind_param("issssss", $faculty_id, $batch, $section, $academic_year, $semester, $sem_start_date, $sem_end_date);

        if (!$stmt->execute()) {
            return [
                'status' => 'error',
                'message' => 'Failed to assign advisor: ' . $stmt->error
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Advisor assigned successfully'
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

function getAdvisorStudents($erp_conn)
{
    // Get advisor data from session
    $faculty_uid = $_SESSION['user']['uid'];

    // Get the latest advisor record for this faculty
    $query = "SELECT ca.batch, ca.section, f.dept 
              FROM class_advisors ca 
              JOIN faculty f ON ca.faculty_id = f.uid 
              WHERE ca.faculty_id = ? 
              ORDER BY ca.advisor_id DESC 
              LIMIT 1";

    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("i", $faculty_uid);

    if (!$stmt->execute()) {
        return [
            'status' => 'error',
            'message' => 'Failed to fetch advisor details'
        ];
    }

    $result = $stmt->get_result();
    $advisorInfo = $result->fetch_assoc();

    if (!$advisorInfo) {
        return [
            'status' => 'error',
            'message' => 'No advisor records found'
        ];
    }

    // Now fetch students based on batch, section and department
    $studentsQuery = "SELECT uid, sid, sname, mail 
                     FROM student 
                     WHERE ayear = ? 
                     AND section = ? 
                     AND dept = ? 
                     AND status = 0 
                     ORDER BY sid";

    $stmt = $erp_conn->prepare($studentsQuery);
    $stmt->bind_param(
        "sss",
        $advisorInfo['batch'],
        $advisorInfo['section'],
        $advisorInfo['dept']
    );

    if (!$stmt->execute()) {
        return [
            'status' => 'error',
            'message' => 'Failed to fetch students'
        ];
    }

    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = [
            'id' => $row['uid'],
            'studentId' => $row['sid'],
            'name' => $row['sname'],
            'email' => $row['mail']
        ];
    }

    return [
        'status' => 'success',
        'students' => $students
    ];
}

// Function to map students to faculty
function mapStudentsToFaculty($erp_conn)
{
    if (!isset($_POST['facultyStudentMap']) || empty($_POST['facultyStudentMap'])) {
        return ["status" => "error", "message" => "No students data received."];
    }

    $dept = $_SESSION['user']['dept'];
    $courseCode = $_POST['courseCode'];
    $academicYear = $_POST['academicYear'];
    $semester = $_POST['semester'];
    $section = $_POST['section'];
    $batch = $_POST['batch'];

    try {
        // Check if the course already exists
        $checkCourseQuery = "SELECT course_id FROM course 
                             WHERE course_code = ? AND dept = ? 
                             AND ayear = ? AND semester = ?";
        $checkCourseStmt = $erp_conn->prepare($checkCourseQuery);
        $checkCourseStmt->bind_param("ssss", $courseCode, $dept, $academicYear, $semester);
        $checkCourseStmt->execute();
        $result = $checkCourseStmt->get_result();
        $courseRow = $result->fetch_assoc();

        if ($courseRow) {
            // Course exists, check for duplicates in course_faculty
            $courseId = $courseRow['course_id'];
            $checkMappingQuery = "SELECT COUNT(*) as count FROM course_faculty 
                                  WHERE course_id = ? AND section = ? AND batch =?";
            $checkMappingStmt = $erp_conn->prepare($checkMappingQuery);
            $checkMappingStmt->bind_param("iss", $courseId, $section, $batch);
            $checkMappingStmt->execute();
            $mappingResult = $checkMappingStmt->get_result();
            $mappingRow = $mappingResult->fetch_assoc();

            if ($mappingRow['count'] > 0) {
                return [
                    "status" => "error",
                    "message" => "This course is already mapped "
                ];
            }
        }

        // Insert the new course if it doesn't exist
        $courseQuery = "INSERT INTO course (course_code, course_name, course_credit, course_type, dept, ayear, semester,section) 
                       VALUES (?, ?, ?, ?, ?, ?, ?,?)";
        $stmt = $erp_conn->prepare($courseQuery);
        $stmt->bind_param(
            "ssisssss",
            $courseCode,
            $_POST['courseName'],
            $_POST['courseCredit'],
            $_POST['courseType'],
            $dept,
            $academicYear,
            $semester,
            $section
        );

        if (!$stmt->execute()) {
            return ["status" => "error", "message" => "Failed to insert course: " . $stmt->error];
        }

        $courseId = $erp_conn->insert_id;

        // Now insert faculty-student mappings
        $mappingQuery = "INSERT INTO course_faculty 
                        (course_id, faculty_id, batch, department, section, student_id) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $erp_conn->prepare($mappingQuery);

        foreach ($_POST['facultyStudentMap'] as $facultyId => $studentIds) {
            foreach ($studentIds as $studentId) {
                $stmt->bind_param(
                    "iissss",
                    $courseId,      // course_id
                    $facultyId,     // faculty_id
                    $batch,         // batch
                    $dept,          // department
                    $section,       // section
                    $studentId      // student_id
                );

                if (!$stmt->execute()) {
                    return ["status" => "error", "message" => "Insert failed: " . $stmt->error];
                }
            }
        }

        return ["status" => "success", "message" => "Course and mappings created successfully"];
    } catch (Exception $e) {
        return ["status" => "error", "message" => "Database error: " . $e->getMessage()];
    }
}
function getFacultyStudentUIDs($erp_conn)
{
    $mappingData = json_decode($_POST['mappingData'], true);
    $mappings = array();

    try {
        foreach ($mappingData as $mapping) {
            $facultyId = $mapping['Faculty ID'];
            $studentIds = array_map('trim', explode(',', $mapping['Student IDs (comma separated)']));

            // Get faculty UID
            $facultyQuery = "SELECT uid FROM faculty WHERE id = ?";
            $stmt = $erp_conn->prepare($facultyQuery);
            $stmt->bind_param("i", $facultyId);
            $stmt->execute();
            $facultyResult = $stmt->get_result();
            $facultyRow = $facultyResult->fetch_assoc();

            if ($facultyRow) {
                $facultyUid = $facultyRow['uid'];

                // Process each student ID
                foreach ($studentIds as $studentId) {
                    // Get student UID
                    $studentQuery = "SELECT uid FROM student WHERE sid = ?";
                    $stmt = $erp_conn->prepare($studentQuery);
                    $stmt->bind_param("s", $studentId);
                    $stmt->execute();
                    $studentResult = $stmt->get_result();
                    $studentRow = $studentResult->fetch_assoc();

                    if ($studentRow) {
                        $mappings[] = [
                            'faculty_uid' => $facultyUid,
                            'student_uid' => $studentRow['uid'],
                            // Use the key that is defined (assume "Course Code" header might be present)
                            'course_code' => isset($mapping['CourseCode']) ? $mapping['CourseCode'] : (isset($mapping['Course Code']) ? $mapping['Course Code'] : null)
                        ];
                    }
                }
            }
        }

        if (empty($mappings)) {
            return [
                'status' => 'error',
                'message' => 'No valid faculty-student mappings found'
            ];
        }

        return [
            'status' => 'success',
            'mappings' => $mappings
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

function getAvailableCourses($erp_conn)
{
    if (!isset($_POST['semester']) || !isset($_POST['academicYear']) || !isset($_POST['department']) ) {
        return [
            'status' => 'error',
            'message' => 'Missing required parameters'
        ];
    }

    $semester = $_POST['semester'];
    $academicYear = $_POST['academicYear'];
    $department = $_POST['department'];

    $query = "SELECT 
        c.course_id,
        c.course_name,
        c.course_code,
        c.course_credit,
        c.course_type,
        c.dept as department,
        c.ayear as academic_year,
        c.semester,
        GROUP_CONCAT(DISTINCT CONCAT(f.name, ' (', f.design, ')') SEPARATOR ', ') as faculty_list
    FROM course c
    LEFT JOIN course_faculty cf ON c.course_id = cf.course_id
    LEFT JOIN faculty f ON cf.faculty_id = f.uid
    WHERE c.semester = ? 
    AND c.ayear = ?
    AND c.dept = ?
    GROUP BY c.course_id";

    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("iss", $semester, $academicYear, $department);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $courses = [];

        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        return [
            "status" => "success",
            "courses" => $courses
        ];
    }

    return [
        "status" => "error",
        "message" => "Failed to fetch courses"
    ];
}

// Function to get advisor courses
function getAdvisorCourses($erp_conn)
{
    if (!isset($_SESSION['advisorData'])) {
        return ['status' => 'error', 'message' => 'No advisor data found in session'];
    }

    $advisorData = $_SESSION['advisorData'];
    $batch = $advisorData['batch'];
    $academicYear = $advisorData['academicYear'];
    $department = $advisorData['department']; // Added department filter
    $semester = $advisorData['semester'];
    $section = $advisorData['section'];

    try {
        // Get all courses with filtering on batch, academic year, semester, section, and department
        $courseQuery = "SELECT DISTINCT 
            c.course_id,
            c.course_name,
            c.course_code,
            c.course_credit,
            c.course_type,
            c.dept as department,
            c.ayear as academic_year,
            c.semester,
            c.lessonplan_edit_status
        FROM course c
        JOIN course_faculty cf ON c.course_id = cf.course_id
        WHERE cf.batch = ?
        AND c.ayear = ?
        AND c.semester = ?
        AND cf.section = ?
        AND c.dept = ?";

        $stmt = $erp_conn->prepare($courseQuery);
        $stmt->bind_param('ssiss', $batch, $academicYear, $semester, $section, $department);
        $stmt->execute();
        $courseResult = $stmt->get_result();

        $courses = [];
        while ($course = $courseResult->fetch_assoc()) {
            // For each course, get its faculty members
            $facultyQuery = "SELECT DISTINCT 
                f.uid as faculty_id,
                f.name as faculty_name,
                f.design as designation,
                cf.batch,
                cf.section
            FROM course_faculty cf
            JOIN faculty f ON cf.faculty_id = f.uid
            WHERE cf.course_id = ?
            AND cf.batch = ?
            AND cf.section = ?";
            $facultyStmt = $erp_conn->prepare($facultyQuery);
            $facultyStmt->bind_param('iss', $course['course_id'], $batch, $section);
            $facultyStmt->execute();
            $facultyResult = $facultyStmt->get_result();

            $facultyMembers = [];
            while ($faculty = $facultyResult->fetch_assoc()) {
                $facultyMembers[] = [
                    'id' => $faculty['faculty_id'],
                    'name' => $faculty['faculty_name'],
                    'designation' => $faculty['designation']
                ];
            }

            // Add faculty members to course data
            $courses[] = array_merge($course, [
                'faculty' => $facultyMembers,
                'batch' => $batch,
                'section' => $section
            ]);
        }

        return [
            'status' => 'success',
            'courses' => $courses
        ];
    } catch (Exception $e) {
        return [
            'status'  => 'error',
            'message' => $e->getMessage()
        ];
    }
}

// Add this function to your existing backend.php file
function getFacultyAcademics($erp_conn)
{
    if (!isset($_SESSION['user']['uid'])) {
        return [
            'status' => 'error',
            'message' => 'User not authenticated'
        ];
    }

    $faculty_id = $_SESSION['user']['uid'];

    $query = "SELECT DISTINCT 
                cf.batch,
                c.ayear as academic_year,
                c.semester,
                ? as faculty_id,  -- Include faculty_id in the result
                GROUP_CONCAT(DISTINCT c.course_name SEPARATOR ', ') as courses
              FROM course_faculty cf
              JOIN course c ON cf.course_id = c.course_id
              WHERE cf.faculty_id = ?
              GROUP BY cf.batch, c.ayear, c.semester
              ORDER BY c.ayear DESC, c.semester ASC";

    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("ii", $faculty_id, $faculty_id);

    if (!$stmt->execute()) {
        return [
            'status' => 'error',
            'message' => 'Database query failed'
        ];
    }

    $result = $stmt->get_result();
    $academics = [];

    while ($row = $result->fetch_assoc()) {
        $academics[] = $row;
    }

    return [
        'status' => 'success',
        'academics' => $academics
    ];
}




function getAttendanceView($erp_conn)
{
    // Validate required parameters
    if (
        !isset($_POST['date']) || !isset($_POST['batch']) ||
        !isset($_POST['semester']) || !isset($_POST['section']) ||
        !isset($_POST['department'])
    ) {  // Add department validation
        throw new Exception('Missing required parameters');
    }

    $date = $_POST['date'];
    $batch = $_POST['batch'];
    $semester = $_POST['semester'];
    $section = $_POST['section'];
    $department = $_POST['department'];  // Add department variable

    try {
        $advisorId = $_POST['advisorId'];

        // First get advisor's department
        $query = "SELECT dept FROM faculty WHERE id = ?";
        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare advisor query: " . $erp_conn->error);
        }

        $stmt->bind_param("i", $advisorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $advisorInfo = $result->fetch_assoc();

        if (!$advisorInfo) {
            throw new Exception('Faculty department not found');
        }


        // Get all students in the class
        $query = "SELECT  s.uid, s.sid as roll_no, s.sname , s.dept
                 FROM student s 
                 WHERE s.ayear = ? 
                 AND s.section = ?
                 AND s.dept = ?
                  /* Add explicit check for CS department students */
                 ORDER BY s.sid";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param(
            "sss",  // Changed to "sss" as dept is likely a string
            $batch,
            $section,
            $advisorInfo['dept']
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $students = $result->fetch_all(MYSQLI_ASSOC);

        // Get attendance for each student
        foreach ($students as &$student) {
            $query = "SELECT t.period, 
                            CASE 
                                WHEN as1.session_id IS NULL THEN 'NA'
                                WHEN as1.attendance_status = 0 THEN 'NA'
                                WHEN ae.attendance_status = 0 THEN 'A'
                                WHEN ae.attendance_status = 1 THEN 'L'
                                WHEN ae.attendance_status = 2 THEN 'OD'
                                when ae.attendance_status = 3 then 'P'
                            END as status
                     FROM timetable t
                     LEFT JOIN attendance_session as1 ON t.timetable_id = as1.timetable_id 
                        AND as1.class_date = ?
                     LEFT JOIN attendance_entry ae ON as1.session_id = ae.session_id 
                        AND ae.student_id = ?
                     WHERE t.semester = ?
                     ORDER BY t.period";

            $stmt = $erp_conn->prepare($query);

            $stmt->bind_param("sii", $date, $student['uid'], $semester);

            $stmt->execute();
            $hours = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $student['hours'] = [];
            foreach ($hours as $hour) {
                $student['hours'][$hour['period']] = $hour['status'];
            }
        }

        return [
            'status' => 'success',
            'attendance' => [
                'students' => $students,
                'total_hours' => count($hours)
            ]
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Failed to fetch attendance data: ' . $e->getMessage()
        ];
    }
}

function getTimetableCourses($erp_conn)
{
    if (!isset($_POST['advisorData'])) {
        return [
            'status' => 'error',
            'message' => 'Missing advisor data'
        ];
    }

    $advisorData = $_POST['advisorData'];

    try {
        $query = "SELECT DISTINCT 
                    c.course_id,
                    c.course_name,
                    c.course_code,
                    c.course_credit,
                    c.course_type,
                    cf.faculty_id,
                    CONCAT(f.name, '/', f.design) as faculty_name
                 FROM course c
                 JOIN course_faculty cf ON c.course_id = cf.course_id
                 JOIN faculty f ON cf.faculty_id = f.uid
                 WHERE cf.section = ? 
                 AND c.semester = ?
                 AND c.ayear = ?
                 AND cf.department = ?";

        $stmt = $erp_conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $erp_conn->error);
        }

        $stmt->bind_param(
            "siss",
            $advisorData['section'],
            $advisorData['semester'],
            $advisorData['academicYear'],
            $advisorData['department']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to execute query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $courses = [];

        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        return [
            'status' => 'success',
            'courses' => $courses
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

// Modify the saveTimetable handler
// Function to save the timetable
function saveTimetable($erp_conn, $timetableData)
{
    $erp_conn->begin_transaction();

    try {

        foreach ($timetableData as $record) {

            $facultyIds = is_array($record['faculty_id']) ? $record['faculty_id'] : [$record['faculty_id']];
            foreach ($facultyIds as $facultyId) {
                $conflictQuery = "SELECT COUNT(*) as cnt FROM timetable 
                                  WHERE day = ? AND period = ? AND academic_year = ? AND faculty_id = ?";
                $conflictStmt = $erp_conn->prepare($conflictQuery);
                if (!$conflictStmt) {
                    throw new Exception("Failed to prepare conflict check: " . $erp_conn->error);
                }


                $conflictStmt->bind_param("sisi", $record['day'], $record['period'], $record['academic_year'], $facultyId);
                $conflictStmt->execute();
                $result = $conflictStmt->get_result();
                $row = $result->fetch_assoc();
                if ($row && $row['cnt'] > 0) {
                    // Fetch faculty name
                    $query = "SELECT name FROM faculty WHERE uid = ?";
                    $querystmt = $erp_conn->prepare($query);
                
                    if (!$querystmt) {
                        return [
                            'status' => 'error',
                            'message' => "Failed to prepare statement: " . $erp_conn->error
                        ];
                    }
                
                    $querystmt->bind_param("i", $facultyId); 
                    $querystmt->execute();
                    $result = $querystmt->get_result();
                    $facultyRow = $result->fetch_assoc();
                    $name = $facultyRow ? $facultyRow['name'] : 'Unknown Faculty';
                
                    $querystmt->close();
                
                    return [
                        'status' => 'alert',
                        'message' => "Schedule conflict: Faculty ID $name already has a class on {$record['day']} during period {$record['period']}."
                    ];
                }
                  $conflictStmt->close();
            }
        }



        // Insert new entries
        $stmt = $erp_conn->prepare("INSERT INTO timetable 
            (course_id, faculty_id, day, period, batch, academic_year, semester, section) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($timetableData as $record) {
            // Handle multiple faculty assignments for the same timetable entry
            $facultyIds = is_array($record['faculty_id']) ? $record['faculty_id'] : [$record['faculty_id']];
            foreach ($facultyIds as $facultyId) {
                $stmt->bind_param(
                    "iisissis",
                    $record['course_id'],
                    $facultyId,
                    $record['day'],
                    $record['period'],
                    $record['batch'],
                    $record['academic_year'],
                    $record['semester'],
                    $record['section']
                );
                $stmt->execute();
            }
        }

        $erp_conn->commit();
        return [
            'status' => 'success',
            'message' => 'Timetable saved successfully',
            'savedRecords' => count($timetableData)
        ];
    } catch (Exception $e) {
        $erp_conn->rollback();
        return [
            'status' => 'info',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}





function getTimeTable($erp_conn, $batch, $semester, $section, $academicYear, $dept)
{
    $query = "
        SELECT 
            c.course_name,
            GROUP_CONCAT(f.name) as faculty_names,
            GROUP_CONCAT(f.uid) as faculty_ids,
            t.day, 
            t.period,
            t.timetable_id,
            t.section  -- Added section to verify
        FROM timetable t
        JOIN course c ON t.course_id = c.course_id 
        JOIN faculty f ON t.faculty_id = f.uid
        WHERE t.batch = ? 
        AND t.semester = ? 
        AND t.section = ?  -- Ensure exact section match
        AND t.academic_year = ? 
        AND c.dept = ?
        AND t.is_active = 1
        GROUP BY t.day, t.period, c.course_name, t.section  -- Added section to group by
    ";

    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("sisis", $batch, $semester, $section, $academicYear, $dept);

    if (!$stmt->execute()) {
        return [
            'status'  => 'error',
            'message' => 'Database query failed: ' . $stmt->error
        ];
    }

    $result = $stmt->get_result();

    // Debug: Print the query parameters
    error_log("Query params - Batch: $batch, Semester: $semester, Section: $section, Year: $academicYear, Dept: $dept");

    // Debug: Print the number of rows returned
    error_log("Number of rows returned: " . $result->num_rows);

    $timetable = [];

    // Initialize the timetable structure for each weekday
    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    foreach ($daysOfWeek as $day) {
        $timetable[$day] = array_fill(0, 8, ['name' => '...', 'teacher' => []]);
    }

    // Populate the timetable with data from the query result
    while ($row = $result->fetch_assoc()) {
        // Debug: Print each row's section
        error_log("Processing row for section: " . $row['section']);

        $day = $row['day'];
        $periodIndex = $row['period'] - 1;

        // Split the concatenated faculty names and ids
        $facultyNames = explode(',', $row['faculty_names']);
        $facultyIds = explode(',', $row['faculty_ids']);

        $teachers = array_map(function ($name, $id) {
            return [
                'name' => $name,
                'id' => $id
            ];
        }, $facultyNames, $facultyIds);

        $timetable[$day][$periodIndex] = [
            'name' => $row['course_name'],
            'teacher' => $teachers,
            'timetable_id' => $row['timetable_id']  // Include timetable_id in response
        ];
    }

    // If the timetable is empty, assign an empty array
    if (empty(array_filter($timetable, function ($day) {
        return !empty(array_filter($day, function ($period) {
            return $period['name'] !== '...' && !empty($period['teacher']);
        }));
    }))) {
        $timetable = [];
    }

    // Get timetable_edit_status
    $faculty_uid = $_SESSION['user']['uid'];
    $courseFacultyQuery = "
        SELECT timetable_edit_status
        FROM class_advisors
        WHERE faculty_id = ? AND batch = ? AND academic_year = ? AND semester = ? AND section = ?
    ";

    $stmtCourseFaculty = $erp_conn->prepare($courseFacultyQuery);
    $stmtCourseFaculty->bind_param("isiss", $faculty_uid, $batch, $academicYear, $semester, $section);
    $stmtCourseFaculty->execute();
    $resultCourseFaculty = $stmtCourseFaculty->get_result();
    $timetable_edit_status = $resultCourseFaculty->fetch_assoc()['timetable_edit_status'];

    return [
        'status' => 'success',
        'timetable' => $timetable,
        'timetable_edit_status' => $timetable_edit_status
    ];
}
function getFacultyTimeTable($erp_conn, $facultyId, $academic_year, $semesterType)
{
    $parity = ($semesterType === 'Even') ? 0 : 1;

    // First get the regular timetable
    $query = "
        SELECT t.*, c.course_name, c.dept, c.semester AS course_semester,
               t.batch, t.section 
        FROM timetable t
        JOIN course c ON t.course_id = c.course_id
        WHERE t.faculty_id = ? AND t.academic_year = ? AND (t.semester % 2) = ? AND t.is_active = 1
    ";

    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("isi", $facultyId, $academic_year, $parity);

    if (!$stmt->execute()) {
        return [
            'status' => 'error',
            'message' => 'Database query failed: ' . $stmt->error
        ];
    }

    $result = $stmt->get_result();
    $timetable = [];
    $courseDetails = null;

    // Initialize timetable structure
    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    foreach ($daysOfWeek as $day) {
        $timetable[$day] = array_fill(0, 8, ['name' => '...']);
    }

    // Populate regular timetable and get class details
    while ($row = $result->fetch_assoc()) {
        $day = $row['day'];
        $periodIndex = $row['period'] - 1;
        $timetable[$day][$periodIndex] = [
            'name' => $row['course_name'],
            'dept' => $row['dept'],
            'semester' => $row['course_semester'],
            'course_id' => $row['course_id'],
            'batch' => $row['batch'], // Include batch
            'section' => $row['section'] // Include section
        ];

        // Store first course details for class identification
        if (!$courseDetails) {
            $courseDetails = [
                'batch' => $row['batch'],
                'semester' => $row['course_semester'],
                'section' => $row['section']
            ];
        }
    }


    if ($courseDetails) {
        $today = new DateTime();
        $saturday = clone $today;
        while ($saturday->format('N') != 6) {
            $saturday->modify('+1 day');
        }

        $overrideQuery = "
            SELECT 
                o.assigned_day,
                t2.period,
                t2.course_id,
                c.course_name,
                c.dept,
                c.semester AS course_semester,
                t2.batch, 
                t2.section  
            FROM day_order_override o
            JOIN timetable t2 ON t2.batch = o.batch 
                AND t2.semester = o.semester 
                AND t2.section = o.section
                AND t2.academic_year = o.academic_year
                AND t2.day = o.assigned_day
            JOIN course c ON t2.course_id = c.course_id
            JOIN course_faculty cf ON c.course_id = cf.course_id 
                AND cf.section = t2.section
                AND cf.faculty_id = ?
            WHERE o.override_date = ? 
                AND o.batch = ? 
                AND o.academic_year = ? 
                AND o.semester = ? 
                AND o.section = ?
                AND t2.is_active = 1 
            GROUP BY t2.period, t2.course_id, t2.batch, t2.section
        ";

        $stmt = $erp_conn->prepare($overrideQuery);
        $saturdayDate = $saturday->format('Y-m-d');
        $stmt->bind_param(
            "issssi",
            $facultyId,
            $saturdayDate,
            $courseDetails['batch'],
            $academic_year,
            $courseDetails['semester'],
            $courseDetails['section']
        );

        if ($stmt->execute()) {
            $overrideResult = $stmt->get_result();

            // Create temporary storage for Saturday overrides
            $saturdayOverrides = array_fill(0, 8, ['name' => '...']);

            while ($row = $overrideResult->fetch_assoc()) {
                $period = $row['period'] - 1;

                // Update Saturday schedule for this course
                $saturdayOverrides[$period] = [
                    'name' => $row['course_name'],
                    'dept' => $row['dept'],
                    'semester' => $row['course_semester'],
                    'course_id' => $row['course_id'],
                    'batch' => $row['batch'], // Use batch from the override result
                    'section' => $row['section'] // Use section from the override result
                ];
            }

            // Merge existing Saturday schedule with overrides
            foreach ($saturdayOverrides as $period => $override) {
                if ($override['name'] !== '...') {
                    $timetable['Saturday'][$period] = $override;
                }
            }
        }
    }

    return [
        'status' => 'success',
        'timetable' => $timetable,
        'batch' => $courseDetails['batch'], // Include batch in response
        'section' => $courseDetails['section'] // Include section in response
    ];
}
function getStudentAttendance($erp_conn)
{
    // Step 1: Get student IDs from the course_faculty table
    $query = "SELECT student_id FROM course_faculty WHERE course_id = ? AND faculty_id = ?";
    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("ii", $_POST['courseId'], $_POST['facultyId']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch student IDs
    $student_ids = [];
    while ($row = $result->fetch_assoc()) {
        $student_ids[] = $row['student_id'];
    }

    // If no students found, return empty result
    if (empty($student_ids)) {
        return [
            'status' => 'success',
            'students' => []
        ];
    }

    // Step 2: Get student details from the student table
    $placeholders = implode(',', array_fill(0, count($student_ids), '?'));
    $query = "SELECT uid, sid as rollNo, sname as name, ayear as academicYear, dept as department, section FROM student WHERE uid IN ($placeholders)";

    $stmt = $erp_conn->prepare($query);

    // Bind parameters dynamically
    $types = str_repeat("i", count($student_ids)); // Create a string of 'i' for integers
    $stmt->bind_param($types, ...$student_ids);

    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all student details
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    return [
        'status' => 'success',
        'students' => $students
    ];
}
function getLessonPlanData($erp_conn)
{
    if (!isset($_POST['courseId'])) {
        return [
            'status' => 'error',
            'message' => 'Course ID is required'
        ];
    }

    try {
        $courseId = intval($_POST['courseId']);

        // Modified query to use unit_name without alias
        $query = "SELECT *
                 FROM unit u 
                 LEFT JOIN important_topic it ON u.unit_id = it.unit_id 
                 WHERE u.course_id = ?
                 ORDER BY u.unit_number, it.topic_id";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();

        $units = [];
        $currentUnit = null;

        while ($row = $result->fetch_assoc()) {
            if ($currentUnit === null || $currentUnit['unit_id'] !== $row['unit_id']) {
                if ($currentUnit !== null) {
                    $units[] = $currentUnit;
                }
                $currentUnit = [
                    'unit_id' => $row['unit_id'],
                    'unit_number' => $row['unit_number'],
                    'unit_name' => $row['unit_name'],  // Changed to use unit_name directly
                    'name' => $row['unit_name'],       // Add both unit_name and name for compatibility
                    'CO' => $row['CO'],
                    'co_weightage' => $row['co_weightage'],
                    'co_threshold' => $row['co_threshold'],
                    'topics' => []
                ];
            }

            if ($row['topic_name']) {
                $currentUnit['topics'][] = ['name' => $row['topic_name']];
            }
        }

        if ($currentUnit !== null) {
            $units[] = $currentUnit;
        }

        return [
            'status' => 'success',
            'data' => $units
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
function saveLessonPlan($erp_conn)
{
    if (!isset($_POST['formData'])) {
        return [
            'status' => 'error',
            'message' => 'No form data provided'
        ];
    }

    try {
        $formData = json_decode($_POST['formData'], true);
        if (!$formData) {
            throw new Exception('Invalid JSON data');
        }

        // Validate course_id
        if (!isset($formData['courseId']) || empty($formData['courseId'])) {
            throw new Exception('Course ID is required');
        }

        $courseId = intval($formData['courseId']);
        if ($courseId <= 0) {
            throw new Exception('Invalid Course ID');
        }

        $erp_conn->begin_transaction();

        // First, delete existing units and topics for this course
        $deleteUnitsStmt = $erp_conn->prepare("DELETE FROM unit WHERE course_id = ?");
        $deleteUnitsStmt->bind_param("i", $courseId);
        $deleteUnitsStmt->execute();

        // Validate units array
        if (!isset($formData['units']) || !is_array($formData['units']) || empty($formData['units'])) {
            throw new Exception('No units provided');
        }

        // Insert units and their topics
        foreach ($formData['units'] as $unit) {
            // Validate unit data
            if (!isset($unit['name']) || !isset($unit['CO']) || !isset($unit['unitNumber']) || !isset($unit['weightage'])) {
                throw new Exception('Invalid unit data structure');
            }

            // Insert unit
            $unitQuery = "INSERT INTO unit (course_id, unit_number, unit_name, CO, co_weightage, co_threshold) 
                         VALUES (?, ?, ?, ?, ?, ?)";
            $unitStmt = $erp_conn->prepare($unitQuery);
            $unitStmt->bind_param(
                "iissii",
                $courseId,
                $unit['unitNumber'],
                $unit['name'],
                $unit['CO'],
                $unit['weightage'],
                $unit['co_threshold']
            );

            if (!$unitStmt->execute()) {
                throw new Exception("Failed to insert unit: " . $unitStmt->error);
            }

            $unitId = $erp_conn->insert_id;

            // Insert topics if they exist
            if (!empty($unit['topics'])) {
                $topicQuery = "INSERT INTO important_topic (unit_id, topic_name) 
                             VALUES (?, ?)";
                $topicStmt = $erp_conn->prepare($topicQuery);

                foreach ($unit['topics'] as $topic) {
                    if (!isset($topic['name']) || empty($topic['name'])) {
                        continue; // Skip empty topics
                    }
                    $topicStmt->bind_param("is", $unitId, $topic['name']);

                    if (!$topicStmt->execute()) {
                        throw new Exception("Failed to insert topic: " . $topicStmt->error);
                    }
                }
            }
        }

        $erp_conn->commit();
        return [
            'status' => 'success',
            'message' => 'Lesson plan saved successfully'
        ];
    } catch (Exception $e) {
        $erp_conn->rollback();
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

function getFacultyList($erp_conn)
{
    try {
        $timetableId = $_POST['timetableId'];

        // Get course and class details from timetable
        $query = "
            SELECT t.*, c.dept, c.semester, c.ayear,c.course_id
            FROM timetable t
            JOIN course c ON t.course_id = c.course_id 
            WHERE t.timetable_id = ?";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("i", $timetableId);
        $stmt->execute();
        $result = $stmt->get_result();
        $timetableData = $result->fetch_assoc();

        if (!$timetableData) {
            throw new Exception('Timetable entry not found');
        }

        // Find advisor for this class
        $advisorQuery = "
            SELECT ca.* 
            FROM class_advisors ca
            WHERE ca.batch = ? 
            AND ca.semester = ?
            AND ca.section = ?
            AND ca.academic_year = ?
            AND CURRENT_DATE BETWEEN ca.start_date AND ca.end_date";

        $stmt = $erp_conn->prepare($advisorQuery);
        $stmt->bind_param(
            "siss",
            $timetableData['batch'],
            $timetableData['semester'],
            $timetableData['section'],
            $timetableData['ayear']
        );
        $stmt->execute();
        $advisorResult = $stmt->get_result();
        $advisorData = $advisorResult->fetch_assoc();

        if (!$advisorData) {
            throw new Exception('No active advisor found for this class');
        }

        // Get all faculty teaching courses in this class
        $facultyQuery = "
            SELECT DISTINCT 
                f.uid as faculty_id,
                f.name as faculty_name,
                c.course_name,
                c.course_code,
                c.course_id
            FROM course_faculty cf
            JOIN faculty f ON cf.faculty_id = f.uid
            JOIN course c ON cf.course_id = c.course_id
            WHERE cf.batch = ?
            AND cf.section = ?
            AND c.semester = ?
            AND c.ayear = ?
            AND f.status = 1
            ORDER BY f.name ASC";

        $stmt = $erp_conn->prepare($facultyQuery);
        $stmt->bind_param(
            "ssis",
            $timetableData['batch'],
            $timetableData['section'],
            $timetableData['semester'],
            $timetableData['ayear']
        );
        $stmt->execute();
        $result = $stmt->get_result();

        $facultyList = [];
        while ($row = $result->fetch_assoc()) {
            $facultyList[] = [
                'id' => $row['faculty_id'],
                'name' => $row['faculty_name'],
                'course_name' => $row['course_name'],
                'course_code' => $row['course_code'],
                'course_id' => $row['course_id']
            ];
        }

        return [
            'status' => 'success',
            'faculty' => $facultyList
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getFacultyClasses($erp_conn)
{
    try {
        // Ensure all required parameters are provided
        if (
            !isset($_POST['facultyId']) ||
            !isset($_POST['date']) ||
            !isset($_POST['academicYear']) ||
            !isset($_POST['semesterType'])
        ) {
            throw new Exception('Missing required parameters');
        }

        $facultyId   = $_POST['facultyId'];
        $date        = $_POST['date'];
        $academicYear = $_POST['academicYear'];
        $semesterType = $_POST['semesterType'];

        // Determine semester parity: Even => 0, Odd => 1
        $parity = ($semesterType === 'Even') ? 0 : 1;

        // Get day name from date (e.g., Monday, Tuesday, etc.)
        $dayOfWeek = date('l', strtotime($date));

        // Modified query: filtering based on faculty_id, day, academic_year,
        // and checking the parity of the semester.
        $query = "
            SELECT 
                t.timetable_id,
                t.period,
                c.course_code,
                c.course_name,
                c.course_id,
                t.section

            FROM timetable t
            JOIN course c ON t.course_id = c.course_id
            WHERE 
                t.faculty_id = ? 
                AND t.day = ?
                AND t.academic_year = ?
                AND (t.semester % 2) = ?
            GROUP BY t.period
            ORDER BY t.period ASC
        ";

        $stmt = $erp_conn->prepare($query);
        // Bind parameters: i->facultyId, s->dayOfWeek, s->academicYear, i->parity
        $stmt->bind_param("issi", $facultyId, $dayOfWeek, $academicYear, $parity);
        $stmt->execute();
        $result = $stmt->get_result();

        $classes = [];
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }

        if (empty($classes)) {
            return [
                'status' => 'error',
                'message' => 'No classes found for this date'
            ];
        }

        return [
            'status' => 'success',
            'classes' => $classes
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Error fetching classes: ' . $e->getMessage()
        ];
    }
}

function getHourAlterations($erp_conn)
{
    try {
        // Validate required parameters
        if (
            !isset($_POST['facultyId']) ||
            !isset($_POST['academicYear']) ||
            !isset($_POST['semesterType'])
        ) {
            throw new Exception('Missing required parameters');
        }

        $facultyId   = $_POST['facultyId'];
        $academicYear = $_POST['academicYear'];
        $semesterType = $_POST['semesterType'];

        // Determine semester parity: Even -> 0, Odd -> 1
        $parity = ($semesterType === 'Even') ? 0 : 1;

        // Complex query joining multiple tables to get all required information,
        // filtered by faculty, academic year, and semester parity.
        $query = "
            SELECT 
                ha.alteration_id,
                ha.date,
                ha.reason,
                ha.status,
                t.period,
                c.course_name,
                f.name as substitute_name
            FROM hour_alteration ha
            JOIN timetable t ON ha.timetable_id = t.timetable_id
            JOIN course c ON t.course_id = c.course_id
            JOIN faculty f ON ha.new_faculty_id = f.uid
            WHERE 
                ha.original_faculty_id = ? 
                AND t.academic_year = ?
                AND (t.semester % 2) = ?
                AND ha.status = 'Pending'
            ORDER BY ha.date ASC, t.period ASC
        ";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("isi", $facultyId, $academicYear, $parity);
        $stmt->execute();
        $result = $stmt->get_result();

        $alterations = [];
        while ($row = $result->fetch_assoc()) {
            $alterations[] = [
                'alteration_id'   => $row['alteration_id'],
                'date'            => $row['date'],
                'period'          => $row['period'],
                'course_name'     => $row['course_name'],
                'substitute_name' => $row['substitute_name'],
                'reason'          => $row['reason'],
                'status'          => $row['status']
            ];
        }

        return [
            'status' => 'success',
            'alterations' => $alterations
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Error fetching alterations: ' . $e->getMessage()
        ];
    }
}

function deleteHourAlteration($erp_conn)
{
    try {
        // Check that required parameters are provided
        if (!isset($_POST['alteration_id']) || !isset($_POST['facultyId'])) {
            throw new Exception('Required parameters missing');
        }

        $alteration_id = (int) $_POST['alteration_id'];
        $facultyId = (int) $_POST['facultyId'];

        // Only delete if the request is still pending and belongs to the logged-in faculty
        $query = "DELETE FROM hour_alteration WHERE alteration_id = ? AND original_faculty_id = ? AND status = 'Pending'";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ii", $alteration_id, $facultyId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            return [
                'status' => 'success',
                'message' => 'Alteration request deleted successfully'
            ];
        } else {
            throw new Exception("No matching pending alteration request found");
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Error deleting alteration request: ' . $e->getMessage()
        ];
    }
}

function submitAlterationRequest($erp_conn)
{
    try {
        $formData = json_decode($_POST['formData'], true);

        if (!$formData) {
            throw new Exception('Invalid form data');
        }

        // Validate date
        $date = $formData['date'];
        if (empty($date)) {
            throw new Exception('Date is required');
        }

        // Ensure date is not in the past
        $selectedDate = new DateTime($date);
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        if ($selectedDate < $today) {
            throw new Exception('Cannot select a past date');
        }

        // First get the timetable details
        $timetableQuery = "
            SELECT 
                t.course_id,
                t.faculty_id as original_faculty_id,
                t.day,
                t.period,
                t.batch,
                t.academic_year,
                t.semester,
                t.section
            FROM timetable t
            WHERE t.timetable_id = ?";

        $stmt = $erp_conn->prepare($timetableQuery);
        $stmt->bind_param("i", $formData['timetable_id']);
        $stmt->execute();
        $timetableResult = $stmt->get_result();
        $timetableData = $timetableResult->fetch_assoc();

        if (!$timetableData) {
            throw new Exception('Timetable entry not found');
        }

        // Begin transaction
        $erp_conn->begin_transaction();

        try {
            // Insert into hour_alterations with date
            //            //ALTER TABLE alteration
            // ADD COLUMN original_faculty_course_id INT(11) NOT NULL AFTER original_faculty_id,
            // ADD COLUMN new_faculty_course_id INT(11) NOT NULL AFTER new_faculty_id;
            $insertQuery = "
                INSERT INTO hour_alteration (
                    timetable_id,
                    original_faculty_id,
                    original_faculty_course_id,
                    new_faculty_id,
                    new_faculty_course_id,
                    reason,
                    date,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

            $stmt = $erp_conn->prepare($insertQuery);
            $stmt->bind_param(
                "iiiiiss",
                $formData['timetable_id'],
                $timetableData['original_faculty_id'],
                $timetableData['course_id'],
                $formData['substitute_faculty'],
                $formData['new_faculty_course_id'],
                $formData['reason'],
                $formData['date']
            );
            $stmt->execute();

            if ($stmt->affected_rows <= 0) {
                throw new Exception('Failed to insert alteration request');
            }

            $erp_conn->commit();

            return [
                'status' => 'success',
                'message' => 'Alteration request submitted successfully',
                'alteration_id' => $erp_conn->insert_id
            ];
        } catch (Exception $e) {
            $erp_conn->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Error submitting alteration request: ' . $e->getMessage()
        ];
    }
}


// To mark attendance for the first time - Modify this
function saveAttendance($erp_conn)
{
    // Validate required fields

    $requiredFields = [
        'facultyId',
        'semester',
        'hourType',
        'unitId',
        'unitName',
        'topicName',
        'description',
        'attendanceData',
        'hour',
        'courseId',
        'date'
    ];



    // foreach ($requiredFields as $field) {
    //     if (!isset($_POST[$field])) {
    //         return [
    //             'status' => 'error',
    //             'message' => "Missing required field: {$field}"
    //         ];
    //     }
    // }


    try {
        $erp_conn->begin_transaction();

        $facultyId = $_POST['facultyId'];
        $hour = $_POST['hour'];
        $courseId = $_POST['courseId'];
        $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');

        // Get the existing session ID
        $query = "SELECT as1.session_id 
                 FROM attendance_session as1
                 JOIN timetable t ON as1.timetable_id = t.timetable_id 
                 WHERE t.faculty_id = ? 
                 AND t.period = ? 
                 AND t.course_id = ?
                 AND as1.class_date = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("iiis", $facultyId, $hour, $courseId, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $sessionData = $result->fetch_assoc();

        if (!$sessionData) {
            throw new Exception('Session not found for period ' . $hour);
        }

        // Update the attendance session with the new details
        $query = "UPDATE attendance_session 
                 SET unit_id = ?,
                     unit_name = ?,
                     topic_name = ?,
                     description = ?,
                     marked_by = ?,
                     attendance_status = 1
                 WHERE session_id = ?";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param(
            "isssii",
            $_POST['unitId'],
            $_POST['unitName'],
            $_POST['topicName'],
            $_POST['description'],
            $facultyId,
            $sessionData['session_id']
        );
        $stmt->execute();

        // Insert attendance entries
        $attendanceData = json_decode($_POST['attendanceData'], true);
        foreach ($attendanceData as $attendance) {
            // Only record absent students
            $studentId = getStudentId($erp_conn, $attendance['rollNo']);

            $query = "INSERT INTO attendance_entry 
                         (session_id, semester, student_id, attendance_status, marked_by) 
                         VALUES (?, ?, ?, ?, 'Faculty')";

            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param(
                "iiii",
                $sessionData['session_id'],
                $_POST['semester'],
                $studentId,
                $attendance['status']
            );
            $stmt->execute();
        }

        $erp_conn->commit();
        return [
            'status' => 'success',
            'message' => 'Attendance saved successfully for period ' . $hour
        ];
    } catch (Exception $e) {
        $erp_conn->rollback();
        return [
            'status' => 'error',
            'message' => 'Failed to save attendance: ' . $e->getMessage()
        ];
    }
}

// Helper function to get student ID
function getStudentId($erp_conn, $rollNo)
{
    $query = "SELECT uid FROM student WHERE sid = ?";
    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param("s", $rollNo);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        throw new Exception('Student not found');
    }

    return $data['uid'];
}

function checkAttendanceStatus($erp_conn)
{
    // Validate required parameters including 'day'
    if (!isset($_POST['courseId'], $_POST['facultyId'], $_POST['date'], $_POST['hour'], $_POST['day'], $_POST['type'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required parameters'
        ]);
        exit;
    }

    try {
        $courseId = $_POST['courseId'];
        $facultyId = $_POST['facultyId'];
        $date = $_POST['date'];
        $hour = $_POST['hour'];
        $day = $_POST['day'];
        $type = $_POST['type'];
        // Check if the given day is Saturday
        if ($day === 'Saturday') {
            // Get distinct section, batch, ayear, and semester for the course
            $query = "
                SELECT DISTINCT 
                    cf.section, 
                    cf.batch, 
                    c.ayear, 
                    c.semester
                FROM course c
                JOIN course_faculty cf ON cf.course_id = c.course_id
                WHERE c.course_id = ?
            ";
            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $result = $stmt->get_result();
            $courseData = $result->fetch_assoc();

            if (!$courseData) {
                throw new Exception('No course data found');
            }

            // Check the day_order_override table for the given date
            $query = "SELECT assigned_day FROM day_order_override 
                      WHERE override_date = ? AND batch = ? AND academic_year = ? AND semester = ? AND section = ?";
            $stmt = $erp_conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $erp_conn->error);
            }
            $stmt->bind_param("sssss", $date, $courseData['batch'], $courseData['ayear'], $courseData['semester'], $courseData['section']);
            $stmt->execute();
            $result = $stmt->get_result();
            $overrideData = $result->fetch_assoc();

            if ($overrideData) {
                $day = $overrideData['assigned_day'];
            } else {
                echo json_encode([
                    'status' => 10,
                ]);
                exit;
            }
        }
        if ($type === "pendingAttendance") {
            $query = "SELECT timetable_id FROM timetable 
                  WHERE course_id = ? AND faculty_id = ? AND day = ? AND period = ? ";
        } else {
            $query = "SELECT timetable_id FROM timetable 
                  WHERE course_id = ? AND faculty_id = ? AND day = ? AND period = ? AND is_active = 1";
        }
        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $erp_conn->error);
        }
        $stmt->bind_param("iisi", $courseId, $facultyId, $day, $hour);
        $stmt->execute();
        $result = $stmt->get_result();
        $timetableData = $result->fetch_assoc();

        if (!$timetableData) {
            throw new Exception("Timetable entry not found");
        }

        $query = "SELECT attendance_status FROM attendance_session 
                  WHERE timetable_id = ? AND class_date = ?";
        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $erp_conn->error);
        }
        $stmt->bind_param("is", $timetableData['timetable_id'], $date);
        $stmt->execute();
        $result = $stmt->get_result();

        // If a record is found, return its attendance_status; otherwise, return 10
        if ($row = $result->fetch_assoc()) {
            $attendanceStatus = (int)$row['attendance_status'];
        } else {
            $attendanceStatus = 10;
        }

        echo json_encode([
            'status' => $attendanceStatus,
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}


function editTimeTable($erp_conn, $timetableData)
{
    try {
        $erp_conn->begin_transaction();

        // First, check for schedule conflicts before marking existing records as inactive
        foreach ($timetableData as $record) {
            $facultyIds = is_array($record['faculty_id']) ? $record['faculty_id'] : [$record['faculty_id']];
            foreach ($facultyIds as $facultyId) {
                $conflictQuery = "SELECT COUNT(*) as cnt FROM timetable 
                                  WHERE day = ? AND period = ? AND academic_year = ? AND faculty_id = ?";
                $conflictStmt = $erp_conn->prepare($conflictQuery);
                if (!$conflictStmt) {
                    throw new Exception("Failed to prepare conflict check: " . $erp_conn->error);
                }

                $conflictStmt->bind_param("sisi", $record['day'], $record['period'], $record['academic_year'], $facultyId);
                $conflictStmt->execute();
                $result = $conflictStmt->get_result();
                $row = $result->fetch_assoc();
                if ($row && $row['cnt'] > 0) {
                    // Fetch faculty name
                    $query = "SELECT name FROM faculty WHERE uid = ?";
                    $querystmt = $erp_conn->prepare($query);
                
                    if (!$querystmt) {
                        return [
                            'status' => 'error',
                            'message' => "Failed to prepare statement: " . $erp_conn->error
                        ];
                    }
                
                    $querystmt->bind_param("i", $facultyId); 
                    $querystmt->execute();
                    $result = $querystmt->get_result();
                    $facultyRow = $result->fetch_assoc();
                    $name = $facultyRow ? $facultyRow['name'] : 'Unknown Faculty';
                
                    $querystmt->close();
                
                    return [
                        'status' => 'alert',
                        'message' => "Schedule conflict: Faculty  $name already has a class on {$record['day']} during period {$record['period']}."
                    ];
                }
                $conflictStmt->close();
            }
        }

        // Mark existing records as inactive
        foreach ($timetableData as $record) {
            $deactivateQuery = "UPDATE timetable 
                              SET is_active = 0 
                              WHERE academic_year = ? 
                              AND batch = ? 
                              AND day = ? 
                              AND period = ? 
                              AND section = ?
                              AND is_active = 1";

            $deactivateStmt = $erp_conn->prepare($deactivateQuery);
            $deactivateStmt->bind_param(
                "sssis",
                $record['academic_year'],
                $record['batch'],
                $record['day'],
                $record['period'],
                $record['section']
            );
            $deactivateStmt->execute();
            $deactivateStmt->close();
        }

        // Then insert new records with is_active = 1
        $insertQuery = "INSERT INTO timetable 
            (course_id, faculty_id, day, period, batch, academic_year, semester, section, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";

        $insertStmt = $erp_conn->prepare($insertQuery);

        foreach ($timetableData as $record) {
            $insertStmt->bind_param(
                "iisissis",
                $record['course_id'],
                $record['faculty_id'],
                $record['day'],
                $record['period'],
                $record['batch'],
                $record['academic_year'],
                $record['semester'],
                $record['section']
            );

            if (!$insertStmt->execute()) {
                throw new Exception("Failed to insert record: " . $insertStmt->error);
            }
        }

        $insertStmt->close();
        $erp_conn->commit();

        return [
            'status' => 'success',
            'message' => 'Timetable updated successfully'
        ];
    } catch (Exception $e) {
        $erp_conn->rollback();
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}



function loadIncomingAlterations($erp_conn)
{
    try {
        $facultyId = $_POST['facultyId'];
        $query = "SELECT * FROM hour_alteration WHERE new_faculty_id = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $alterations = $result->fetch_all(MYSQLI_ASSOC);
        return [
            'status' => 'success',
            'alterations' => $alterations
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getFacultyPendingAttendance($erp_conn)
{
    if (!isset($_POST['facultyId'])) {
        return json_encode([
            'status' => 'error',
            'message' => 'Faculty ID required'
        ]);
    }

    try {
        $facultyId = $_POST['facultyId'];
        $academic_year = $_POST['academicYear'];
        $semesterType = $_POST['semesterType'];

        // Determine semester parity based on semesterType
        $semesterCondition = ($semesterType === 'Even') ?
            't.semester IN (2, 4, 6, 8)' :
            't.semester IN (1, 3, 5, 7)';

        // Get pending attendance entries for this faculty
        $query = "SELECT 
                    a.session_id,
                    a.class_date,
                    t.period,
                    t.day,
                    t.section,
                    t.batch,
                    t.semester,
                    t.academic_year,
                    c.course_id,
                    c.course_name,
                    c.course_code,
                    a.attendance_status
                 FROM attendance_session a 
                 JOIN timetable t ON a.timetable_id = t.timetable_id
                 JOIN course c ON t.course_id = c.course_id
                 WHERE t.faculty_id = ?
                 AND a.attendance_status IN (0, 4)
                 AND a.class_date <= CURDATE() - INTERVAL 1 DAY
                 AND t.academic_year = ?
                 AND $semesterCondition
                 ORDER BY a.class_date DESC, t.period ASC";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("is", $facultyId, $academic_year);
        $stmt->execute();
        $result = $stmt->get_result();
        $pendingList = $result->fetch_all(MYSQLI_ASSOC);

        // Get all courses assigned to faculty for reference
        $query1 = "SELECT 
                    t.timetable_id,
                    t.day,
                    t.period,
                    t.section,
                    t.batch,
                    t.semester,
                    t.academic_year,
                    c.course_id,
                    c.course_name,
                    c.course_code,
                    c.course_type
                 FROM timetable t
                 JOIN course c ON t.course_id = c.course_id 
                 WHERE t.faculty_id = ?
                 AND t.academic_year = ?
                 AND $semesterCondition";

        $stmt1 = $erp_conn->prepare($query1);
        $stmt1->bind_param("is", $facultyId, $academic_year);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $courseData = $result1->fetch_all(MYSQLI_ASSOC);

        return json_encode([
            'status' => 'success',
            'pendingList' => $pendingList,
            'courseData' => $courseData
        ]);
    } catch (Exception $e) {
        return json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

// Function to submit leave request
function submitLeaveRequest($erp_conn)
{
    if (
        !isset($_POST['student_id']) || !isset($_POST['leave_type']) ||
        !isset($_POST['start_date']) || !isset($_POST['end_date']) ||
        !isset($_POST['reason'])
    ) {
        return [
            'status' => 'error',
            'message' => 'Missing required fields'
        ];
    }

    try {
        // Get student UID from student ID (roll number)
        $query = "SELECT uid FROM student WHERE sid = ?";
        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error: Failed to prepare statement for student lookup.");
        }

        $stmt->bind_param("s", $_POST['student_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();

        if (!$student) {
            return [
                'status' => 'error',
                'message' => 'Student not found'
            ];
        }

        // Set semester type and academic year (Modify if dynamic)
        $semesterType = 'Even';
        $academic_year = '2024-25'; // Make dynamic if needed

        // Insert leave request
        $query = "INSERT INTO leave_requests (user_id, user_type, leave_type, start_date, end_date, status, academicYear, semesterType, reason) 
                  VALUES (?, 'Student', ?, ?, ?, 'Pending', ?, ?, ?)";

        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Database error: Failed to prepare statement for leave request.");
        }

        $stmt->bind_param(
            "issssss",
            $student['uid'],
            $_POST['leave_type'],
            $_POST['start_date'],
            $_POST['end_date'],
            $academic_year,
            $semesterType,
            $_POST['reason']
        );

        if (!$stmt->execute()) {
            throw new Exception('Failed to submit leave request');
        }

        $stmt->close();

        return [
            'status' => 'success',
            'message' => 'Leave request submitted successfully',
            'request_id' => $erp_conn->insert_id
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Failed to submit leave request: ' . $e->getMessage()
        ];
    }
}



function saveMultipleAttendance($erp_conn)
{
    try {
        $erp_conn->begin_transaction();

        $facultyId = $_POST['facultyId'];
        $courseId = $_POST['courseId'];
        $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
        $hours = json_decode($_POST['hours'], true);

        // Common data for all sessions
        $semester = $_POST['semester'];
        $unitId = $_POST['unitId'];
        $unitName = $_POST['unitName'];
        $topicName = $_POST['topicName'];
        $description = $_POST['description'];
        $attendanceData = json_decode($_POST['attendanceData'], true);

        $sessions = [];

        // Process each hour
        foreach ($hours as $hour) {
            // First check if session exists
            $query = "SELECT as1.session_id 
                     FROM attendance_session as1
                     JOIN timetable t ON as1.timetable_id = t.timetable_id 
                     WHERE t.faculty_id = ? 
                     AND t.period = ? 
                     AND t.course_id = ?
                     AND as1.class_date = ?";

            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param("iiis", $facultyId, $hour, $courseId, $date);
            $stmt->execute();
            $result = $stmt->get_result();
            $sessionData = $result->fetch_assoc();

            if ($sessionData) {
                // Update existing session
                $query = "UPDATE attendance_session 
                         SET unit_id = ?,
                             unit_name = ?,
                             topic_name = ?,
                             description = ?,
                             marked_by = ?,
                             attendance_status = 1
                         WHERE session_id = ?";

                $stmt = $erp_conn->prepare($query);
                $stmt->bind_param(
                    "isssii",
                    $unitId,
                    $unitName,
                    $topicName,
                    $description,
                    $facultyId,
                    $sessionData['session_id']
                );
                $stmt->execute();
                $sessionId = $sessionData['session_id'];

                // Delete existing attendance entries
                $query = "DELETE FROM attendance_entry WHERE session_id = ?";
                $stmt = $erp_conn->prepare($query);
                $stmt->bind_param("i", $sessionId);
                $stmt->execute();
            } else {
                throw new Exception("Session not found for period " . $hour);
            }

            // Insert new attendance entries
            foreach ($attendanceData as $entry) {
                $studentId = getStudentId($erp_conn, $entry['rollNo']);
                $query = "INSERT INTO attendance_entry 
                         (session_id, semester, student_id, attendance_status) 
                         VALUES (?, ?, ?, ?)";
                $stmt = $erp_conn->prepare($query);
                $stmt->bind_param(
                    "iiis",
                    $sessionId,
                    $semester,
                    $studentId,
                    $entry['status']
                );
                $stmt->execute();
            }

            $sessions[] = $sessionId;
        }

        $erp_conn->commit();
        return [
            'status' => 'success',
            'message' => 'Attendance marked for all continuous periods',
            'sessions' => $sessions
        ];
    } catch (Exception $e) {
        $erp_conn->rollback();
        return [
            'status' => 'error',
            'message' => $e->getMessage()

        ];
    }
}

function getStudentLeaveList($erp_conn)
{
    try {
        // Validate input parameters
        if (!isset($_POST['ayear']) || !isset($_POST['semesterType'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
        }

        // Modified query to check for data
        $query = "SELECT lr.*, s.sid as student_roll_no, s.sname as student_name 
                 FROM leave_requests lr
                 JOIN student s ON lr.user_id = s.uid 
                 WHERE lr.user_type = 'Student' 
                 AND lr.status = 'Approved' 
                 AND lr.academicYear = ? 
                 AND lr.semesterType = ? 
                 AND CURRENT_DATE BETWEEN lr.start_date AND lr.end_date";

        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $erp_conn->error);
        }

        $stmt->bind_param("ss", $_POST['ayear'], $_POST['semesterType']);

        // Execute and check for errors
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $leaveList = $result->fetch_all(MYSQLI_ASSOC);






        return [
            'status' => 'success',
            'leaveHistory' => $leaveList  // Array of leave requests
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage(),
            'debug' => [
                'academicYear' => $_POST['academicYear'] ?? 'not set',
                'semesterType' => $_POST['semesterType'] ?? 'not set',
                'error' => $e->getMessage()
            ]
        ];
    }
}
function getStudentLeaveHistory($erp_conn)
{
    if (!isset($_POST['studentId']) || !isset($_POST['batch'])) {
        return json_encode([
            'status' => 'error',
            'message' => 'Missing required parameters'
        ]);
    }


    $academicYear = '2024-2025'; // Modify if dynamic
    $semesterType = 'Even'; // Modify if dynamic
    $query1 = "SELECT * FROM student WHERE sid = ?";
    $stmt1 = $erp_conn->prepare($query1);
    $stmt1->bind_param("s", $_POST['studentId']);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $studentInfo = $result1->fetch_assoc();


    $query = "SELECT * FROM leave_requests WHERE user_id = ? AND academicYear = ? AND semesterType = ?";
    $stmt = $erp_conn->prepare($query);
    if (!$stmt) {
        return json_encode([
            'status' => 'error',
            'message' => 'Database error: Failed to prepare statement.'
        ]);
    }

    $stmt->bind_param("iss", $studentInfo['uid'], $academicYear, $semesterType);
    $stmt->execute();
    $result = $stmt->get_result();
    $leaveHistory = $result->fetch_all(MYSQLI_ASSOC);

    return [
        'status' => 'success',
        'leaveHistory' => $leaveHistory
    ];
}

function deleteLeaveRequest($erp_conn)
{
    try {
        $leaveId = $_POST['leaveId'];

        // First check if the leave request is still pending
        $checkQuery = "SELECT status FROM leave_requests WHERE leave_id = ?";
        $stmt = $erp_conn->prepare($checkQuery);
        $stmt->bind_param("i", $leaveId);
        $stmt->execute();
        $result = $stmt->get_result();
        $leave = $result->fetch_assoc();

        if ($leave['status'] !== 'Pending') {
            throw new Exception('Only pending requests can be deleted');
        }

        // Delete the leave request
        $deleteQuery = "DELETE FROM leave_requests WHERE leave_id = ? AND status = 'Pending'";
        $stmt = $erp_conn->prepare($deleteQuery);
        $stmt->bind_param("i", $leaveId);

        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Leave request deleted successfully'
            ];
        } else {
            throw new Exception('Failed to delete leave request');
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getODRequests($erp_conn)
{
    try {
        $query = "SELECT lr.*, 
                    s.sid as student_roll_no, 
                    s.sname as student_name,
                    CASE 
                        WHEN lr.status != 'Pending' THEN lr.approved_at 
                        ELSE NULL 
                    END as approved_at
                 FROM leave_requests lr 
                 JOIN student s ON lr.user_id = s.uid 
                 WHERE lr.user_type = 'Student' 
                 AND lr.academicYear = ? 
                 AND s.section = ? 
                 AND s.ayear = ? 
                 AND s.dept = ?";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ssss", $_POST['academicYear'], $_POST['section'], $_POST['batch'], $_POST['dept']);
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = $result->fetch_all(MYSQLI_ASSOC);
        return [
            'status' => 'success',
            'requests' => $requests
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getSpecialAttendanceSessions($erp_conn)
{
    try {
        // Validate required parameters
        if (!isset($_POST['date']) || !isset($_POST['advisorData']) || !isset($_POST['periods'])) {
            throw new Exception('Missing required parameters');
        }

        $date = $_POST['date'];
        $advisorData = json_decode($_POST['advisorData'], true);
        $periods = json_decode($_POST['periods'], true);

        // Validate advisor data
        if (!isset($advisorData['batch']) || !isset($advisorData['section']) || !isset($advisorData['semester'])) {
            throw new Exception('Invalid advisor data');
        }

        // Create placeholders for periods
        $periodPlaceholders = str_repeat('?,', count($periods) - 1) . '?';

        // Query to get attendance sessions with their status
        $query = "SELECT t.timetable_id, t.period, c.course_name, f.name as faculty_name, 
                        COALESCE(a.attendance_status, '0') as attendance_status
                 FROM timetable t
                 JOIN course c ON t.course_id = c.course_id
                 JOIN faculty f ON t.faculty_id = f.uid
                 LEFT JOIN attendance_session a ON a.timetable_id = t.timetable_id 
                    AND a.class_date = ?
                 WHERE t.batch = ? 
                 AND t.section = ?
                 AND t.semester = ?
                 AND t.period IN ($periodPlaceholders)";

        $stmt = $erp_conn->prepare($query);

        // Create parameter array for bind_param
        $params = array_merge(
            [$date, $advisorData['batch'], $advisorData['section'], $advisorData['semester']],
            $periods
        );

        // Create types string for bind_param
        $types = 'sssi' . str_repeat('i', count($periods));

        // Add types to start of params array
        array_unshift($params, $types);

        // Bind parameters using reference
        call_user_func_array([$stmt, 'bind_param'], array_map(function (&$param) {
            return $param;
        }, $params));

        $stmt->execute();
        $result = $stmt->get_result();

        $sessions = [];
        while ($row = $result->fetch_assoc()) {
            $sessions[] = $row;
        }

        return [
            'status' => 'success',
            'sessions' => $sessions
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getStudentsForSpecialAttendance($erp_conn)
{
    try {


        $query = "SELECT s.uid, s.sid, s.sname 
                 FROM student s
                 WHERE s.ayear = ? 
                 AND s.section = ? 
                 AND s.dept = ?
                 ORDER BY s.sid";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param(
            "sss",
            $_POST('batch'),  // ❌ Incorrect: should be $_POST['batch']
            $_POST('section'), // ❌ Incorrect: should be $_POST['section']
            $_POST('dept')     // ❌ Incorrect: should be $_POST['dept']
        );


        if (!$stmt->execute()) {
            throw new Exception("Failed to fetch students: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $students = [];

        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }

        return [
            'status' => 'success',
            'students' => $students
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function markSpecialAttendance($erp_conn)
{
    global $erp_conn;

    try {
        $erp_conn->begin_transaction();

        // Parse the form data
        $date = $_POST['formData']['date'];
        $periods = $_POST['formData']['periods'];
        $advisor = $_POST['formData']['advisor'];
        $attendance = $_POST['formData']['attendance'];

        // Get day name from date
        $dayName = date('l', strtotime($date));

        // 1. Get timetable entries
        $placeholders = str_repeat('?,', count($periods) - 1) . '?';
        $timetableQuery = "SELECT * FROM timetable 
            WHERE day = ? 
            AND period IN ($placeholders)
            AND batch = ?
            AND academic_year = ?
            AND semester = ?
            AND section = ?";

        $stmt = $erp_conn->prepare($timetableQuery);

        // Create parameter array
        $params = array_merge(
            [$dayName],
            $periods,
            [
                $advisor['batch'],
                $advisor['academicYear'],
                $advisor['semester'],
                $advisor['section']
            ]
        );

        // Create types string for bind_param
        $types = 's' . str_repeat('i', count($periods)) . 'ssis';

        // Add types to start of params array
        array_unshift($params, $types);

        // Bind parameters using reference
        call_user_func_array([$stmt, 'bind_param'], $params);

        $stmt->execute();
        $result = $stmt->get_result();
        $timetableEntries = [];
        while ($row = $result->fetch_assoc()) {
            $timetableEntries[] = $row;
        }

        // 2. Update attendance sessions for each timetable entry
        foreach ($timetableEntries as $entry) {
            // Update attendance_session status
            $sessionQuery = "UPDATE attendance_session 
                SET attendance_status = 3
                WHERE timetable_id = ? 
                AND class_date = ?
                AND semester = ?";

            $stmt = $erp_conn->prepare($sessionQuery);
            $stmt->bind_param(
                "isi",
                $entry['timetable_id'],
                $date,
                $advisor['semester']
            );
            $stmt->execute();

            // Get session_id for this timetable entry
            $sessionIdQuery = "SELECT session_id FROM attendance_session 
                WHERE timetable_id = ? 
                AND class_date = ?
                AND semester = ?";

            $stmt = $erp_conn->prepare($sessionIdQuery);
            $stmt->bind_param(
                "isi",
                $entry['timetable_id'],
                $date,
                $advisor['semester']
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $sessionId = $result->fetch_assoc()['session_id'];

            if ($sessionId) {
                // 3. Insert attendance entries for each student
                $entryQuery = "INSERT INTO attendance_entry 
                    (session_id, semester, student_id, attendance_status, marked_by) 
                    VALUES (?, ?, ?, ?, 'Advisor')
                    ON DUPLICATE KEY UPDATE 
                    attendance_status = VALUES(attendance_status),
                    marked_by = VALUES(marked_by)";

                $stmt = $erp_conn->prepare($entryQuery);

                foreach ($attendance as $studentAttendance) {
                    $stmt->bind_param(
                        "iiis",
                        $sessionId,
                        $advisor['semester'],
                        $studentAttendance['student_id'],
                        $studentAttendance['attendance_status']
                    );
                    $stmt->execute();
                }
            }
        }

        $erp_conn->commit();
        return [
            'status' => 'success',
            'message' => 'Special attendance marked successfully',
            'timetable_count' => count($timetableEntries)
        ];
    } catch (Exception $e) {
        $erp_conn->rollback();
        return [
            'status' => 'error',
            'message' => 'Failed to mark attendance: ' . $e->getMessage()
        ];
    }
}
function getStudentLeaveListSpecialAttendance($erp_conn)
{
    try {
        // Validate input parameters
        if (!isset($_POST['ayear']) || !isset($_POST['semester'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
        }
        if ($_POST['semester'] % 2 == 0) {
            $semesterType = 'Even';
        } else {
            $semesterType = 'Odd';
        }

        // Modified query to check for data
        $query = "SELECT lr.*, s.sid as student_roll_no, s.sname as student_name 
                 FROM leave_requests lr
                 JOIN student s ON lr.user_id = s.uid 
                 WHERE lr.user_type = 'Student' 
                 AND lr.status = 'Approved' 
                 AND lr.academicYear = ? 
                 AND lr.semesterType = ? 
                 AND CURRENT_DATE BETWEEN lr.start_date AND lr.end_date";

        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $erp_conn->error);
        }

        $stmt->bind_param("ss", $_POST['ayear'], $semesterType);

        // Execute and check for errors
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $leaveList = $result->fetch_all(MYSQLI_ASSOC);






        return [
            'status' => 'success',
            'leaveHistory' => $leaveList  // Array of leave requests
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage(),
            'debug' => [
                'academicYear' => $_POST['academicYear'] ?? 'not set',
                'semesterType' => $_POST['semester'] ?? 'not set',
                'error' => $e->getMessage()
            ]
        ];
    }
}
function checkSpecialAttendanceStatus($erp_conn)
{
    try {
        // Validate required parameters
        if (!isset($_POST['date']) || !isset($_POST['advisorData']) || !isset($_POST['periods'])) {
            throw new Exception('Missing required parameters');
        }

        $date = $_POST['date'];
        $advisorData = json_decode($_POST['advisorData'], true);
        $periods = json_decode($_POST['periods'], true);

        // Validate advisor data
        if (!isset($advisorData['batch']) || !isset($advisorData['section']) || !isset($advisorData['semester'])) {
            throw new Exception('Invalid advisor data');
        }

        // Get day name from date
        $dayName = date('l', strtotime($date));

        // Query to get attendance sessions with their status
        $query = "SELECT t.timetable_id, t.period, c.course_name, f.name as faculty_name, 
                        COALESCE(a.attendance_status, '0') as attendance_status
                 FROM timetable t
                 JOIN course c ON t.course_id = c.course_id
                 JOIN faculty f ON t.faculty_id = f.uid
                 LEFT JOIN attendance_session a ON a.timetable_id = t.timetable_id 
                    AND a.class_date = ?
                 WHERE t.batch = ? 
                 AND t.section = ?
                 AND t.semester = ?
                 AND t.day = ?
                 AND t.period IN (" . implode(',', array_fill(0, count($periods), '?')) . ")";

        $stmt = $erp_conn->prepare($query);

        // Create parameter array
        $params = array_merge(
            [$date, $advisorData['batch'], $advisorData['section'], $advisorData['semester'], $dayName],
            $periods
        );

        // Create types string
        $types = 'sssss' . str_repeat('i', count($periods));

        // Bind parameters
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            throw new Exception("Failed to fetch sessions: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $sessions = [];

        while ($row = $result->fetch_assoc()) {
            $sessions[] = $row;
        }

        return [
            'status' => 'success',
            'sessions' => $sessions
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}
function updateLessonPlan($erp_conn)
{
    try {
        if (!isset($_POST['courseId']) || !isset($_POST['units'])) {
            throw new Exception('Missing required parameters');
        }

        $courseId = intval($_POST['courseId']);
        $units = json_decode($_POST['units'], true);

        if (!is_array($units)) {
            throw new Exception('Invalid units data');
        }

        // Start transaction
        $erp_conn->begin_transaction();

        try {
            // First, delete existing topics for all units of this course
            $stmt = $erp_conn->prepare("DELETE it FROM important_topic it 
                                  INNER JOIN unit u ON it.unit_id = u.unit_id 
                                  WHERE u.course_id = ?");
            $stmt->bind_param("i", $courseId);
            $stmt->execute();

            // Then, delete existing units
            $stmt = $erp_conn->prepare("DELETE FROM unit WHERE course_id = ?");
            $stmt->bind_param("i", $courseId);
            $stmt->execute();

            // Insert new units and their topics
            $stmtUnit = $erp_conn->prepare("INSERT INTO unit (course_id, unit_number, unit_name, CO,co_weightage, co_threshold) VALUES (?, ?, ?, ?,?,?)");
            $stmtTopic = $erp_conn->prepare("INSERT INTO important_topic (unit_id, topic_name) VALUES (?, ?)");

            foreach ($units as $unit) {
                $stmtUnit->bind_param(
                    "iissii",
                    $courseId,
                    $unit['unit_number'],
                    $unit['unit_name'],
                    $unit['CO'],
                    $unit['co_weightage'],
                    $unit['co_threshold']
                );
                $stmtUnit->execute();

                $unitId = $erp_conn->insert_id;

                // Insert topics for this unit
                foreach ($unit['topics'] as $topic) {
                    $stmtTopic->bind_param(
                        "is",
                        $unitId,
                        $topic['name']
                    );
                    $stmtTopic->execute();
                }
            }

            // Commit transaction
            $erp_conn->commit();

            return [
                'status' => 'success',
                'message' => 'Lesson plan updated successfully'
            ];
        } catch (Exception $e) {
            // Rollback on error
            $erp_conn->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}


function markHoliday($erp_conn)
{
    try {
        // Validate required parameters
        if (
            !isset($_POST['date']) || !isset($_POST['description']) ||
            !isset($_POST['academicYear']) || !isset($_POST['batch']) ||
            !isset($_POST['section']) || !isset($_POST['semester'])
        ) {
            throw new Exception('Missing required parameters');
        }

        $date = $_POST['date'];
        $description = $_POST['description'];
        $academicYear = $_POST['academicYear'];
        $batch = $_POST['batch'];
        $section = $_POST['section'];
        $semester = intval($_POST['semester']);

        // Start transaction
        $erp_conn->begin_transaction();

        try {
            // First, update attendance_session status to 2 (holiday)
            // Join with timetable to get the correct sessions
            $query = "UPDATE attendance_session AS a 
            INNER JOIN timetable AS t ON a.timetable_id = t.timetable_id 
            SET a.attendance_status = 2,
                a.description = ?
            WHERE a.class_date = ?
            AND t.batch = ?
            AND t.section = ?
            AND t.semester = ?
            AND t.academic_year = ?";

            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param(
                "ssssss",
                $description,
                $date,
                $batch,
                $section,
                $semester,
                $academicYear
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to update attendance sessions: " . $stmt->error);
            }

            // Check if any rows were affected
            if ($stmt->affected_rows === 0) {
                throw new Exception("No sessions found for the specified criteria");
            }

            // Commit transaction
            $erp_conn->commit();

            return [
                'status' => 'success',
                'message' => 'Holiday marked successfully',
                'affected_sessions' => $stmt->affected_rows
            ];
        } catch (Exception $e) {
            // Rollback on error
            $erp_conn->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getLms($erp_conn)
{
    try {
        if (!isset($_POST['course_id'])) {
            throw new Exception('Course ID is required');
        }

        $course_id = $_POST['course_id'];

        // Query to get units and their topics
        $query = "
            SELECT 
                u.unit_id,
                u.unit_number,
                u.unit_name,
                u.CO,
                t.topic_id,
                t.topic_name,
                t.notes,
                t.video_link
            FROM unit u
            LEFT JOIN important_topic t ON u.unit_id = t.unit_id
            WHERE u.course_id = ?
            ORDER BY u.unit_number, t.topic_id";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $units = [];
        $currentUnit = null;

        while ($row = $result->fetch_assoc()) {
            // If this is a new unit or the first row
            if ($currentUnit === null || $currentUnit['unit_id'] !== $row['unit_id']) {
                // Add the previous unit to the array if it exists
                if ($currentUnit !== null) {
                    $units[] = $currentUnit;
                }

                // Start a new unit
                $currentUnit = [
                    'unit_id' => $row['unit_id'],
                    'unit_number' => $row['unit_number'],
                    'unit_name' => $row['unit_name'],
                    'CO' => $row['CO'],
                    'topics' => []
                ];
            }

            // Add topic if it exists
            if ($row['topic_id']) {
                $currentUnit['topics'][] = [
                    'topic_id' => $row['topic_id'],
                    'topic_name' => $row['topic_name'],
                    'notes' => $row['notes'],
                    'video_link' => $row['video_link']
                ];
            }
        }

        // Add the last unit
        if ($currentUnit !== null) {
            $units[] = $currentUnit;
        }

        // Get total number of units
        $total_units = count($units);

        return [
            'status' => 'success',
            'data' => [
                'total_units' => $total_units,
                'units' => $units
            ]
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}



function saveAllLmsTopics($erp_conn)
{
    header('Content-Type: application/json');

    if (!isset($_POST['topics_data'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No topics data provided']);
        exit;
    }

    $topics = json_decode($_POST['topics_data'], true);
    if (!is_array($topics)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid topics data']);
        exit;
    }

    $uploadDir = 'uploads/';
    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $updatedCount = 0;
    $errorList = [];

    foreach ($topics as $index => $topicData) {
        if (!isset($topicData['topic_id'], $topicData['video_link'], $topicData['unit_id'])) {
            $errorList[] = "Missing topic_id, video_link, or unit_id for topic at index {$index}";
            continue;
        }

        $topic_id   = intval($topicData['topic_id']);
        $unit_id    = intval($topicData['unit_id']);
        $video_link = trim($topicData['video_link']);
        $pdf_path   = null;

        // Process file upload if it exists using the key "pdf_files[topic_id]".
        if (
            isset($_FILES['pdf_files']) &&
            isset($_FILES['pdf_files']['name'][$topic_id]) &&
            $_FILES['pdf_files']['error'][$topic_id] === UPLOAD_ERR_OK
        ) {
            $originalName = $_FILES['pdf_files']['name'][$topic_id];
            $tmpName      = $_FILES['pdf_files']['tmp_name'][$topic_id];
            $extension    = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueName   = time() . "_unit{$unit_id}_topic{$topic_id}." . $extension;
            $destination  = $uploadDir . $uniqueName;

            if (move_uploaded_file($tmpName, $destination)) {
                $pdf_path = $destination;
            } else {
                $errorList[] = "Failed to upload file for topic ID {$topic_id}";
                continue;
            }
        } else {
            $errorList[] = "No file uploaded for topic ID {$topic_id}";
            continue;
        }

        // Update the database with the new video link and PDF file path.
        $stmt = $erp_conn->prepare("UPDATE important_topic SET video_link = ?, notes = ? WHERE unit_id = ? AND topic_id = ?");
        if (!$stmt) {
            $errorList[] = "Database error (prepare) for topic ID {$topic_id}: " . $erp_conn->error;
            continue;
        }

        $stmt->bind_param("ssii", $video_link, $pdf_path, $unit_id, $topic_id);
        if (!$stmt->execute()) {
            $errorList[] = "Database error (execute) for topic ID {$topic_id}: " . $stmt->error;
        } else {
            $updatedCount++;
        }

        $stmt->close();
    }

    // If at least one topic was updated, update the course status to 'Pending'
    if ($updatedCount > 0) {
        // Collect distinct unit_ids from the topics data submitted.
        $unitIds = [];
        foreach ($topics as $topicData) {
            if (isset($topicData['unit_id'])) {
                $unitIds[] = intval($topicData['unit_id']);
            }
        }
        $unitIds = array_unique($unitIds);
        $unitIdsCsv = implode(',', $unitIds);

        // Use a JOIN query to update the course status based on the unit's course_id.
        // Assumes the `unit` table contains columns `unit_id` and `course_id`.
        $sql = "UPDATE course c
                JOIN unit u ON c.course_id = u.course_id 
                SET c.status = 'Pending'
                WHERE u.unit_id IN ($unitIdsCsv)";
        $erp_conn->query($sql);

        echo json_encode([
            'status'  => 'success',
            'message' => "{$updatedCount} topic(s) updated successfully.",
            'errors'  => $errorList
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'message' => 'No topics were updated.',
            'errors'  => $errorList
        ]);
    }
}



function getLmsCourses($erp_conn)
{
    session_start();
    // Use $_REQUEST to support both GET and POST methods.
    $dept = isset($_REQUEST['dept']) ? $erp_conn->real_escape_string($_REQUEST['dept']) : '';
    if (empty($dept)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Department not provided'
        ]);
        exit;
    }

    $sql = "
        SELECT
            c.course_id,
            c.course_code,
            c.course_name,
            c.course_credit,
            c.course_type,
            c.dept,
            c.ayear,
            c.semester,
            c.status,
            c.reason,
            COALESCE(NULLIF(GROUP_CONCAT(DISTINCT f.name SEPARATOR ', '), ''), 'Not Assigned') AS staff_name
        FROM course c
        LEFT JOIN course_faculty cf ON c.course_id = cf.course_id
        LEFT JOIN faculty f ON cf.faculty_id = f.uid
        WHERE c.status = 'Pending'
          AND c.dept = '$dept'
        GROUP BY c.course_id
    ";

    $result = $erp_conn->query($sql);
    $courses = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Database query failed: ' . $erp_conn->error
        ]);
        exit;
    }
    // get the courses that are approved
    $approvedCourses = "
    SELECT
            c.course_id,
            c.course_code,
            c.course_name,
            c.course_credit,
            c.course_type,
            c.dept,
            c.ayear,
            c.semester,
            c.status,
            c.reason,
            COALESCE(NULLIF(GROUP_CONCAT(DISTINCT f.name SEPARATOR ', '), ''), 'Not Assigned') AS staff_name
        FROM course c
        LEFT JOIN course_faculty cf ON c.course_id = cf.course_id
        LEFT JOIN faculty f ON cf.faculty_id = f.uid
        WHERE c.status = 'Approved'
          AND c.dept = '$dept'
        GROUP BY c.course_id";
    $approvedResult = $erp_conn->query($approvedCourses);
    $approvedCourses = [];
    while ($row = $approvedResult->fetch_assoc()) {
        $approvedCourses[] = $row;
    }

    // send the data in the above format
    echo json_encode([
        'status'  => 'success',
        'data'    => [
            'approvals' => $courses,
            'available' => $approvedCourses
        ]
    ]);
}

function saveDayOrderOverride($erp_conn)
{
    try {
        // Validate required fields
        $requiredFields = ['override_date', 'assigned_day', 'batch', 'academic_year', 'semester', 'section'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Validate date is a Saturday
        $date = new DateTime($_POST['override_date']);
        if ($date->format('N') !== '6') {
            throw new Exception("Selected date must be a Saturday");
        }

        // Prepare query
        $query = "INSERT INTO day_order_override 
                 (override_date, batch, academic_year, semester, section, assigned_day) 
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE 
                 assigned_day = VALUES(assigned_day)";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param(
            "sssiss",
            $_POST['override_date'],
            $_POST['batch'],
            $_POST['academic_year'],
            $_POST['semester'],
            $_POST['section'],
            $_POST['assigned_day']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to save day order override: " . $stmt->error);
        }

        return [
            'status' => 'success',
            'message' => 'Day order override saved successfully'
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function createDayOrderAttendanceSessions($erp_conn, $overrideDate, $batch, $semester, $section, $academicYear, $assignedDay)
{
    try {
        $erp_conn->begin_transaction();

        // Get timetable entries for the assigned day
        $query = "
            SELECT t.*, c.course_name 
            FROM timetable t
            JOIN course c ON t.course_id = c.course_id
            WHERE t.day = ? 
            AND t.batch = ? 
            AND t.semester = ?
            AND t.section = ?
            AND t.academic_year = ?
            AND t.is_active = 1
        ";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ssiss", $assignedDay, $batch, $semester, $section, $academicYear);

        if (!$stmt->execute()) {
            throw new Exception("Failed to fetch timetable entries: " . $stmt->error);
        }

        $result = $stmt->get_result();

        // Insert attendance sessions for each period
        $insertQuery = "
            INSERT INTO attendance_session 
            (timetable_id, class_date, semester, attendance_status)
            VALUES (?, ?, ?, 0)
            ON DUPLICATE KEY UPDATE attendance_status = attendance_status
        ";
        $insertStmt = $erp_conn->prepare($insertQuery);

        $sessionCount = 0;
        while ($row = $result->fetch_assoc()) {
            $insertStmt->bind_param(
                "isi",
                $row['timetable_id'],
                $overrideDate,
                $semester
            );
            $insertStmt->execute();
            $sessionCount++;
        }

        $erp_conn->commit();
        return [
            'status' => 'success',
            'message' => "Created $sessionCount attendance sessions for day order",
            'sessions_created' => $sessionCount
        ];
    } catch (Exception $e) {
        $erp_conn->rollback();
        return [
            'status' => 'error',
            'message' => 'Failed to create attendance sessions: ' . $e->getMessage()
        ];
    }
}

function approveRequest($erp_conn)
{
    $leave_id = $_POST['leave_id'];

    try {
        $query = "UPDATE leave_requests SET status = 'Approved' WHERE leave_id = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("i", $leave_id);
        $stmt->execute();
        return [
            'status' => 'success',
            'message' => 'Request approved successfully.'
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function rejectRequest($erp_conn)
{
    $leave_id = $_POST['leave_id'];
    try {
        $query = "UPDATE leave_requests SET status = 'Rejected'  WHERE leave_id = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("i",  $leave_id);
        $stmt->execute();
        return [
            'status' => 'success',
            'message' => 'Request rejected successfully.'
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getFacultyMarkingStatus($erp_conn)
{
    try {
        // Validate required parameters
        if (
            !isset($_POST['date']) || !isset($_POST['batch']) ||
            !isset($_POST['semester']) || !isset($_POST['section'])
        ) {
            throw new Exception('Missing required parameters');
        }

        $date = $_POST['date'];
        $batch = $_POST['batch'];
        $semester = $_POST['semester'];
        $section = $_POST['section'];



        // Modified query: now returning the raw attendance_status as session_status.
        $periodsQuery = "
            SELECT DISTINCT 
                t.period,
                f.name as faculty_name,
                c.course_name,
                a.session_id,
                a.attendance_status as session_status
            FROM attendance_session a
            JOIN timetable t ON a.timetable_id = t.timetable_id
            JOIN faculty f ON t.faculty_id = f.uid
            JOIN course c ON t.course_id = c.course_id
            WHERE a.class_date = ?
              AND t.batch = ?
              AND t.semester = ?
              AND t.section = ?
            ORDER BY t.period";

        $stmt = $erp_conn->prepare($periodsQuery);
        $stmt->bind_param('ssis', $date, $batch, $semester, $section);
        $stmt->execute();
        $periodsResult = $stmt->get_result();
        $periods = [];

        while ($row = $periodsResult->fetch_assoc()) {
            // Fetch the attendance summary only if the session status is Marked (1) or Special Attendance (3)
            $summary = null;
            if ($row['session_status'] == 1 || $row['session_status'] == 3) {
                $summaryQuery = "SELECT 
                    SUM(CASE WHEN attendance_status = 3 THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN attendance_status = 0 THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN attendance_status = 2 THEN 1 ELSE 0 END) as od_count,
                    SUM(CASE WHEN attendance_status = 1 THEN 1 ELSE 0 END) as leave_count
                FROM attendance_entry WHERE session_id = ?";

                $summaryStmt = $erp_conn->prepare($summaryQuery);
                $summaryStmt->bind_param('i', $row['session_id']);
                $summaryStmt->execute();
                $summary = $summaryStmt->get_result()->fetch_assoc();
            }

            $periods[] = [
                'period'        => $row['period'],
                'faculty_name'  => $row['faculty_name'],
                'course_name'   => $row['course_name'],
                'session_status' => $row['session_status'],
                'summary'       => $summary
            ];
        }

        // Get total student count for pagination
        $countQuery = "
            SELECT COUNT(DISTINCT ae.student_id) as total 
            FROM attendance_entry ae
            JOIN attendance_session a ON ae.session_id = a.session_id
            JOIN timetable t ON a.timetable_id = t.timetable_id
            WHERE t.batch = ? 
              AND t.section = ?
              AND a.class_date = ?";
        $stmt = $erp_conn->prepare($countQuery);
        $stmt->bind_param('sss', $batch, $section, $date);
        $stmt->execute();
        $totalStudents = $stmt->get_result()->fetch_assoc()['total'];

        // Get students with attendance data
        $studentsQuery = "
            SELECT 
                s.uid,
                s.sid as roll_no,
                s.sname,
                GROUP_CONCAT(
                    CONCAT(t.period, ':', 
                        CASE 
                            WHEN ae.attendance_status = 0 THEN 'A'
                            WHEN ae.attendance_status = 1 THEN 'L'
                            WHEN ae.attendance_status = 2 THEN 'OD'
                            WHEN ae.attendance_status = 3 THEN 'P'
                            ELSE 'NA'
                        END
                    ) ORDER BY t.period
                ) as attendance_data
            FROM attendance_entry ae
            JOIN attendance_session a ON ae.session_id = a.session_id
            JOIN timetable t ON a.timetable_id = t.timetable_id
            JOIN student s ON ae.student_id = s.uid
            WHERE t.batch = ? 
              AND t.section = ?
              AND a.class_date = ?
            GROUP BY s.uid, s.sid, s.sname
            ORDER BY s.sid
            ";
        $stmt = $erp_conn->prepare($studentsQuery);
        $stmt->bind_param('sss', $batch, $section, $date);
        $stmt->execute();
        $studentsResult = $stmt->get_result();

        $students = [];
        while ($student = $studentsResult->fetch_assoc()) {
            // Process attendance data string into period-wise array
            $attendanceMap = [];
            if ($student['attendance_data']) {
                foreach (explode(',', $student['attendance_data']) as $data) {
                    list($period, $status) = explode(':', $data);
                    $attendanceMap[$period] = $status;
                }
            }

            // Ensure all periods have a status
            $student['hours'] = [];
            for ($i = 1; $i <= 8; $i++) {
                $student['hours'][$i] = $attendanceMap[$i] ?? 'NA';
            }
            unset($student['attendance_data']);
            $students[] = $student;
        }

        return [
            'status'   => 'success',
            'periods'  => $periods,
            'students' => [
                'data' => $students
            ]
        ];
    } catch (Exception $e) {
        return [
            'status'  => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getStudentAttendanceSummary($erp_conn)
{
    try {
        // Get parameters from POST
        $batch = $_POST['batch'];
        $semester = $_POST['semester'];
        $section = $_POST['section'];
        $academicYear = $_POST['academicYear'];

        // Main query:
        // - Total Hours: count distinct attendance_session.session_id
        //   when the session's status is one of (0, 1, 3, 4).
        // - Present Hours: count attendance_entry records where the
        //   attendance_status is in (2, 3).
        // - Attendance Percentage is computed based on these values.
        $query = "
            SELECT 
                s.sid as roll_no,
                s.sname as student_name,
                COUNT(DISTINCT CASE WHEN a.attendance_status IN (0,1,3,4) THEN a.session_id END) as total_hours,
                SUM(CASE WHEN ae.attendance_status IN (2,3) THEN 1 ELSE 0 END) as present_hours,
                ROUND(
                    (SUM(CASE WHEN ae.attendance_status IN (2,3) THEN 1 ELSE 0 END) * 100.0 / 
                     NULLIF(COUNT(DISTINCT CASE WHEN a.attendance_status IN (0,1,3,4) THEN a.session_id END), 0)
                    ), 2
                ) as attendance_percentage
            FROM student s
            LEFT JOIN attendance_entry ae ON s.uid = ae.student_id
            LEFT JOIN attendance_session a ON ae.session_id = a.session_id
            LEFT JOIN timetable t ON a.timetable_id = t.timetable_id
            WHERE t.batch = ?
              AND t.semester = ?
              AND t.section = ?
              AND t.academic_year = ?
              AND a.attendance_status IN (0,1,3,4)
            GROUP BY s.uid, s.sid, s.sname
            ORDER BY s.sid
           
        ";

        // Count query for pagination:
        // Only include students who have a session with status in (0,1,3,4)
        $countQuery = "
            SELECT COUNT(DISTINCT s.uid) as total
            FROM student s
            JOIN attendance_entry ae ON s.uid = ae.student_id
            JOIN attendance_session a ON ae.session_id = a.session_id
            JOIN timetable t ON a.timetable_id = t.timetable_id
            WHERE t.batch = ?
              AND t.semester = ?
              AND t.section = ?
              AND t.academic_year = ?
              AND a.attendance_status IN (0,1,3,4)
        ";

        // Execute count query
        $stmt = $erp_conn->prepare($countQuery);
        $stmt->bind_param('ssss', $batch, $semester, $section, $academicYear);
        $stmt->execute();
        $totalStudents = $stmt->get_result()->fetch_assoc()['total'];

        // Execute main query
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('ssss', $batch, $semester, $section, $academicYear);
        $stmt->execute();
        $result = $stmt->get_result();

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'roll_no'             => $row['roll_no'],
                'student_name'        => $row['student_name'],
                'total_hours'         => (int)$row['total_hours'],
                'present_hours'       => (int)$row['present_hours'],
                'attendance_percentage' => (float)$row['attendance_percentage']
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                'students' => $students,

            ]
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}
function ApproveCourse($erp_conn)
{
    $courseId = $_POST['courseId'];
    $query = "UPDATE course SET status = 'Approved' WHERE course_id = ?";
    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $erp_conn->close();

    // Add return statement
    return [
        'status' => 'success',
        'message' => 'Course approved successfully'
    ];
}
function updateStatus($erp_conn)
{
    $courseId = $_POST['courseId'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];
    $query = "UPDATE course SET status = 'Pending', reason = '$reason' WHERE course_id = $courseId";
    $erp_conn->query($query);
    return [
        'status' => 'success',
        'message' => 'Status updated successfully'
    ];
}
function editLmsTopic($erp_conn)
{
    header('Content-Type: application/json');

    // Validate required fields (unit_id is optional)
    if (!isset($_POST['topic_id'], $_POST['video_link'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
        exit;
    }

    $topic_id   = intval($_POST['topic_id']);
    // Use unit_id if provided; otherwise fallback to 0.
    $unit_id    = isset($_POST['unit_id']) ? intval($_POST['unit_id']) : 0;
    $video_link = trim($_POST['video_link']);

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $pdf_path = null;
    $updatePdf = false;

    // Check if a new PDF file was uploaded.
    if (isset($_FILES['pdf'])) {
        if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $originalName = $_FILES['pdf']['name'];
            $tmpName      = $_FILES['pdf']['tmp_name'];
            $extension    = pathinfo($originalName, PATHINFO_EXTENSION);
            // Use unit_id if available in file name, otherwise use topic_id.
            $uniqueName   = time() . "_unit{$unit_id}_topic{$topic_id}." . $extension;
            $destination  = $uploadDir . $uniqueName;
            if (move_uploaded_file($tmpName, $destination)) {
                $pdf_path = $destination;
                $updatePdf = true;
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => "Failed to upload file for topic ID {$topic_id}"]);
                exit;
            }
        }
        // If no file uploaded or error occurs, we assume the PDF should remain unchanged.
    }

    // Update the database.
    if ($updatePdf) {
        // Update both the video link and the PDF path.
        $stmt = $erp_conn->prepare("UPDATE important_topic SET video_link = ?, notes = ? WHERE topic_id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $erp_conn->error]);
            exit;
        }
        $stmt->bind_param("ssi", $video_link, $pdf_path, $topic_id);
    } else {
        // Update only the video link.
        $stmt = $erp_conn->prepare("UPDATE important_topic SET video_link = ? WHERE topic_id = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $erp_conn->error]);
            exit;
        }
        $stmt->bind_param("si", $video_link, $topic_id);
    }

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        exit;
    }

    echo json_encode(['status' => 'success', 'message' => "Topic updated successfully."]);
}

function getCourseStudents($erp_conn)
{
    // Check for courseId in POST
    if (!isset($_POST['courseId'])) {
        return [
            'status' => 'error',
            'message' => 'Missing course ID'
        ];
    }
    $courseId = (int)$_POST['courseId'];

    // Retrieve faculty uid from session (always use session for security)
    if (!isset($_SESSION['user']['uid'])) {
        return [
            'status' => 'error',
            'message' => 'User not logged in'
        ];
    }
    $facultyUid = (int)$_SESSION['user']['uid'];

    // Query to join course_faculty and student to find the assigned students
    $query = "
        SELECT s.uid as student_id, s.sid, s.sname, s.ayear 
        FROM student s
        JOIN course_faculty cf ON s.uid = cf.student_id
        WHERE cf.course_id = ? AND cf.faculty_id = ?
    ";

    $stmt = $erp_conn->prepare($query);
    if (!$stmt) {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $erp_conn->error,
        ];
    }

    $stmt->bind_param("ii", $courseId, $facultyUid);

    if (!$stmt->execute()) {
        return [
            'status' => 'error',
            'message' => 'Execution error: ' . $stmt->error,
        ];
    }

    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        // Use the alias 'student_id' returned by the SQL query
        $students[] = [
            'uid'             => $row['student_id'],
            'register_number' => $row['sid'],
            'name'            => $row['sname'],
            'batch'           => $row['ayear']
        ];
    }

    return [
        'status'   => 'success',
        'students' => $students
    ];
}

function getAttendanceSummary($erp_conn)
{
    try {
        // Validate required parameters
        if (!isset($_POST['courseId'])) {
            throw new Exception('Course ID is required');
        }

        $courseId = (int)$_POST['courseId'];
        $facultyId = (int)$_SESSION['user']['uid'];



        // Updated count query to count rows from the grouped subquery.
        $countQuery = "
            SELECT COUNT(*) as total FROM (
                SELECT t.timetable_id, a.session_id
                FROM timetable t
                LEFT JOIN attendance_session a ON t.timetable_id = a.timetable_id
                WHERE t.course_id = ? AND t.faculty_id = ?
                GROUP BY t.timetable_id, a.session_id
            ) AS sub
        ";
        $stmt = $erp_conn->prepare($countQuery);
        $stmt->bind_param('ii', $courseId, $facultyId);
        $stmt->execute();
        $countResult = $stmt->get_result();
        $totalSessions = $countResult->fetch_assoc()['total'];

        // Main query to get attendance summary
        $query = "
            SELECT 
                t.period,
                a.class_date,
                DAYNAME(a.class_date) as day,
                a.attendance_status as session_status,
                a.unit_name,
                a.topic_name,
                a.description,
                (
                    SELECT COUNT(*) 
                    FROM course_faculty cf 
                    WHERE cf.course_id = t.course_id
                ) as total_students,
                CASE 
                    WHEN a.attendance_status IN (1,3) THEN 
                        CONCAT(
                            COUNT(CASE WHEN ae.attendance_status = 1 THEN 1 END),
                            ' - ',
                            GROUP_CONCAT(CASE WHEN ae.attendance_status = 1 THEN s.sid END)
                        )
                    ELSE 'Not Marked'
                END as leave_details,
                CASE 
                    WHEN a.attendance_status IN (1,3) THEN 
                        CONCAT(
                            COUNT(CASE WHEN ae.attendance_status = 0 THEN 1 END),
                            ' - ',
                            GROUP_CONCAT(CASE WHEN ae.attendance_status = 0 THEN s.sid END)
                        )
                    ELSE 'Not Marked'
                END as absent_details,
                CASE 
                    WHEN a.attendance_status IN (1,3) THEN 
                        CONCAT(
                            COUNT(CASE WHEN ae.attendance_status = 2 THEN 1 END),
                            ' - ',
                            GROUP_CONCAT(CASE WHEN ae.attendance_status = 2 THEN s.sid END)
                        )
                    ELSE 'Not Marked'
                END as od_details
            FROM timetable t
            LEFT JOIN attendance_session a ON t.timetable_id = a.timetable_id
            LEFT JOIN attendance_entry ae ON a.session_id = ae.session_id
            LEFT JOIN student s ON ae.student_id = s.uid
            WHERE t.course_id = ? AND t.faculty_id = ?
            GROUP BY t.timetable_id, a.session_id
            ORDER BY a.class_date DESC, t.period
            
        ";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('ii', $courseId, $facultyId);
        $stmt->execute();
        $result = $stmt->get_result();

        $attendanceSummary = [];
        while ($row = $result->fetch_assoc()) {
            // Format description
            $description = 'No description';
            if (!empty($row['unit_name']) || !empty($row['topic_name']) || !empty($row['description'])) {
                $topicsPart = '';
                if (!empty($row['unit_name']) || !empty($row['topic_name'])) {
                    $topicsPart = 'Topics Covered: ' .
                        ($row['unit_name'] ?? '') .
                        (!empty($row['unit_name']) && !empty($row['topic_name']) ? ' - ' : '') .
                        ($row['topic_name'] ?? '');
                }

                $deliveryPart = '';
                if (!empty($row['description'])) {
                    $deliveryPart = 'Mode of Delivery - ' . $row['description'];
                }

                $description = implode(', ', array_filter([$topicsPart, $deliveryPart]));
            }

            $attendanceSummary[] = [
                'class_date'      => $row['class_date'],
                'day'             => $row['day'],
                'hour'            => $row['period'],
                'leave'           => $row['leave_details'] ?: '0',
                'absent'          => $row['absent_details'] ?: '0',
                'od'              => $row['od_details'] ?: '0',
                'description'     => $description,
                'total_students'  => $row['total_students']
            ];
        }

        return [
            'status' => 'success',
            'attendanceSummary' => $attendanceSummary
        ];
    } catch (Exception $e) {
        return [
            'status'  => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getAttendancePercentage($erp_conn)
{
    try {
        if (!isset($_POST['courseId'])) {
            throw new Exception('Course ID is required');
        }

        $courseId = (int)$_POST['courseId'];
        $facultyId = (int)$_SESSION['user']['uid'];

        $query = "
            SELECT 
                s.uid,
                s.sid as register_number,
                s.sname as student_name,
                COUNT(DISTINCT CASE 
                    WHEN as2.attendance_status IN (0,1,3,4) 
                    THEN as2.session_id 
                END) as total_hours,
                COUNT(DISTINCT CASE 
                    WHEN ae.attendance_status IN (2,3) 
                    THEN ae.session_id 
                END) as present_hours,
                CASE 
                    WHEN COUNT(DISTINCT CASE WHEN as2.attendance_status IN (0,1,3,4) THEN as2.session_id END) > 0 
                    THEN ROUND((COUNT(DISTINCT CASE WHEN ae.attendance_status IN (2,3) THEN ae.session_id END) * 100.0) / 
                         COUNT(DISTINCT CASE WHEN as2.attendance_status IN (0,1,3,4) THEN as2.session_id END), 2)
                    ELSE 0 
                END as attendance_percentage
            FROM course_faculty cf
            JOIN student s ON cf.student_id = s.uid
            JOIN timetable t ON t.course_id = cf.course_id AND t.faculty_id = cf.faculty_id
            LEFT JOIN attendance_session as2 ON t.timetable_id = as2.timetable_id
            LEFT JOIN attendance_entry ae ON ae.session_id = as2.session_id AND ae.student_id = s.uid
            WHERE cf.course_id = ? AND cf.faculty_id = ?
            GROUP BY s.uid, s.sid, s.sname
            ORDER BY s.sid";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('ii', $courseId, $facultyId);
        $stmt->execute();
        $result = $stmt->get_result();

        $attendanceData = [];
        while ($row = $result->fetch_assoc()) {
            $attendanceData[] = [
                'uid' => $row['uid'],
                'register_number' => $row['register_number'],
                'student_name' => $row['student_name'],
                'total_hours' => $row['total_hours'],
                'present_hours' => $row['present_hours'],
                'attendance_percentage' => $row['attendance_percentage']
            ];
        }

        return [
            'status' => 'success',
            'data' => $attendanceData
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getCourseDetails($erp_conn)
{
    try {
        if (!isset($_POST['courseId'])) {
            throw new Exception('Course ID is required');
        }

        $courseId = $_POST['courseId'];

        $query = "SELECT course_type FROM course WHERE course_id = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('i', $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        $courseDetails = $result->fetch_assoc();

        if (!$courseDetails) {
            throw new Exception('Course not found');
        }

        return [
            'status' => 'success',
            'data' => $courseDetails
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}
function requestTimeTableEdit($erp_conn, $batch, $semester, $section, $academicYear, $id, $status)
{
    $faculty_uid = $_SESSION['user']['uid'];
    $query = "UPDATE class_advisors 
              SET timetable_edit_status = ? 
              WHERE batch = ? 
                AND semester = ? 
                AND section = ? 
                AND academic_year = ? 
                AND faculty_id = ?";
    $stmt = $erp_conn->prepare($query);
    if (!$stmt) {
        return [
            'status'  => 'error',
            'message' => 'Failed to prepare update query: ' . $erp_conn->error
        ];
    }
    $stmt->bind_param('ssissi', $status, $batch, $semester, $section, $academicYear, $faculty_uid);
    if (!$stmt->execute()) {
        return [
            'status'  => 'error',
            'message' => 'Update query failed: ' . $stmt->error
        ];
    }
    if ($stmt->affected_rows > 0) {
        return [
            'status'  => 'success',
            'message' => 'Time Table Edit Request Sent Successfully'
        ];
    }
    return [
        'status'  => 'error',
        'message' => 'Failed to send Time Table Edit Request'
    ];
}


function requestLessonPlanEdit($erp_conn, $courseId, $status)
{
    $query = "UPDATE course SET lessonplan_edit_status = ? WHERE course_id = ?";
    $stmt = $erp_conn->prepare($query);
    $stmt->bind_param('si', $status, $courseId);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        return [
            'status'  => 'success',
            'message' => 'Lesson Plan Edit Request Sent Successfully'
        ];
    }
    return [
        'status'  => 'error',
        'message' => 'Failed to send Lesson Plan Edit Request'
    ];
}
function fetchCourseDetails($erp_conn)
{
    try {
        if (!isset($_POST['course_id'])) {
            throw new Exception('Course ID is required');
        }

        $courseId = $_POST['course_id'];

        $query = "SELECT cf.batch, cf.section, c.ayear AS academic_year, c.semester,
                         c.dept AS department, c.course_name, c.course_code 
                  FROM course c
                  JOIN course_faculty cf ON c.course_id = cf.course_id
                  WHERE c.course_id = ?
                  LIMIT 1";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return [
                'status' => 'success',
                'data' => [
                    'academic_year' => $row['academic_year'],
                    'batch' => $row['batch'],
                    'section' => $row['section'],
                    'semester' => $row['semester'],
                    'department' => $row['department'],
                    'course_name' => $row['course_name'],
                    'course_code' => $row['course_code']
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'No data found for the given course ID'
            ];
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function getComponentCOData($erp_conn)
{
    if (!isset($_POST['courseId'])) {
        return ['status' => 'error', 'message' => 'Course ID is required'];
    }

    $courseId = (int)$_POST['courseId'];

    // First get components and their CO mapping
    $componentsQuery = "
        SELECT 
            ec.component_id,
            ec.component_name,
            qt.co_number,
            SUM(qt.marks) as max_marks  -- Changed to SUM marks for each CO
        FROM exam_components ec
        JOIN question_templates qt ON ec.component_id = qt.component_id
        WHERE ec.course_id = ? 
        AND ec.status != 'draft' AND ec.is_cqi = 0
        GROUP BY ec.component_id, ec.component_name, qt.co_number
        ORDER BY ec.component_id, qt.co_number";

    // Get student marks with details - corrected column names
    $marksQuery = "
        SELECT 
            s.uid as student_id,
            s.sname as student_name,
            s.sid as register_number,
            m.component_id,
            ec.component_name,
            qt.co_number,
            SUM(m.marks_obtained) as obtained_marks,
            m.is_absent
        FROM marks m
        JOIN question_templates qt ON m.component_id = qt.component_id 
            AND m.question_number = qt.question_number
        JOIN exam_components ec ON m.component_id = ec.component_id
        JOIN student s ON m.student_id = s.uid
        WHERE ec.course_id = ? 
        AND ec.status != 'draft' AND ec.is_cqi = 0
        GROUP BY s.uid, m.component_id, qt.co_number
        ORDER BY s.sid, ec.component_id, qt.co_number";

    try {
        $stmt = $erp_conn->prepare($componentsQuery);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $componentsResult = $stmt->get_result();

        $components = [];
        $totalCOMarks = [];

        while ($row = $componentsResult->fetch_assoc()) {
            $compName = $row['component_name'];
            $coNumber = 'CO' . $row['co_number'];

            if (!isset($components[$compName])) {
                $components[$compName] = [];
            }
            $components[$compName][$coNumber] = $row['max_marks'];

            if (!isset($totalCOMarks[$coNumber])) {
                $totalCOMarks[$coNumber] = 0;
            }
            $totalCOMarks[$coNumber] += (float)$row['max_marks'];
        }

        // Get student marks with details
        $stmt = $erp_conn->prepare($marksQuery);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $marksResult = $stmt->get_result();

        $studentData = [];

        while ($row = $marksResult->fetch_assoc()) {
            $studentId = $row['student_id'];
            $compName = $row['component_name'];
            $coNumber = 'CO' . $row['co_number'];

            if (!isset($studentData[$studentId])) {
                $studentData[$studentId] = [
                    'student_id' => $studentId,
                    'name' => $row['student_name'],
                    'register_number' => $row['register_number'],
                    'marks' => []
                ];
            }

            if (!isset($studentData[$studentId]['marks'][$compName])) {
                $studentData[$studentId]['marks'][$compName] = [];
            }

            $studentData[$studentId]['marks'][$compName][$coNumber] = [
                'marks' => $row['obtained_marks'],
                'is_absent' => (bool)$row['is_absent']
            ];
        }

        return [
            'status' => 'success',
            'components' => $components,
            'totalCOMarks' => $totalCOMarks,
            'students' => array_values($studentData)
        ];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

function generateRecommendation($gaps)
{
    $recommendations = [];
    foreach ($gaps as $co => $gap) {
        if ($gap <= 10) {
            $recommendations[] = "CO$co: Minor improvement needed (~${gap}% to target)";
        } else if ($gap <= 20) {
            $recommendations[] = "CO$co: Moderate revision required (~${gap}% to target)";
        } else {
            $recommendations[] = "CO$co: Significant improvement needed (~${gap}% to target)";
        }
    }
    return implode('<br>', $recommendations);
}

function getCQIAnalysis($erp_conn)
{
    $courseId = $_POST['courseId'];
    $facultyId = $_SESSION['user']['uid'];

    // Get total marks for each CO
    $coTotalMarksQuery = "
        SELECT qt.co_number, SUM(qt.marks) as total_marks
        FROM question_templates qt 
        JOIN exam_components ec ON qt.component_id = ec.component_id 
        WHERE ec.course_id = ? AND ec.faculty_id = ?
        AND qt.is_cqi = 0
        GROUP BY qt.co_number
        ORDER BY qt.co_number";

    // Get detailed student performance data
    $studentMarksQuery = "
        SELECT s.uid, s.sid, s.sname as name,
               qt.co_number,
               SUM(CASE WHEN m.is_absent = 0 THEN m.marks_obtained ELSE 0 END) as obtained_marks,
               SUM(qt.marks) as total_marks
        FROM student s
        JOIN marks m ON s.uid = m.student_id
        JOIN exam_components ec ON m.component_id = ec.component_id
        JOIN question_templates qt ON m.component_id = qt.component_id 
             AND m.question_number = qt.question_number
        WHERE ec.course_id = ? AND ec.faculty_id = ?
        AND qt.is_cqi = 0
        GROUP BY s.uid, s.sid, s.sname, qt.co_number";

    try {
        // Execute CO total marks query
        $coStmt = $erp_conn->prepare($coTotalMarksQuery);
        $coStmt->bind_param("ii", $courseId, $facultyId);  // Add bind_param
        $coStmt->execute();
        $coResult = $coStmt->get_result();  // Get result set
        $coTotalMarks = [];
        while ($row = $coResult->fetch_assoc()) {  // Use result set to fetch
            $coTotalMarks[$row['co_number']] = $row['total_marks'];
        }

        // Execute student marks query
        $studentStmt = $erp_conn->prepare($studentMarksQuery);
        $studentStmt->bind_param("ii", $courseId, $facultyId);  // Add bind_param
        $studentStmt->execute();
        $studentResult = $studentStmt->get_result();  // Get result set
        $studentResults = [];
        while ($row = $studentResult->fetch_assoc()) {  // Use fetch_assoc instead of fetch_all
            $studentResults[] = $row;
        }

        // Process student data
        $studentData = [];
        foreach ($studentResults as $row) {
            if (!isset($studentData[$row['uid']])) {
                $studentData[$row['uid']] = [
                    'uid' => $row['uid'],
                    'sid' => $row['sid'],
                    'name' => $row['name'],
                    'marks' => []
                ];
            }
            $studentData[$row['uid']]['marks'][$row['co_number']] = [
                'obtained' => $row['obtained_marks'],
                'total' => $row['total_marks']
            ];
        }

        $students = [];
        $coStats = array_fill_keys(array_keys($coTotalMarks), ['below_target' => 0, 'total' => 0]);
        $totalStudents = count($studentData);
        $belowTargetCount = 0;
        $allScores = []; // For debugging

        foreach ($studentData as $uid => $data) {
            $unattainedCOs = [];
            $currentScores = [];
            $requiredScores = [];
            $gaps = [];
            $studentBelowTarget = false;

            foreach ($coTotalMarks as $co => $totalMarks) {
                if (!isset($data['marks'][$co])) {
                    continue;
                }

                $marks = $data['marks'][$co];
                $percentage = ($marks['obtained'] / $marks['total']) * 100;
                $currentScores[$co] = round($percentage, 2);

                if ($percentage < 58) {
                    $studentBelowTarget = true;
                    $unattainedCOs[] = $co;
                    $requiredScores[$co] = 58;
                    $gaps[$co] = round(58 - $percentage, 2);
                    $coStats[$co]['below_target']++;
                }
                $coStats[$co]['total']++;
            }

            // Track all scores for debugging
            $allScores[$data['sid']] = [
                'name' => $data['name'],
                'scores' => $currentScores
            ];

            if ($studentBelowTarget) {
                $belowTargetCount++;
                $students[] = [
                    'uid' => $data['uid'],
                    'sid' => $data['sid'],
                    'name' => $data['name'],
                    'unattained_cos' => $unattainedCOs,
                    'current_scores' => $currentScores,
                    'required_scores' => $requiredScores,
                    'gaps' => $gaps,
                    'recommendation' => generateRecommendation($gaps)
                ];
            }
        }

        // Find most affected CO
        $mostAffectedCO = null;
        $highestFailureRate = 0;
        foreach ($coStats as $co => $stats) {
            $failureRate = ($stats['total'] > 0) ?
                ($stats['below_target'] / $stats['total']) * 100 : 0;
            if ($failureRate > $highestFailureRate) {
                $highestFailureRate = $failureRate;
                $mostAffectedCO = $co;
            }
        }
        // if both total students and below target count are 0 then target achievement is 0
        if ($totalStudents == 0 && $belowTargetCount == 0) {
            $targetAchievement = 0;
        } else {
            $targetAchievement = round(
                (($totalStudents - $belowTargetCount) / $totalStudents) * 100,
                2
            );
        }

        return [
            'status' => 'success',
            'data' => [
                'total_students' => $totalStudents,
                'below_target_count' => $belowTargetCount,
                'most_affected_co' => $mostAffectedCO,
                'target_achievement' => $targetAchievement,
                'students' => $students,
                'co_total_marks' => $coTotalMarks,
                'debug_all_scores' => $allScores
            ]
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}
function getCQIAttainment($erp_conn)
{
    if (!isset($_POST['courseId'])) {
        return ['status' => 'error', 'message' => 'Course ID is required'];
    }

    $courseId = (int)$_POST['courseId'];

    // First get regular components
    $componentsQuery = "
        SELECT 
            ec.component_id,
            ec.component_name,
            qt.co_number,
            SUM(qt.marks) as max_marks,
            ec.is_cqi
        FROM exam_components ec
        JOIN question_templates qt ON ec.component_id = qt.component_id
        WHERE ec.course_id = ? 
        AND ec.status != 'draft'
        GROUP BY ec.component_id, ec.component_name, qt.co_number
        ORDER BY ec.is_cqi, ec.component_id, qt.co_number";

    // Get student marks with details
    $marksQuery = "
        SELECT 
            s.uid as student_id,
            s.sname as student_name,
            s.sid as register_number,
            m.component_id,
            ec.component_name,
            qt.co_number,
            ec.is_cqi,
            SUM(m.marks_obtained) as obtained_marks,
            SUM(qt.marks) as total_marks,
            m.is_absent
        FROM marks m
        JOIN question_templates qt ON m.component_id = qt.component_id 
            AND m.question_number = qt.question_number
        JOIN exam_components ec ON m.component_id = ec.component_id
        JOIN student s ON m.student_id = s.uid
        WHERE ec.course_id = ? 
        AND ec.status != 'draft'
        GROUP BY s.uid, m.component_id, qt.co_number
        ORDER BY s.sid, ec.is_cqi, ec.component_id, qt.co_number";

    try {
        // Get components
        $stmt = $erp_conn->prepare($componentsQuery);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $componentsResult = $stmt->get_result();

        $components = [
            'regular' => [],
            'cqi' => []
        ];
        $totalCOMarks = [];

        while ($row = $componentsResult->fetch_assoc()) {
            $compName = $row['component_name'];
            $coNumber = 'CO' . $row['co_number'];
            $type = $row['is_cqi'] ? 'cqi' : 'regular';

            if (!isset($components[$type][$compName])) {
                $components[$type][$compName] = [];
            }
            $components[$type][$compName][$coNumber] = $row['max_marks'];

            if (!isset($totalCOMarks[$coNumber])) {
                $totalCOMarks[$coNumber] = [
                    'regular' => 0,
                    'cqi' => 0,
                    'total' => 0  // Combined total for both types
                ];
            }
            $totalCOMarks[$coNumber][$type] += (float)$row['max_marks'];
            $totalCOMarks[$coNumber]['total'] += (float)$row['max_marks'];  // Update combined total
        }

        // Get student marks
        $stmt = $erp_conn->prepare($marksQuery);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $marksResult = $stmt->get_result();

        $studentData = [];

        while ($row = $marksResult->fetch_assoc()) {
            $studentId = $row['student_id'];
            $compName = $row['component_name'];
            $coNumber = 'CO' . $row['co_number'];
            $type = $row['is_cqi'] ? 'cqi' : 'regular';

            if (!isset($studentData[$studentId])) {
                $studentData[$studentId] = [
                    'student_id' => $studentId,
                    'name' => $row['student_name'],
                    'register_number' => $row['register_number'],
                    'marks' => [
                        'regular' => [],
                        'cqi' => []
                    ],
                    'totals' => []
                ];
            }

            if (!isset($studentData[$studentId]['marks'][$type][$compName])) {
                $studentData[$studentId]['marks'][$type][$compName] = [];
            }

            // Store individual component marks
            $studentData[$studentId]['marks'][$type][$compName][$coNumber] = [
                'marks' => $row['obtained_marks'],
                'total' => $row['total_marks'],
                'is_absent' => (bool)$row['is_absent'],
                'percentage' => $row['total_marks'] > 0 ?
                    round(($row['obtained_marks'] / $row['total_marks']) * 100, 2) : 0
            ];

            // Initialize CO totals if not exists
            if (!isset($studentData[$studentId]['totals'][$coNumber])) {
                $studentData[$studentId]['totals'][$coNumber] = [
                    'regular' => ['obtained' => 0, 'total' => 0],
                    'cqi' => ['obtained' => 0, 'total' => 0],
                    'combined' => ['obtained' => 0, 'total' => 0]  // New combined total
                ];
            }

            // Update type-specific totals (regular/cqi)
            $studentData[$studentId]['totals'][$coNumber][$type]['obtained'] += $row['obtained_marks'];
            $studentData[$studentId]['totals'][$coNumber][$type]['total'] += $row['total_marks'];

            // Update combined totals
            $studentData[$studentId]['totals'][$coNumber]['combined']['obtained'] += $row['obtained_marks'];
            $studentData[$studentId]['totals'][$coNumber]['combined']['total'] += $row['total_marks'];

            // Calculate percentage based on combined totals
            $combinedTotal = $studentData[$studentId]['totals'][$coNumber]['combined'];
            $studentData[$studentId]['totals'][$coNumber]['percentage'] =
                $combinedTotal['total'] > 0 ?
                round(($combinedTotal['obtained'] / $combinedTotal['total']) * 100, 2) : 0;

            // Flag to indicate if this CO has both regular and CQI components
            $studentData[$studentId]['totals'][$coNumber]['has_both'] =
                ($studentData[$studentId]['totals'][$coNumber]['regular']['total'] > 0 &&
                    $studentData[$studentId]['totals'][$coNumber]['cqi']['total'] > 0);
        }

        // Calculate total internal marks after the while loop
        foreach ($studentData as $studentId => $student) {
            $totalInternal = 0;
            $weights = ['CO1' => 9, 'CO2' => 9, 'CO3' => 9, 'CO4' => 9, 'CO5' => 5];

            foreach ($weights as $co => $weight) {
                if (isset($student['totals'][$co]['combined']['obtained'])) {
                    $totalInternal += ($student['totals'][$co]['combined']['obtained'] / $weight);
                }
            }

            $studentData[$studentId]['total_internal'] = round($totalInternal, 2);
        }

        return [
            'status' => 'success',
            'components' => $components,
            'totalCOMarks' => $totalCOMarks,
            'students' => array_values($studentData)
        ];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

function getInternalMarks($erp_conn)
{
    if (!isset($_POST['courseId'])) {
        return ['status' => 'error', 'message' => 'Course ID is required'];
    }

    $courseId = (int)$_POST['courseId'];

    // Get regular component totals for each CO
    $regularCoTotalsQuery = "
        SELECT 
            qt.co_number,
            SUM(qt.marks) as total_marks
        FROM exam_components ec
        JOIN question_templates qt ON ec.component_id = qt.component_id
        WHERE ec.course_id = ? 
        AND ec.status != 'draft'
        AND ec.is_cqi = 0
        GROUP BY qt.co_number
        ORDER BY qt.co_number";

    // Get student marks for both regular and CQI components
    $marksQuery = "
        SELECT 
            s.uid as student_id,
            s.sname as student_name,
            s.sid as register_number,
            qt.co_number,
            ec.is_cqi,
            SUM(m.marks_obtained) as obtained_marks,
            SUM(qt.marks) as total_marks
        FROM marks m
        JOIN question_templates qt ON m.component_id = qt.component_id 
            AND m.question_number = qt.question_number
        JOIN exam_components ec ON m.component_id = ec.component_id
        JOIN student s ON m.student_id = s.uid
        WHERE ec.course_id = ? 
        AND ec.status != 'draft'
        GROUP BY s.uid, qt.co_number, ec.is_cqi
        ORDER BY s.sid, qt.co_number, ec.is_cqi";

    // Get CO weightages from unit table
    $weightageQuery = "
        SELECT CO, co_weightage 
        FROM unit 
        WHERE course_id = ?
        ORDER BY unit_number";

    try {
        // Get CO totals for headers
        $stmt = $erp_conn->prepare($regularCoTotalsQuery);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $coTotalsResult = $stmt->get_result();

        $coTotals = [];
        while ($row = $coTotalsResult->fetch_assoc()) {
            $coNumber = 'CO' . $row['co_number'];
            $coTotals[$coNumber] = $row['total_marks'];
        }

        // Get student marks
        $stmt = $erp_conn->prepare($marksQuery);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $marksResult = $stmt->get_result();

        $studentData = [];
        while ($row = $marksResult->fetch_assoc()) {
            $studentId = $row['student_id'];
            $coNumber = 'CO' . $row['co_number'];
            $type = $row['is_cqi'] ? 'cqi' : 'regular';

            if (!isset($studentData[$studentId])) {
                $studentData[$studentId] = [
                    'student_id' => $studentId,
                    'name' => $row['student_name'],
                    'register_number' => $row['register_number'],
                    'co_marks' => []
                ];
            }

            if (!isset($studentData[$studentId]['co_marks'][$coNumber])) {
                $studentData[$studentId]['co_marks'][$coNumber] = [
                    'regular' => ['obtained' => 0, 'total' => 0],
                    'cqi' => ['obtained' => 0, 'total' => 0],
                    'final_total' => 0
                ];
            }

            // Store marks based on type
            $studentData[$studentId]['co_marks'][$coNumber][$type] = [
                'obtained' => (float)$row['obtained_marks'],
                'total' => (float)$row['total_marks']
            ];
        }

        // Get CO weightages
        $stmt = $erp_conn->prepare($weightageQuery);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $weightageResult = $stmt->get_result();

        $coWeightages = [];
        while ($row = $weightageResult->fetch_assoc()) {
            $coWeightages[$row['CO']] = (float)$row['co_weightage'];
        }

        // Calculate final totals with CQI adjustments and internal marks
        foreach ($studentData as &$student) {
            $internalTotal = 0;

            foreach ($student['co_marks'] as $coNumber => &$coData) {
                $regularPercentage = ($coData['regular']['total'] > 0) ?
                    ($coData['regular']['obtained'] / $coData['regular']['total']) * 100 : 0;

                if ($regularPercentage < 58 && $coData['cqi']['total'] > 0) {
                    // Calculate how many marks needed to reach 58%
                    $marksNeededFor58 = ($coData['regular']['total'] * 0.58) - $coData['regular']['obtained'];

                    // Calculate proportion of CQI marks earned
                    $cqiProportion = $coData['cqi']['obtained'] / $coData['cqi']['total'];

                    // Add proportional CQI marks to final total
                    $additionalMarks = $marksNeededFor58 * $cqiProportion;
                    $coData['final_total'] = $coData['regular']['obtained'] + $additionalMarks;
                } else {
                    $coData['final_total'] = $coData['regular']['obtained'];
                }

                // Round final total to 2 decimal places
                $coData['final_total'] = round($coData['final_total'], 2);

                // Calculate internal marks contribution for this CO
                if (isset($coWeightages[$coNumber]) && $coWeightages[$coNumber] > 0) {
                    $internalTotal += $coData['final_total'] / $coWeightages[$coNumber];
                }
            }

            // Add internal marks to student data
            $student['internal_marks'] = round($internalTotal, 2);
        }

        return [
            'status' => 'success',
            'headers' => $coTotals,
            'students' => array_values($studentData),
            'co_weightages' => $coWeightages  // Including weightages in response for reference
        ];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

function getSubjectWiseAttendance($erp_conn)
{
    try {
        // Get parameters from POST
        $batch = $_POST['batch'];
        $semester = $_POST['semester'];
        $section = $_POST['section'];
        $academicYear = $_POST['academicYear'];

        $query = "
            SELECT 
                s.sid as roll_no,
                s.sname as student_name,
                c.course_code,
                c.course_name,
                COUNT(DISTINCT CASE WHEN a.attendance_status IN (0,1,3,4) THEN a.session_id END) as total_hours,
                SUM(CASE WHEN ae.attendance_status IN (2,3) THEN 1 ELSE 0 END) as present_hours,
                ROUND(
                    (SUM(CASE WHEN ae.attendance_status IN (2,3) THEN 1 ELSE 0 END) * 100.0 / 
                     NULLIF(COUNT(DISTINCT CASE WHEN a.attendance_status IN (0,1,3,4) THEN a.session_id END), 0)
                    ), 2
                ) as attendance_percentage
            FROM student s
            JOIN course_faculty cf ON s.uid = cf.student_id
            JOIN course c ON cf.course_id = c.course_id
            LEFT JOIN timetable t ON c.course_id = t.course_id
            LEFT JOIN attendance_session a ON t.timetable_id = a.timetable_id
            LEFT JOIN attendance_entry ae ON ae.session_id = a.session_id AND ae.student_id = s.uid
            WHERE t.batch = ?
              AND t.semester = ?
              AND t.section = ?
              AND t.academic_year = ?
            GROUP BY s.uid, s.sid, s.sname, c.course_id, c.course_code, c.course_name
            ORDER BY s.sid, c.course_code";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('ssss', $batch, $semester, $section, $academicYear);
        $stmt->execute();
        $result = $stmt->get_result();

        $subjectWiseData = [];
        while ($row = $result->fetch_assoc()) {
            $subjectWiseData[] = [
                'roll_no' => $row['roll_no'],
                'student_name' => $row['student_name'],
                'course_code' => $row['course_code'],
                'course_name' => $row['course_name'],
                'total_hours' => (int)$row['total_hours'],
                'present_hours' => (int)$row['present_hours'],
                'attendance_percentage' => (float)$row['attendance_percentage']
            ];
        }

        return [
            'status' => 'success',
            'data' => $subjectWiseData
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

switch ($action) {
    case 'getSubjectWiseAttendance':
        $response = getSubjectWiseAttendance($erp_conn);
        echo json_encode($response);
        break;
    case 'getAdvisorData':
        echo getAdvisorData($erp_conn);
        break;

    case 'getDepartments':
        $response = getDepartments($erp_conn);
        echo json_encode($response);
        break;

    case 'getFacultyByDepartment':
        $response = getFacultyByDepartment($erp_conn);
        echo json_encode($response);
        break;

    case 'getAdvisorStudents':
        $response = getAdvisorStudents($erp_conn);
        echo json_encode($response);
        break;

    case 'getSections':
        $response = getSections($erp_conn);
        echo json_encode($response);
        break;

    case 'getUniqueBatches':
        $response = getUniqueBatches($erp_conn);
        echo json_encode($response);
        break;

    case 'getUniqueSections':
        $response = getUniqueSections($erp_conn);
        echo json_encode($response);
        break;

    case 'getFacultyMembers':
        $response = getFacultyMembers($erp_conn);
        echo json_encode($response);
        break;

    case 'assignAdvisor':
        $response = assignAdvisor($erp_conn);
        echo json_encode($response);
        break;
    case 'mapStudentsToFaculty':
        $response = mapStudentsToFaculty($erp_conn);
        echo json_encode($response);
        break;

    case 'getAvailableCourses':
        $response = getAvailableCourses($erp_conn);
        echo json_encode($response);
        break;

    case 'getAdvisorCourses':
        $response = getAdvisorCourses($erp_conn);
        echo json_encode($response);
        break;

    case 'storeAdvisorData':
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['advisorData'])) {
                $_SESSION['advisorData'] = $_POST['advisorData'];
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No data provided']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
        break;
    case 'getFacultyStudentUIDs':
        $mappingData = json_decode($_POST['mappingData'], true);
        $response = array();
        $mappings = array();

        try {
            foreach ($mappingData as $mapping) {
                $facultyId = $mapping['Faculty ID'];
                $studentIds = array_map('trim', explode(',', $mapping['Student IDs (comma separated)']));

                // Get faculty UID
                $facultyQuery = "SELECT uid FROM faculty WHERE id = ?";
                $stmt = $erp_conn->prepare($facultyQuery);
                $stmt->bind_param("i", $facultyId);
                $stmt->execute();
                $facultyResult = $stmt->get_result();
                $facultyRow = $facultyResult->fetch_assoc();

                if ($facultyRow) {
                    foreach ($studentIds as $studentId) {
                        // Get student UID
                        $studentQuery = "SELECT uid FROM student WHERE sid = ?";
                        $stmt = $erp_conn->prepare($studentQuery);
                        $stmt->bind_param("s", $studentId);
                        $stmt->execute();
                        $studentResult = $stmt->get_result();
                        $studentRow = $studentResult->fetch_assoc();

                        if ($studentRow) {
                            $mappings[] = array(
                                'faculty_uid' => $facultyRow['uid'],
                                'student_uid' => $studentRow['uid']
                            );
                        }
                    }
                }
            }

            if (empty($mappings)) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'No valid mappings found'
                ));
            } else {
                echo json_encode(array(
                    'status' => 'success',
                    'mappings' => $mappings
                ));
            }
        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 'error',
                'message' => $e->getMessage()
            ));
        }
        break;
    case 'createFacultyStudentMappings':
        $mappings = json_decode($_POST['mappings'], true);

        try {
            // Begin transaction
            $erp_conn->begin_transaction();

            foreach ($mappings as $mapping) {
                // Insert into course_faculty table
                $query = "INSERT INTO course_faculty (course_id, faculty_id, student_id) VALUES (?, ?, ?)";
                $stmt = $erp_conn->prepare($query);
                $stmt->bind_param("iii", $courseId, $mapping['faculty_uid'], $mapping['student_uid']);
                $stmt->execute();
            }

            // Commit transaction
            $erp_conn->commit();

            echo json_encode(array(
                'status' => 'success',
                'message' => 'Mappings created successfully'
            ));
        } catch (Exception $e) {
            // Rollback on error
            $erp_conn->rollback();

            echo json_encode(array(
                'status' => 'error',
                'message' => $e->getMessage()
            ));
        }
        break;


    case 'getFacultyAcademics':
        $response = getFacultyAcademics($erp_conn);
        echo json_encode($response);
        break;


    case 'storeFacultyData':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['facultyData'])) {
            $_SESSION['selectedFacultyData'] = $_POST['facultyData'];
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No data provided']);
        }
        break;

    case 'saveTimetable':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $timetableData = $_POST['timetable'];
            $response = saveTimetable($erp_conn, $timetableData);
            echo json_encode($response);
        }
        break;

    case 'getTimeTable':
        $batch = $_POST['batch'];
        $semester = $_POST['semester'];
        $section = $_POST['section'];
        $academicYear = $_POST['academicYear'];
        $dept = $_POST['dept'];
        $id = $_POST['id'];
        $response = getTimeTable($erp_conn, $batch, $semester, $section, $academicYear, $dept, $id);
        echo json_encode($response);
        break;
    case 'getFacultyTimeTable':
        $academic_year = $_POST['academicYear'];
        $semesterType = $_POST['semesterType'];
        $facultyId = $_POST['facultyId'];

        $response = getFacultyTimeTable($erp_conn, $facultyId, $academic_year, $semesterType);
        echo json_encode($response);
        break;
    case 'getStudentAttendance':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $faculty_id = $_POST['facultyId'];
            $course_id = $_POST['courseId'];

            $response = getStudentAttendance($erp_conn, $faculty_id, $course_id);
            echo json_encode($response);
        }
        break;

    case 'getLessonPlanData':
        $response = getLessonPlanData($erp_conn);
        echo json_encode($response);
        break;
    case 'saveLessonPlan':
        $response = saveLessonPlan($erp_conn);
        echo json_encode($response);
        break;

    case 'saveAttendance':
        $response = saveAttendance($erp_conn);
        echo json_encode($response);
        break;

    case 'getFacultyList':
        $response = getFacultyList($erp_conn);
        echo json_encode($response);
        break;

    case 'getFacultyClasses':
        $response = getFacultyClasses($erp_conn);
        echo json_encode($response);
        break;

    case 'submitAlterationRequest':
        $response = submitAlterationRequest($erp_conn);
        echo json_encode($response);
        break;

    case 'getHourAlterations':
        $response = getHourAlterations($erp_conn);
        echo json_encode($response);
        break;

    case 'deleteHourAlteration':
        $response = deleteHourAlteration($erp_conn);
        echo json_encode($response);
        break;
    case 'checkAttendanceStatus':
        $response = checkAttendanceStatus($erp_conn);
        break;

    case 'getAttendanceView':
        $response = getAttendanceView($erp_conn);
        echo json_encode($response);
        break;

    case 'getFacultyPendingAttendance':
        $response = getFacultyPendingAttendance($erp_conn);
        echo $response;
        break;

    case 'editTimeTable':
        $timetableData = $_POST['timetable'];
        $response = editTimeTable($erp_conn, $timetableData);
        echo json_encode($response);
        break;
    case 'loadIncomingAlterations':
        $response = loadIncomingAlterations($erp_conn);
        echo json_encode($response);
        break;
    case 'saveMultipleAttendance':
        $response = saveMultipleAttendance($erp_conn);
        echo json_encode($response);
        break;

    case 'submitLeaveRequest':
        $response = submitLeaveRequest($erp_conn);
        echo json_encode($response);
        break;

    case 'getStudentLeaveList':
        $response = getStudentLeaveList($erp_conn);
        echo json_encode($response);
        break;

    case 'saveDayOrderOverride':
        $override_date = $_POST['override_date'];
        $assigned_day = $_POST['assigned_day'];
        $batch = $_POST['batch'];
        $academic_year = $_POST['academic_year'];
        $semester = $_POST['semester'];
        $section = $_POST['section'];

        try {
            $erp_conn->begin_transaction();

            // Insert into day_order_override table
            $query = "INSERT INTO day_order_override 
                     (override_date, batch, academic_year, semester, section, assigned_day)
                     VALUES (?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE assigned_day = VALUES(assigned_day)";

            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param(
                "sssiss",
                $override_date,
                $batch,
                $academic_year,
                $semester,
                $section,
                $assigned_day
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to save day order override");
            }

            // Create attendance sessions for the day order
            $sessionResult = createDayOrderAttendanceSessions(
                $erp_conn,
                $override_date,
                $batch,
                $semester,
                $section,
                $academic_year,
                $assigned_day
            );

            if ($sessionResult['status'] === 'error') {
                throw new Exception($sessionResult['message']);
            }

            $erp_conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Day order changed and attendance sessions created successfully',
                'sessions_created' => $sessionResult['sessions_created']
            ]);
        } catch (Exception $e) {
            $erp_conn->rollback();
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;
    case 'getStudentLeaveHistory':
        $response = getStudentLeaveHistory($erp_conn);
        echo json_encode($response);
        break;

    case 'deleteLeaveRequest':
        $response = deleteLeaveRequest($erp_conn);
        echo json_encode($response);
        break;
    case 'getODRequests':
        $response = getODRequests($erp_conn);
        echo json_encode($response);
        break;
    case 'approveRequest':
        $response = approveRequest($erp_conn);
        echo json_encode($response);
        break;
    case 'rejectRequest':
        $response = rejectRequest($erp_conn);
        echo json_encode($response);
        break;
    case 'getFacultyMarkingStatus':
        $response = getFacultyMarkingStatus($erp_conn);
        echo json_encode($response);
        break;

    case 'getFacultyAttendanceSummary':
        $batch = $_POST['batch'] ?? null;
        $academicYear = $_POST['academicYear'] ?? null;
        $semester = $_POST['semester'] ?? null;
        $section = $_POST['section'] ?? null;

        // Validate required parameters
        if (!$batch || !$academicYear || !$semester || !$section) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
            exit;
        }

        try {
            // Modified query based on status codes:
            // - Total hours: count all attendance sessions except those with status code 2 (Holiday)
            // - Pending hours: count sessions where attendance_status is 0 or 4 (both "Not marked")
            $query = "
                SELECT 
                    c.course_name,
                    f.name AS faculty_name,
                    COUNT(DISTINCT CASE WHEN a.attendance_status IS NOT NULL AND a.attendance_status <> 2 THEN a.session_id END) AS total_hours,
                    SUM(CASE WHEN a.attendance_status IN (0,4) THEN 1 ELSE 0 END) AS pending_hours
                FROM timetable t
                INNER JOIN course c ON t.course_id = c.course_id
                INNER JOIN faculty f ON t.faculty_id = f.uid
                LEFT JOIN attendance_session a ON a.timetable_id = t.timetable_id
                WHERE t.batch = ?
                    AND t.academic_year = ?
                    AND t.semester = ?
                    AND t.section = ?
                GROUP BY c.course_id, f.uid, c.course_name, f.name
                ORDER BY c.course_name, f.name";

            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param('ssss', $batch, $academicYear, $semester, $section);
            $stmt->execute();
            $result = $stmt->get_result();

            $summaryData = [];
            while ($row = $result->fetch_assoc()) {
                $summaryData[] = $row;
            }

            echo json_encode([
                'status' => 'success',
                'data' => $summaryData
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
        break;

    case 'getStudentAttendanceSummary':
        $response = getStudentAttendanceSummary($erp_conn);
        echo json_encode($response);
        break;

    case 'getFacultyCourses':
        try {
            $faculty_id = $_POST['faculty_id'] ?? null;
            $academic_year = $_POST['academic_year'] ?? null;
            $semester_type = $_POST['semester_type'] ?? null;

            if (!$faculty_id || !$academic_year || !$semester_type) {
                throw new Exception('Missing required parameters');
            }

            // Determine semester numbers based on type
            $semesters = $semester_type === 'Even' ? [2, 4, 6, 8] : [1, 3, 5, 7];
            $semester_list = implode(',', $semesters);

            $query = "
                SELECT DISTINCT 
                    c.course_id,
                    c.course_code,
                    c.status,
                    c.reason,
                    c.course_name,
                    c.course_credit,
                    c.course_type,
                    c.semester,
                    c.ayear,
                    cf.batch,
                    cf.section,
                    cf.department
                FROM course c
                INNER JOIN course_faculty cf ON c.course_id = cf.course_id
                WHERE cf.faculty_id = ?
                AND c.ayear = ?
                AND c.semester IN ($semester_list)
                ORDER BY c.semester, cf.section, c.course_code
            ";

            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param('is', $faculty_id, $academic_year);
            $stmt->execute();
            $result = $stmt->get_result();

            $courses = [];
            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }

            echo json_encode([
                'status' => 'success',
                'data' => $courses
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;
    case 'getSpecialAttendanceSessions':
        $response = getSpecialAttendanceSessions($erp_conn);
        echo json_encode($response);
        break;
    case 'getStudentsForSpecialAttendance':
        $response = getStudentsForSpecialAttendance($erp_conn);
        echo json_encode($response);
        break;
    case 'getStudentLeaveListSpecialAttendance':
        $response = getStudentLeaveListSpecialAttendance($erp_conn);
        echo json_encode($response);
        break;
    case 'markSpecialAttendance':
        $response = markSpecialAttendance($erp_conn);
        echo json_encode($response);
        break;
    case 'checkSpecialAttendanceStatus':
        $response = checkSpecialAttendanceStatus($erp_conn);
        echo json_encode($response);
        break;
    case 'updateLessonPlan':
        $response = updateLessonPlan($erp_conn);
        echo json_encode($response);
        break;
    case 'markHoliday':
        $response = markHoliday($erp_conn);
        echo json_encode($response);
        break;
    case 'getCourseStudents':
        $response = getCourseStudents($erp_conn);
        echo json_encode($response);
        break;
    case 'getAttendanceSummary':
        $response = getAttendanceSummary($erp_conn);
        echo json_encode($response);
        break;
    case 'getLms':
        $response = getLms($erp_conn);
        echo json_encode($response);
        break;
    case 'saveAllLmsTopics':
        saveAllLmsTopics($erp_conn);
        break;

    case 'editLmsTopic':
        editLmsTopic($erp_conn);
        break;

    case 'getLmsCourses':
        $response = getLmsCourses($erp_conn);
        break;

    case 'getAttendancePercentage':
        $response = getAttendancePercentage($erp_conn);
        echo json_encode($response);
        break;
    case 'ApproveCourse':
        $response = ApproveCourse($erp_conn);
        echo json_encode($response);
        break;
    case 'updateStatus':
        $response = updateStatus($erp_conn);
        echo json_encode($response);
        break;
    case 'serveFile':
        $filePath = $_GET['file'];
        if (file_exists($filePath)) {
            header('Content-Type: application/pdf');
            readfile($filePath);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'error',
                'message' => 'File not found'
            ]);
        }
        break;

    case 'getCourseDetails':
        $response = getCourseDetails($erp_conn);
        echo json_encode($response);
        break;
    
    case 'addExamComponent':
        try {
            // Validate required fields
            $required_fields = ['course_id', 'component_name', 'component_type', 'conducted_marks', 
                                'weightage_marks', 'exam_date'];
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }
                    // Check if component already exists for this course and faculty
            $duplicateCheck = "SELECT component_id 
                            FROM exam_components 
                            WHERE course_id = ? 
                            AND faculty_id = ? 
                            AND component_name = ?";

                            $stmt = $erp_conn->prepare($duplicateCheck);
                            $faculty_id = $_SESSION['user']['uid'];
                            $stmt->bind_param("iis", 
                            $_POST['course_id'],
                            $faculty_id,
                            $_POST['component_name']
                            );
                            $stmt->execute();
                            $duplicateResult = $stmt->get_result();

                            if ($duplicateResult->num_rows > 0) {
                            throw new Exception("An exam component with this name already exists for this course.");
                            }
    
         // Get course details including batch, academic year, semester, and section
                    $courseQuery = "SELECT DISTINCT 
                    cf.batch,
                    c.ayear as academicYear,
                    c.semester,
                    cf.section,
                    c.dept as department
                FROM course c
                JOIN course_faculty cf ON c.course_id = cf.course_id
                WHERE c.course_id = ?";

            $stmt = $erp_conn->prepare($courseQuery);
            $stmt->bind_param("i", $_POST['course_id']);
            $stmt->execute();
            $courseResult = $stmt->get_result();

            if ($courseResult->num_rows === 0) {
            throw new Exception("Course details not found");
            }

            $courseDetails = $courseResult->fetch_assoc();


            $examQuery = "SELECT e.exam_id 
            FROM exams e
            JOIN exam_subjects es ON e.exam_id = es.exam_id
            WHERE e.exam_name = ? 
            AND e.batch = ? 
            AND e.department = ? 
            AND e.academicYear = ? 
            AND e.section = ?
            AND es.course_id = ?
            AND es.exam_date = ?";  // Added exam_date check

            $stmt = $erp_conn->prepare($examQuery);
            $stmt->bind_param("sssssss", 
            $_POST['component_name'],
            $courseDetails['batch'],
            $courseDetails['department'],
            $courseDetails['academicYear'],
            $courseDetails['section'],
            $_POST['course_id'],
            $_POST['exam_date']  // Added exam_date parameter
            );
            $stmt->execute();
            $examResult = $stmt->get_result();

            if ($examResult->num_rows === 0) {
            throw new Exception("This exam component is not scheduled in the exam timetable for this date. Please contact your exam coordinator.");
            }



            $examRow = $examResult->fetch_assoc();
            $examId = $examRow['exam_id'];
    
            // Check if exam date matches in exam_subjects table
            $subjectQuery = "SELECT exam_date FROM exam_subjects 
                            WHERE exam_id = ? 
                            AND course_id = ?";
            
            $stmt = $erp_conn->prepare($subjectQuery);
            $stmt->bind_param("ii", $examId, $_POST['course_id']);
            $stmt->execute();
            $subjectResult = $stmt->get_result();
    
            if ($subjectResult->num_rows === 0) {
                throw new Exception("This course is not scheduled for this exam. Please contact your exam coordinator.");
            }
    
            $subjectRow = $subjectResult->fetch_assoc();
            if ($subjectRow['exam_date'] != $_POST['exam_date']) {
                throw new Exception("The exam date doesn't match with the scheduled date in exam timetable. Please contact your exam coordinator.");
            }
    
            // If we reach here, exam exists and dates match
            // Continue with existing component creation logic
            $deadline_date = date('Y-m-d', strtotime($_POST['exam_date'] . ' + 4 days'));
            $faculty_id = $_SESSION['user']['uid'];
            $current_date = date('Y-m-d');
            $status = strtotime($current_date) > strtotime($deadline_date) ? 'locked' : 'draft';
    
            // Calculate deadline date (exam_date + 4 days)
            $exam_date = $_POST['exam_date'];
            $deadline_date = date('Y-m-d', strtotime($exam_date . ' + 4 days'));
            $faculty_id = $_SESSION['user']['uid'];

            // Check if current date is past deadline
            $current_date = date('Y-m-d');
            $status = strtotime($current_date) > strtotime($deadline_date) ? 'locked' : 'draft';

            // Prepare the query
            $query = "INSERT INTO exam_components (
                course_id, faculty_id, component_name, component_type, conducted_marks, 
                weightage_marks, exam_date, deadline_date, is_cqi, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";

            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param(
                "iissddsss",
                $_POST['course_id'],
                $faculty_id,
                $_POST['component_name'],
                $_POST['component_type'],
                $_POST['conducted_marks'],
                $_POST['weightage_marks'],
                $_POST['exam_date'],
                $deadline_date,
                $status
            );

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Component added successfully',
                    'component_status' => $status
                ]);
            } else {
                throw new Exception("Failed to add component");
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'getExamComponents':
        try {
            $course_id = $_POST['course_id'] ?? null;
            $faculty_id = $_SESSION['user']['uid'];
            if (!$course_id) {
                throw new Exception('Course ID is required');
            }

            $query = "
                SELECT 
                    ec.*,
                    DATEDIFF(ec.deadline_date, CURRENT_DATE) as days_remaining,
                    c.course_name,
                    c.course_code
                FROM exam_components ec
                JOIN course c ON ec.course_id = c.course_id
                WHERE ec.course_id = ? AND ec.faculty_id = ?
                ORDER BY ec.exam_date DESC
            ";

            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param('ii', $course_id, $faculty_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $components = [];
            while ($row = $result->fetch_assoc()) {
                // Add status class for UI
                $statusClass = match ($row['status']) {
                    'draft' => 'bg-secondary',
                    'pending_marks' => 'bg-warning',
                    'marks_entered' => 'bg-info',
                    'approved' => 'bg-success',
                    'locked' => 'bg-dark',
                    default => 'bg-secondary'
                };

                $row['status_class'] = $statusClass;
                $components[] = $row;
            }

            echo json_encode([
                'status' => 'success',
                'data' => $components
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'requestTimeTableEdit':
        $batch = $_POST['batch'];
        $semester = $_POST['semester'];
        $section = $_POST['section'];
        $academicYear = $_POST['academicYear'];
        $id = $_POST['id'];
        $status = $_POST['status'];
        $response = requestTimeTableEdit($erp_conn, $batch, $semester, $section, $academicYear, $id, $status);
        echo json_encode($response);
        break;

    case 'requestLessonPlanEdit':
        $courseId = $_POST['courseId'];
        $status = $_POST['status'];
        $response = requestLessonPlanEdit($erp_conn, $courseId, $status);
        echo json_encode($response);
        break;

    case 'get_component_details':
        $componentId = $_POST['componentId'] ?? 0;
        $query = "SELECT component_name, conducted_marks, weightage_marks, component_type, 
                            exam_date, deadline_date,is_cqi 
                              FROM exam_components 
                    WHERE component_id = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('i', $componentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $component = $result->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'data' => $component
        ]);
        break;
    case 'get_template':
        $componentId = $_POST['component_id'] ?? 0;
        $query = "SELECT question_number, marks, co_number 
                        FROM question_templates 
                        WHERE component_id = ? 
                        ORDER BY question_number";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('i', $componentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $template = [];
        while ($row = $result->fetch_assoc()) {
            $template[] = $row;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $template
        ]);
        break;
    case 'getCourseStudent';
        $query = "SELECT student_id FROM course_faculty WHERE course_id = ? AND faculty_id = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ii", $_POST['courseId'], $_POST['facultyId']);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch student IDs
        $student_ids = [];
        while ($row = $result->fetch_assoc()) {
            $student_ids[] = $row['student_id'];
        }

        // If no students found, return empty result
        if (empty($student_ids)) {
            return [
                'status' => 'success',
                'students' => []
            ];
        }

        // Step 2: Get student details from the student table
        $placeholders = implode(',', array_fill(0, count($student_ids), '?'));
        $query = "SELECT uid as student_id, sid as rollNo, sname as name, ayear as academicYear, dept as department, section FROM student WHERE uid IN ($placeholders)";

        $stmt = $erp_conn->prepare($query);

        // Bind parameters dynamically
        $types = str_repeat("i", count($student_ids)); // Create a string of 'i' for integers
        $stmt->bind_param($types, ...$student_ids);

        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all student details
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $students
        ]);
        break;

    case 'get_existing_marks':
        $componentId = $_POST['component_id'] ?? 0;

        $query = "SELECT m.student_id, m.question_number, m.marks_obtained, m.is_absent
                            FROM marks m
                            WHERE m.component_id = ?
                            ORDER BY m.student_id, m.question_number";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('i', $componentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $marks = [];
        $currentStudent = null;
        while ($row = $result->fetch_assoc()) {
            if ($currentStudent !== $row['student_id']) {
                if ($currentStudent !== null) {
                    $marks[] = $markData;
                }
                $currentStudent = $row['student_id'];
                $markData = [
                    'student_id' => $row['student_id'],
                    'is_absent' => $row['is_absent'],
                    'marks' => []
                ];
            }
            $markData['marks'][] = [
                'question_number' => $row['question_number'],
                'marks_obtained' => $row['marks_obtained']
            ];
        }
        if ($currentStudent !== null) {
            $marks[] = $markData;
        }


        echo json_encode([
            'status' => 'success',
            'data' => $marks
        ]);
        break;
    case 'save_marks':
        header('Content-Type: application/json');

        try {
            $marks = json_decode($_POST['marks'] ?? '[]', true);

            if (empty($marks)) {
                echo json_encode(['status' => 'error', 'message' => 'No marks data provided']);
                exit;
            }

            $erp_conn->begin_transaction();

            // Get component details for validation
            $componentId = $marks[0]['component_id'] ?? 0;
            $query = "SELECT status, conducted_marks, is_cqi FROM exam_components WHERE component_id = ?";
            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param('i', $componentId);
            $stmt->execute();
            $componentResult = $stmt->get_result();
            $componentDetails = $componentResult->fetch_assoc();

            if (!$componentDetails) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid component ID']);
                exit;
            }

            if (in_array($componentDetails['status'], ['approved', 'locked'])) {
                echo json_encode(['status' => 'error', 'message' => 'Cannot modify marks for approved or locked components']);
                exit;
            }

            // Get question template for validation
            $query = "SELECT question_number, marks as max_marks FROM question_templates WHERE component_id = ?";
            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param('i', $componentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $questionTemplates = [];
            while ($row = $result->fetch_assoc()) {
                $questionTemplates[$row['question_number']] = $row['max_marks'];
            }

            // Prepare statements
            $checkQuery = "SELECT mark_id FROM marks WHERE student_id = ? AND academic_year = ? AND component_id = ? AND question_number = ?";
            $updateQuery = "UPDATE marks SET marks_obtained = ?, is_absent = ?, is_cqi = ?, last_modified = NOW() WHERE student_id = ? AND academic_year = ? AND component_id = ? AND question_number = ?";
            $insertQuery = "INSERT INTO marks (student_id, academic_year, component_id, question_number, marks_obtained, is_absent, is_cqi, entry_date, last_modified) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $checkStmt = $erp_conn->prepare($checkQuery);
            $updateStmt = $erp_conn->prepare($updateQuery);
            $insertStmt = $erp_conn->prepare($insertQuery);

            $updatedCount = 0;
            $insertedCount = 0;
            $errors = [];

            foreach ($marks as $mark) {
                $studentId = $mark['student_id'];
                $academicYear = $_POST['academicYear'];
                $questionNumber = (int)$mark['question_number'];
                $marksObtained = isset($mark['marks']) ? (float)$mark['marks'] : 0;
                $isAbsent = $mark['is_absent'] ? 1 : 0;
                $isCqi = isset($mark['is_cqi']) ? (int)$mark['is_cqi'] : 0;

                // Fetch student UID
                $uidStmt = $erp_conn->prepare("SELECT uid FROM student WHERE sid = ?");
                $uidStmt->bind_param('s', $studentId);
                $uidStmt->execute();
                $uidResult = $uidStmt->get_result();
                $uidRow = $uidResult->fetch_assoc();

                if (!$uidRow) {
                    $errors[] = "Invalid student ID: {$studentId}";
                    continue;
                }

                $studentUid = $uidRow['uid'];
                $maxMarks = $questionTemplates[$questionNumber] ?? 0;

                if (!$isAbsent && ($marksObtained < 0 || $marksObtained > $maxMarks)) {
                    $errors[] = "Invalid marks for student {$studentId}, question {$questionNumber}";
                    continue;
                }

                // Check if record exists
                $checkStmt->bind_param('ssii', $studentUid, $academicYear, $componentId, $questionNumber);
                $checkStmt->execute();

                if ($checkStmt->get_result()->num_rows > 0) {
                    // Update
                    $updateStmt->bind_param('diissii', $marksObtained, $isAbsent, $isCqi, $studentUid, $academicYear, $componentId, $questionNumber);
                    $updateStmt->execute();
                    $updatedCount++;
                } else {
                    // Insert
                    $insertStmt->bind_param('ssiidii', $studentUid, $academicYear, $componentId, $questionNumber, $marksObtained, $isAbsent, $isCqi);
                    $insertStmt->execute();
                    $insertedCount++;
                }
            }

            if (!empty($errors)) {
                $erp_conn->rollback();
                echo json_encode(['status' => 'error', 'message' => 'Some records failed to save', 'errors' => $errors]);
                exit;
            }

            // Update component status
            $query = "UPDATE exam_components SET status = 'marks_entered', last_modified = NOW() WHERE component_id = ? AND status NOT IN ('approved', 'locked')";
            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param('i', $componentId);
            $stmt->execute();

            // Audit log
            $query = "INSERT INTO mark_entry_audit (component_id, action_type, user_id, details) VALUES (?, ?, ?, ?)";
            $stmt = $erp_conn->prepare($query);
            $userId = $_SESSION['user_id'] ?? 'system';
            $auditDetails = json_encode(['timestamp' => date('Y-m-d H:i:s'), 'marks_count' => count($marks), 'action' => 'marks_saved', 'is_cqi' => $isCqi]);
            $actionType = 'update';
            $stmt->bind_param('isss', $componentId, $actionType, $userId, $auditDetails);
            $stmt->execute();

            $erp_conn->commit();
            echo json_encode(['status' => 'success', 'message' => "Successfully saved marks (Updated: $updatedCount, Inserted: $insertedCount)", 'updated' => $updatedCount, 'inserted' => $insertedCount]);
        } catch (Exception $e) {
            $erp_conn->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Error saving marks: ' . $e->getMessage()]);
        }
        exit;
    case 'createTemplate':
        try {
            // Validate required fields
            if (!isset($_POST['component_id'])) {
                throw new Exception('Component ID is required');
            }

            $componentId = intval($_POST['component_id']);

            // Start transaction
            $erp_conn->begin_transaction();

            // First, update the component status to pending_marks
            $updateQuery = "UPDATE exam_components 
                           SET status = 'pending_marks' 
                           WHERE component_id = ?";
            $stmt = $erp_conn->prepare($updateQuery);
            $stmt->bind_param('i', $componentId);
            $stmt->execute();

            // Insert template questions
            $insertQuery = "INSERT INTO question_templates 
                           (component_id, question_number, marks, co_number, is_cqi) 
                           VALUES (?, ?, ?, ?, 0)";
            $stmt = $erp_conn->prepare($insertQuery);

            // Process each question from the form
            foreach ($_POST['questions'] as $qNum => $question) {
                $marks = floatval($question['marks']);
                $coNumber = intval($question['co_number']);

                $stmt->bind_param(
                    'iidd',
                    $componentId,
                    $qNum,
                    $marks,
                    $coNumber
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert question $qNum");
                }
            }

            // Commit transaction
            $erp_conn->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'Template created successfully'
            ]);
        } catch (Exception $e) {
            // Rollback on error
            if ($erp_conn->connect_error) {
                $erp_conn->rollback();
            }

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;


    case 'createCqiTemplate':
        try {
            // Validate required fields
            if (!isset($_POST['course_id'])) {
                throw new Exception('Course ID is required');
            }

            // Start transaction
            $erp_conn->begin_transaction();

            // Calculate deadline date (exam_date + 4 days)
            $exam_date = $_POST['exam_date'];
            $deadline_date = date('Y-m-d', strtotime($exam_date . ' + 4 days'));
            $faculty_id = $_SESSION['user']['uid'];

            // First, insert into exam_components
            $componentQuery = "INSERT INTO exam_components (
                course_id, faculty_id, component_name, conducted_marks, weightage_marks,
                exam_date, deadline_date, component_type, is_cqi, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'internal', 1, 'pending_marks')";

            $stmt = $erp_conn->prepare($componentQuery);
            $stmt->bind_param(
                'iisddss',
                $_POST['course_id'],
                $faculty_id,
                $_POST['component_name'],
                $_POST['conducted_marks'],
                $_POST['weightage_marks'],
                $exam_date,
                $deadline_date
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to create CQI component");
            }

            // Get the newly created component_id
            $componentId = $erp_conn->insert_id;

            // Insert template questions
            $questionQuery = "INSERT INTO question_templates 
                             (component_id, question_number, marks, co_number, is_cqi) 
                             VALUES (?, ?, ?, ?, 1)";
            $stmt = $erp_conn->prepare($questionQuery);

            // Process each question from the form
            foreach ($_POST['questions'] as $qNum => $question) {
                $marks = floatval($question['marks']);
                $coNumber = intval($question['co_number']);

                $stmt->bind_param(
                    'iidd',
                    $componentId,
                    $qNum,
                    $marks,
                    $coNumber
                );

                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert question $qNum");
                }
            }

            // Commit transaction
            $erp_conn->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'CQI Template created successfully'
            ]);
        } catch (Exception $e) {
            // Rollback on error
            if (!$erp_conn->connect_error) {
                $erp_conn->rollback();
            }

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'checkCqiStatus':
        $courseId = $_POST['course_id'] ?? 0;
        $faculty_id = $_SESSION['user']['uid'];

        // Check if any CQI components exist and their status
        $stmt = $erp_conn->prepare("SELECT 
            EXISTS(SELECT 1 FROM exam_components 
                    WHERE course_id = ? AND is_cqi = 1 AND faculty_id = ?) AS has_cqi,
            EXISTS(SELECT 1 FROM exam_components 
                    WHERE course_id = ? AND is_cqi = 1 AND faculty_id = ? 
                    AND status = 'approved') AS has_cqi_marks_entered,
            EXISTS(SELECT 1 FROM exam_components 
                    WHERE course_id = ? AND is_cqi = 0 AND faculty_id = ? 
                    AND status = 'approved') AS has_regular_marks_entered
        ");
        $stmt->bind_param('iiiiii', $courseId, $faculty_id, $courseId, $faculty_id, $courseId, $faculty_id);
            $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        echo json_encode([
            'status' => 'success',
            'hasCqi' => (bool)$result['has_cqi'],
            'hasCQIMarksEntered' => (bool)$result['has_cqi_marks_entered'],
            'hasRegularMarksEntered' => (bool)$result['has_regular_marks_entered']
        ]);
        break;

    case 'fetchCourseDetails':
        $response = fetchCourseDetails($erp_conn);
        echo json_encode($response);
        break;

    case 'getComponentCOData':
        $response = getComponentCOData($erp_conn);
        echo json_encode($response);
        break;

    case 'getCQIAnalysis':
        $response = getCQIAnalysis($erp_conn);
        echo json_encode($response);
        break;

    case 'get_cqi_attainment':
        $response = getCQIAttainment($erp_conn);
        echo json_encode($response);
        break;
    case 'get_excel_template':
        $componentId = $_POST['component_id'] ?? 0;
        $query = "
           SELECT * FROM question_templates WHERE component_id = ?
            ";
        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare Error (Question Templates): " . $erp_conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $erp_conn->error]);
            exit;
        }

        $stmt->bind_param('i', $componentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $questions = [];

        while ($row = $result->fetch_assoc()) {
            $questions[] = [
                'question_number'             => $row['question_number'],
                'marks' => $row['marks']

            ];
        }

        echo json_encode([
            'status' => 'success',
            'data' => $questions
        ]);

        break;

    case "getRollNumbers":
        // Assuming $uids is already set from POST data
        $uids = isset($_POST['uids']) ? json_decode($_POST['uids'], true) : [];

        // For debugging, you can uncomment the line below
        // error_log("Received UIDs: " . print_r($uids, true));

        if (!empty($uids) && is_array($uids)) {
            // Convert UIDs to integers since uid is stored as an int in the database
            $uids = array_map('intval', $uids);

            // Create placeholders for the prepared statement
            $placeholders = implode(',', array_fill(0, count($uids), '?'));

            // Prepare the SQL query
            $sql = "SELECT uid, sid FROM student WHERE uid IN ($placeholders)";
            $stmt = $erp_conn->prepare($sql);
            if (!$stmt) {
                echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $erp_conn->error]);
                exit;
            }

            // Build the types string using 'i' for each UID
            $types = str_repeat('i', count($uids));

            // Bind the UID values dynamically
            $stmt->bind_param($types, ...$uids);
            $stmt->execute();
            $result = $stmt->get_result();

            $mapping = [];
            while ($row = $result->fetch_assoc()) {
                $mapping[$row['uid']] = $row['sid'];
            }

            echo json_encode(['success' => true, 'data' => $mapping]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid UIDs provided.']);
        }
        break;
    case 'approve_marks':
        $componentId = $_POST['component_id'] ?? 0;

        $erp_conn->begin_transaction();
        try {
            // Update component status
            $query = "UPDATE exam_components 
                                    SET status = 'approved' 
                                    WHERE component_id = ?";
            $stmt = $erp_conn->prepare($query);
            $stmt->bind_param('i', $componentId);
            $stmt->execute();

            // Log the approval
            $query = "INSERT INTO mark_entry_audit 
                                    (component_id, action_type, user_id, details) 
                                    VALUES (?, 'approve', ?, ?)";
            $stmt = $erp_conn->prepare($query);
            $userId = $_SESSION['user_id'] ?? 'system';
            $details = json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'action' => 'marks_approved'
            ]);
            $stmt->bind_param('iss', $componentId, $userId, $details);
            $stmt->execute();

            $erp_conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Marks approved successfully'
            ]);
        } catch (Exception $e) {
            $erp_conn->rollback();
            $response = ['status' => 'error', 'message' => 'Failed to approve marks: ' . $e->getMessage()];
        }
        break;

    case 'getInternalMarks':
        $response = getInternalMarks($erp_conn);
        echo json_encode($response);
        break;

        case 'get_cqi_students':
            $componentId = $_POST['component_id'] ?? 0;
            $subjectId = $_POST['subject_id'] ?? '';
            $facultyId = $_POST['facultyId'] ?? '';
        
            $query = "WITH AllStudents AS (
                        SELECT DISTINCT
                            s.sid,
                            s.sname,
                            qt.co_number,
                            -- Calculate total obtained marks per CO
                            SUM(CASE WHEN m.is_absent = 0 THEN m.marks_obtained ELSE 0 END) as obtained_marks,
                            SUM(qt.marks) as total_marks,
                            m.component_id,
                            ec.component_name,
                            m.question_number
                        FROM student s
                        JOIN course_faculty ss ON s.uid = ss.student_id
                            AND ss.course_id = ?
                            AND ss.faculty_id = ?
                        JOIN marks m ON s.uid = m.student_id
                        JOIN exam_components ec ON m.component_id = ec.component_id
                        JOIN question_templates qt ON m.component_id = qt.component_id 
                            AND m.question_number = qt.question_number
                        WHERE m.is_cqi = 0
                        AND ec.is_cqi = 0
                        AND ec.course_id = ?
                        GROUP BY s.sid, s.sname, qt.co_number, m.component_id, ec.component_name, m.question_number
                    ),
                    StudentCOAttainment AS (
                        -- Calculate CO-wise attainment for each student
                        SELECT 
                            sid,
                            sname,
                            co_number,
                            SUM(obtained_marks) as total_obtained,
                            SUM(total_marks) as total_possible,
                            (SUM(obtained_marks) / SUM(total_marks) * 100) as co_percentage
                        FROM AllStudents
                        GROUP BY sid, sname, co_number
                        HAVING co_percentage < 58
                    )
                    SELECT 
                        a.sid,
                        a.sname as name,
                        GROUP_CONCAT(DISTINCT sca.co_number) as unattained_cos,
                        GROUP_CONCAT(
                            DISTINCT CONCAT(
                                sca.co_number, ':',
                                ROUND(sca.co_percentage, 2)
                            )
                        ) as co_percentages,
                        GROUP_CONCAT(
                            DISTINCT CONCAT(
                                a.co_number, '|',
                                a.component_name, '|',
                                a.question_number, '|',
                                a.obtained_marks, '|',
                                a.total_marks
                            )
                            ORDER BY a.component_id, a.question_number
                        ) as marks_details
                    FROM AllStudents a
                    JOIN StudentCOAttainment sca ON a.sid = sca.sid
                    GROUP BY a.sid, a.sname";
        
            $stmt = $erp_conn->prepare($query); // Note: Changed $conn to $erp_conn to match your connection variable
            $stmt->bind_param('sii', $subjectId, $facultyId, $subjectId);
            $stmt->execute();
            $result = $stmt->get_result();
        
            $students = [];
            while ($row = $result->fetch_assoc()) {
                // Process marks details into a structured format
                $marksDetails = [];
                $previousMarks = [];
        
                foreach (explode(',', $row['marks_details']) as $detail) {
                    list($coNumber, $componentName, $questionNumber, $obtainedMarks, $totalMarks) = explode('|', $detail);
        
                    if (!isset($marksDetails[$componentName])) {
                        $marksDetails[$componentName] = [];
                    }
        
                    $marksDetails[$componentName][] = [
                        'question_number' => (int)$questionNumber,
                        'obtained_marks' => (float)$obtainedMarks,
                        'total_marks' => (float)$totalMarks
                    ];
        
                    // Aggregate previous marks per CO
                    if (!isset($previousMarks[$coNumber])) {
                        $previousMarks[$coNumber] = 0;
                    }
                    $previousMarks[$coNumber] += (float)$obtainedMarks;
                }
        
                $students[] = [
                    'sid' => $row['sid'],
                    'name' => $row['name'],
                    'unattained_cos' => array_filter(explode(',', $row['unattained_cos'])),
                    'co_percentages' => $row['co_percentages'],
                    'previous_marks' => $previousMarks
                ];
            }
        
            echo json_encode([
                'status' => 'success',
                'data' => $students
            ]);
            break;

    case 'getPendingLessonPlanEditRequest':
        $dept = $_SESSION['user']['dept'];
        $status = "Pending";
        $query = "SELECT 
                    c.course_id, 
                    c.course_code, 
                    c.course_name, 
                    c.dept, 
                    c.ayear, 
                    c.semester, 
                    c.status, 
                    c.reason, 
                    c.lessonplan_edit_status,  
                    ca.advisor_id, 
                    ca.faculty_id, 
                    f.name AS faculty_name
                  FROM course c
                  INNER JOIN class_advisors ca ON ca.academic_year = c.ayear AND ca.semester = c.semester 
                  INNER JOIN faculty f ON f.uid = ca.faculty_id and f.dept = c.dept
                  WHERE c.lessonplan_edit_status = ? AND c.dept = ?";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ss", $status, $dept);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data'   => $data
        ]);
        break;
    case 'getTimeTableEditRequest':
        $dept = $_SESSION['user']['dept'];
        $status = "Pending";
        $query = "SELECT 
                        ca.advisor_id,
                        ca.faculty_id,
                        ca.batch,
                        ca.academic_year,
                        ca.semester,
                        ca.section,
                        ca.start_date,
                        ca.end_date,
                        ca.timetable_edit_status,
                        f.name AS faculty_name
                    FROM class_advisors ca
                    INNER JOIN faculty f ON f.uid = ca.faculty_id
                    WHERE ca.timetable_edit_status = ? 
                    AND f.dept = ?";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ss", $status, $dept);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data'   => $data
        ]);
        break;

    case 'setTimeTableEditStatus':
        $id = $_POST['id'];
        $status = $_POST['status'];
        $query = "UPDATE class_advisors SET timetable_edit_status = ? WHERE advisor_id = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Time table edit status updated successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Time table edit status update failed'
            ]);
        }
        break;

    case 'getAttendanceLockedRequest':
        $dept = $_SESSION['user']['dept'];
        $query = "SELECT 
                            a.session_id,
                            a.timetable_id,
                            a.class_date,
                            a.semester,
                            t.faculty_id,
                            t.batch,
                            t.academic_year,
                            t.section,
                            f.name as faculty_name,
                            c.course_name
                        FROM attendance_session a
                        INNER JOIN timetable t ON a.timetable_id = t.timetable_id
                        INNER JOIN faculty f ON t.faculty_id = f.uid
                        INNER JOIN course c ON t.course_id = c.course_id
                        WHERE f.dept = ?
                        AND a.attendance_status = 4
                        ORDER BY a.marked_at DESC";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("s", $dept);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        break;
    case 'approveAttendanceLock':
        $sessionId = $_POST['sessionId'];
        $status = 0;
        $query = "UPDATE attendance_session SET attendance_status = ? WHERE session_id = ?";
        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param("ii", $status, $sessionId);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Attendance lock approved successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Attendance lock approval failed'
            ]);
        }
        break;
    case "download_internal":
        if (!isset($_GET['component_id'])) {
            throw new Exception('Component ID not provided');
        }

        $componentId = $_GET['component_id'];

        // Get component and course details
        $query = "SELECT ec.*, c.*,
               
               component_name,conducted_marks
          FROM exam_components ec
          JOIN course c ON ec.course_id = ec.course_id
          WHERE ec.component_id = ?
          LIMIT 1";

        $stmt = $erp_conn->prepare($query);
        $stmt->bind_param('i', $componentId);
        $stmt->execute();
        $component = $stmt->get_result()->fetch_assoc();

        if (!$componentId) {
            throw new Exception('Component not found');
        }

        // Prepare course data
        $courseData = [
            'course_code'   => $component['course_id'],
            'course_name'   => $component['course_name'],
            'print_date'    => date('d-m-Y'),
            'department'    => $component['dept'],
            'batch'         => $component['ayear'],
            'section'       => 'A',
            'exam_type'     => $component['component_name'],
            'max_mark'      => $component['conducted_marks'],
            'faculty_name'  => 'Mr.KALAIARASAN K',
            'exam_date'     => date('d-m-Y', strtotime($component['exam_date']))
        ];

        // Get student marks
        $marksQuery = "SELECT s.sid, s.sname,
                                SUM(m.marks_obtained) as total_marks,
                                m.is_absent
                          FROM student s
                          JOIN marks m ON s.uid = m.student_id
                          WHERE m.component_id = ?
                          GROUP BY s.sid
                          ORDER BY s.sid";

        $stmt = $erp_conn->prepare($marksQuery);
        $stmt->bind_param('i', $componentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'reg_no'    => $row['sid'],
                'name'      => $row['sname'],
                'marks'     => $row['total_marks'],
                'is_absent' => $row['is_absent']
            ];
        }

        // Calculate ranges
        $ranges = [
            '0-9'   => 0,
            '10-19' => 0,
            '20-29' => 0,
            '30-39' => 0,
            '40-44' => 0,
            '45-49' => 0,
            '50-54' => 0
        ];

        $totalMarks = 0;
        $present    = 0;
        $absent     = 0;
        $passCount  = 0;
        $passMarkThreshold = $courseData['max_mark'] * 0.5; // 50% passing mark

        foreach ($students as $student) {
            if ($student['is_absent']) {
                $absent++;
                continue;
            }

            $present++;
            $marks = floatval($student['marks']);
            $totalMarks += $marks;

            // Increment range counters based on the student's mark
            if ($marks >= 0 && $marks <= 9) {
                $ranges['0-9']++;
            } else if ($marks >= 10 && $marks <= 19) {
                $ranges['10-19']++;
            } else if ($marks >= 20 && $marks <= 29) {
                $ranges['20-29']++;
            } else if ($marks >= 30 && $marks <= 39) {
                $ranges['30-39']++;
            } else if ($marks >= 40 && $marks <= 44) {
                $ranges['40-44']++;
            } else if ($marks >= 45 && $marks <= 49) {
                $ranges['45-49']++;
            } else if ($marks >= 50 && $marks <= 54) {
                $ranges['50-54']++;
            }

            if ($marks >= $passMarkThreshold) {
                $passCount++;
            }
        }

        $average = $present ? round($totalMarks / $present, 2) : 0;
        $passPercentage = $present ? round(($passCount / $present) * 100, 2) : 0;

        // Build the output array
        $output = [
            'courseData'    => $courseData,
            'students'      => $students,
            'statistics'    => [
                'average'        => $average,
                'present'        => $present,
                'absent'         => $absent,
                'passCount'      => $passCount,
                'passPercentage' => $passPercentage,
            ],
            'ranges'        => $ranges
        ];

        // Clear any previous output and send JSON response
        echo json_encode([
            'status' => 'success',
            'data'   => $output
        ]);


        break;

    case 'changeFaculty':
        $courseId = $_POST['courseId'];
        $oldFacultyId = $_POST['oldFacultyId'];
        $newFacultyId = $_POST['newFacultyId'];

        // Check if the new faculty ID exists
        $checkQuery = "SELECT COUNT(*) FROM faculty WHERE uid = ?";
        $stmt = $erp_conn->prepare($checkQuery);
        $stmt->bind_param("i", $newFacultyId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close(); // Close the statement after use

        if ($count == 0) {
            echo json_encode(['status' => 'error', 'message' => 'New faculty ID does not exist']);
            exit;
        }

        try {
            // Start transaction
            $erp_conn->begin_transaction();

            // Update course_faculty table for all records with the specified course_id
            $updateQuery = "UPDATE course_faculty 
                            SET faculty_id = ? 
                            WHERE course_id = ? AND faculty_id = ?";

            $stmt = $erp_conn->prepare($updateQuery);
            $stmt->bind_param("iii", $newFacultyId, $courseId, $oldFacultyId);

            if ($stmt->execute()) {
                $stmt->close(); // Close the statement after use

                // Update timetable table for all records with the specified course_id
                $updateTimetableQuery = "UPDATE timetable 
                                          SET faculty_id = ? 
                                          WHERE course_id = ? AND faculty_id = ?";

                $stmt = $erp_conn->prepare($updateTimetableQuery);
                $stmt->bind_param("iii", $newFacultyId, $courseId, $oldFacultyId);
                $stmt->execute();
                $stmt->close(); // Close the statement after use

                // Log the faculty change
                $logQuery = "INSERT INTO faculty_change_log 
                            (course_id, old_faculty_id, new_faculty_id, changed_by, change_date) 
                            VALUES (?, ?, ?, ?, NOW())";

                $stmt = $erp_conn->prepare($logQuery);
                $advisorId = $_SESSION['user']['uid']; // Assuming advisor's ID is stored in session
                $stmt->bind_param("iiii", $courseId, $oldFacultyId, $newFacultyId, $advisorId);
                $stmt->execute();
                $stmt->close(); // Close the statement after use

                $erp_conn->commit();
                echo json_encode(['status' => 'success', 'message' => 'Faculty changed successfully']);
            } else {
                throw new Exception("Failed to update faculty");
            }
        } catch (Exception $e) {
            $erp_conn->rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;
    case "addExamTimeTable":
        $data = json_decode($_POST['data'], true);

    // Validate required fields
    if (!isset($_POST['section'])) {
        echo json_encode(['status' => 'error', 'message' => 'Exam name is required']);
        exit;
    }
    if (!isset($data['courses']) || !is_array($data['courses']) || count($data['courses']) == 0) {
        echo json_encode(['status' => 'error', 'message' => 'At least one exam subject is required']);
        exit;
    }
    
    $examName = trim($data['examName']);
    $courses = $data['courses'];
    $academic_year = $_POST['academicYear'];
    $batch = $_POST["batch"];
    $dept = $_POST['department'];
    $section = $_POST['section'];
    $semester = $_POST['semester'];

    try {
        // Start transaction
        $erp_conn->begin_transaction();

        // Insert into exams table (exam header)
        $insertExamQuery = "INSERT INTO exams (exam_name,created_at,section,batch,department,academicYear,semester) VALUES (?, NOW(),?,?,?,?,?)";
        $stmt = $erp_conn->prepare($insertExamQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $erp_conn->error);
        }
        $stmt->bind_param("ssssss", $examName, $section, $batch, $dept, $academic_year, $semester);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert exam: " . $stmt->error);
        }
        $examId = $erp_conn->insert_id;
        $stmt->close();

        // Prepare statement for inserting exam subjects
        $insertSubjectQuery = "INSERT INTO exam_subjects (exam_id, course_id, course_name, exam_date, exam_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $erp_conn->prepare($insertSubjectQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $erp_conn->error);
        }

        // Loop through each course and insert the exam subject details
        foreach ($courses as $course) {
            // Validate required fields for each course if needed
            if (!isset($course['course_id']) || !isset($course['exam_date']) || !isset($course['exam_time'])) {
                throw new Exception("Missing course data");
            }
            
            // Retrieve values from the course entry
            $courseId = $course['course_id'];
            $courseName = isset($course['course_name']) ? $course['course_name'] : "";
            $examDate = $course['exam_date'];
            $examTime = $course['exam_time'];
           

            $stmt->bind_param("iisss", $examId, $courseId, $courseName, $examDate, $examTime);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert exam subject: " . $stmt->error);
            }
        }
        $stmt->close();

        // Commit transaction
        $erp_conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Exam timetable saved successfully', 'examId' => $examId]);
    } catch (Exception $e) {
        $erp_conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }   
        break;
    case "getExamTimeTables":
        $academicYear = isset($_POST['academicYear']) ? $_POST['academicYear'] : '';
        $batch = isset($_POST['batch']) ? $_POST['batch'] : '';
        $section = isset($_POST['section']) ? $_POST['section'] : '';
        $department = isset($_POST['department']) ? $_POST['department'] : '';
        $semester = isset($_POST['semester']) ? $_POST['semester'] : '';
    
        $timetables = [];
        
        // Build the query with extra filters
        // Ensure the corresponding columns exist in your 'exams' table (or join with another table if needed)
        $query = "SELECT exam_id, exam_name, created_at 
                  FROM exams 
                  WHERE academicYear = ? 
                    AND batch = ? 
                    AND section = ? 
                    AND department = ? 
                    AND semester = ? 
                  ORDER BY created_at DESC";
        
        $stmt = $erp_conn->prepare($query);
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $erp_conn->error]);
            exit;
        }
        
        $stmt->bind_param("sssss", $academicYear, $batch, $section, $department, $semester);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result) {
            while ($exam = $result->fetch_assoc()) {
                $examId = $exam['exam_id'];
                // For each exam, fetch the related subjects
                $subjects = [];
                $subQuery = "SELECT course_id, course_name, exam_date, exam_time 
                             FROM exam_subjects 
                             WHERE exam_id = ?";
                $stmtSub = $erp_conn->prepare($subQuery);
                $stmtSub->bind_param("i", $examId);
                $stmtSub->execute();
                $resultSub = $stmtSub->get_result();
                while ($subject = $resultSub->fetch_assoc()) {
                    $subjects[] = $subject;
                }
                $stmtSub->close();
    
                $exam['courses'] = $subjects;
                $timetables[] = $exam;
            }
        }
        $stmt->close();
    
        echo json_encode(['status' => 'success', 'timetables' => $timetables]);
    break;


    
    case 'deleteExamComponent':
        try {
            if (!isset($_POST['component_id'])) {
                throw new Exception('Component ID not provided');
            }

            $componentId = $_POST['component_id'];

            // First check if the component can be deleted (e.g., not locked, no marks entered)
            $checkQuery = "SELECT status FROM exam_components WHERE component_id = ?";
            $stmt = $erp_conn->prepare($checkQuery);
            $stmt->bind_param('i', $componentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $component = $result->fetch_assoc();

            if (!$component) {
                throw new Exception('Component not found');
            }

            if ($component['status'] !== 'draft') {
                throw new Exception('Cannot delete component - marks already entered or component is locked');
            }

            // If checks pass, delete the component
            $deleteQuery = "DELETE FROM exam_components WHERE component_id = ?";
            $stmt = $erp_conn->prepare($deleteQuery);
            $stmt->bind_param('i', $componentId);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Component deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete component');
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid Action'
        ]);
        break;
}

// SELECT 
//                 o.assigned_day,
//                 t2.period,
//                 t2.course_id,
//                 c.course_name,
//                 c.dept,
//                 c.semester AS course_semester
//             FROM day_order_override o
//             JOIN timetable t2 ON t2.batch = o.batch 
//                 AND t2.semester = o.semester 
//                 AND t2.section = o.section
//                 AND t2.academic_year = o.academic_year
//                 AND t2.day = o.assigned_day
//             JOIN course c ON t2.course_id = c.course_id
//             JOIN course_faculty cf ON c.course_id = cf.course_id 
//                 AND cf.section = o.section
//                 AND cf.faculty_id = ?
//             WHERE o.override_date = ? 
//                 AND o.batch = ? 
//                 AND o.academic_year = ? 
//                 AND o.semester = ? 
//                 AND o.section = ?
//             GROUP BY t2.period, t2.course_id
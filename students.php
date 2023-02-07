<?php

require_once 'connection.php';

// Access action information from the $_SERVER method
$method = $_SERVER['REQUEST_METHOD'];

// GET method for retrieving all student records
if ($method == 'GET') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        get_student($id);
    } else {
        get_students();
    }
}

// POST method for creating a new student record
if ($method == 'POST') {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body, true);
    insert_student($data['id'], $data['student_name'], $data['student_number'], $data['student_age']);
}

// PUT method for updating a student record
if ($method == 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    update_student($data['id'], $data['student_name'], $data['student_number'], $data['student_age']);

}

// DELETE method for deleting a student record
if ($method == 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    delete_student($data['id']);
}

//Method to get all the students using bind param
function get_students() {
    global $conn;
    $sql = "SELECT * FROM student";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = array();
    while ($row = $result->fetch_assoc()) {
    array_push($students, $row);
    }
    echo json_encode($students);
    }
    
    //Method to get a specific student by ID using bind param
    function get_student($id) {
    global $conn;
    $sql = "SELECT * FROM student WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = array();
    while ($row = $result->fetch_assoc()) {
    array_push($student, $row);
    }
    echo json_encode($student);
    }

//Method to insert a student using bind parameters
function insert_student($id, $student_name, $student_number, $student_age) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO student (id, student_name, student_number, student_age) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $id, $student_name, $student_number, $student_age);
    if ($stmt->execute()) {
        echo "New student record created successfully";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}


//Method to update student information using bind parameters
function update_student($id, $student_name, $student_number, $student_age)
{
global $conn;
$stmt = $conn->prepare("UPDATE student SET student_name = ?, student_number = ?, student_age = ? WHERE id = ?");
$stmt->bind_param("ssii", $student_name, $student_number, $student_age, $id);
if ($stmt->execute()) {
echo "Student record updated successfully";
} else {
echo "Error: " . $stmt->error;
}
}

//Method to delete a student using bind parameters
function delete_student($id)
{
global $conn;
$stmt = $conn->prepare("DELETE FROM student WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
echo "Student record deleted successfully";
} else {
echo "Error: " . $stmt->error;
}
}





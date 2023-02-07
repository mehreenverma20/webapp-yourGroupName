<?php

require_once 'connection.php';

// Access action information from the $_SERVER method
$method = $_SERVER['REQUEST_METHOD'];

// GET route for retrieving all student records
if ($method == 'GET') {
    $sql = "SELECT * FROM student";
    $result = mysqli_query($conn, $sql);
    $students = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($students, $row);
    }
    echo json_encode($students);
}

// POST route for creating a new student record
if ($method == 'POST') {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body, true);
    $id = $data['id'];
    $student_name = $data['student_name'];
    $student_number = $data['student_number'];
    $student_age = $data['student_age'];

    $sql = "INSERT INTO student (id, student_name, student_number, student_age) VALUES ('$id', '$student_name', '$student_number', '$student_age')";
    if (mysqli_query($conn, $sql)) {
        echo "New student record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// PUT route for updating a student record
if ($method == 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    update_student($data['id'], $data['student_name'], $data['student_number'], $data['student_age']);

}

// DELETE route for deleting a student record
if ($method == 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    delete_student($data['id']);
}

// Get all students
function get_all_students() {
    global $conn;
    $sql = "SELECT * FROM student";
    $result = $conn->query($sql);
    $students = array();
    while ($row = $result->fetch_assoc()) {
        array_push($students, $row);
    }
    echo json_encode($students);
}

// Get a specific student by ID
function get_student_by_id($id) {
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

// Insert a student
function insert_student($id, $student_name, $student_number, $student_age) {
    global $conn;
    $sql = "INSERT INTO student (id, student_name, student_number, student_age) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $id, $student_name, $student_number, $student_age);
    if ($stmt->execute()) {
        echo "New student record created successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Update student information
function update_student($id, $student_name, $student_number, $student_age) {
    global $conn;
    $sql = "UPDATE student SET student_name = ?, student_number = ?, student_age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $student_name, $student_number, $student_age, $id);
    if ($stmt->execute()) {
        echo "Student record updated successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Delete a student
function delete_student($id)
{
    global $conn;
    $sql = "DELETE FROM student WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Student record deleted successfully";
    }
}
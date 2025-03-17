<?php
session_start();
$idUsuario = $_SESSION['id'];

// Configure headers for AJAX requests
header('Content-Type: application/json');

// Get the JSON data sent
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Verify if the data was correctly decoded
if ($data === null) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
    exit;
}

// Include the results processor class
require_once '../../models/monitor_dao/ResultProcessor.php';

// Create processor instance
$processor = new ResultsProcessor($idUsuario);

// Process the simulation data
$result = $processor->processData($data);

// Close the database connection
$processor->closeConnection();

// Return the result
echo json_encode($result);

?>
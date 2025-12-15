<?php
session_start();
header('Content-Type: application/json');
include __DIR__ . '/../../config/config.php';



if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}
$user_id = $_SESSION["user_id"];

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 4;
$offset = ($page - 1) * $limit;

$sql = "SELECT 
            p.property_id, 
            p.building_id, 
            p.owner_id, 
            p.tenant_id, 
            p.floor, 
            p.number, 
            p.status, 
            p.pet, 
            p.furnished, 
            p.rooms, 
            p.bathrooms, 
            p.parking, 
            p.area, 
            p.details, 
            MIN(pp.photo_path) AS photo_path
        FROM 
            property p
        LEFT JOIN 
            property_photos pp ON p.property_id = pp.property_id 
        WHERE
            p.owner_id=?
        GROUP BY 
            p.property_id 
        LIMIT ?, ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL Error: ' . $mysqli->error]);
    exit;
}

$stmt->bind_param("iii", $user_id, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
$properties = $result->fetch_all(MYSQLI_ASSOC);

$countSql = "SELECT COUNT(DISTINCT property_id) as total FROM property";
$countResult = $mysqli->query($countSql);
$totalCount = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalCount / $limit);

echo json_encode([
    'data' => $properties,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);

?>
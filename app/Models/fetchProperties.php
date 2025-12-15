<?php

header('Content-Type: application/json');
include "../../config/config.php";

if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 4;
$offset = ($page - 1) * $limit;

// Modified SQL query to include a condition on the status
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
            p.status = 'a'  -- Only select properties where status is 'a'
        GROUP BY 
            p.property_id 
        LIMIT ?, ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'SQL Error: ' . $mysqli->error]);
    exit;
}

$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
$properties = $result->fetch_all(MYSQLI_ASSOC);

// Modified count query to account for status filter
$countSql = "SELECT COUNT(DISTINCT property_id) as total FROM property WHERE status = 'a'";
$countResult = $mysqli->query($countSql);
$totalCount = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalCount / $limit);

echo json_encode([
    'data' => $properties,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);

?>
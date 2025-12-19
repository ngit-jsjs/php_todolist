<?php
// lấy sort order
$sort = $_GET['sort'] ?? 'created';
if ($sort === 'deadline') {
    // Sắp xếp: task có deadline gần nhất lên trước, quá hạn xuống cuối, không có deadline xuống cuối cùng
    $orderBy = "CASE 
        WHEN end_time IS NULL THEN 2
        WHEN end_time < NOW() THEN 3
        ELSE 1
        END, 
        
        CASE
            WHEN end_time >= NOW() THEN end_time    
            WHEN end_time < NOW() THEN TIMESTAMPDIFF(SECOND, end_time, NOW())
            ELSE NULL
        END ASC,
        CASE
            WHEN end_time IS NULL THEN created_at
            ELSE NULL
        END DESC
    ";
} else {
    $orderBy = 'created_at DESC';
}
?>
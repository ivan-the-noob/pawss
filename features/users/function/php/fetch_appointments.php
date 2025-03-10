<?php

require '../../../../db.php';

$sql = "
    SELECT DATE(appointment_date) AS date 
    FROM appointment 
    WHERE status IN ('pending', 'waiting', 'on-going')
    GROUP BY email, DATE(appointment_date)
    HAVING COUNT(email) >= 2
";
$result = $conn->query($sql);

$blockedDates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blockedDates[] = $row['date'];
    }
}

echo implode(',', $blockedDates);

$conn->close();
?>

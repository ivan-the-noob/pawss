<?php
require '../../../../db.php';

$sql = "
    SELECT DATE(appointment_date) AS date,
           COUNT(*) as appointment_count
    FROM appointment 
    WHERE status IN ('pending', 'waiting', 'on-going')
    GROUP BY DATE(appointment_date)
    HAVING COUNT(*) >= 9
";
$result = $conn->query($sql);

$blockedDates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Subtract 1 day from the blocked date (your requirement)
        $date = new DateTime($row['date']);
        $date->modify('-1 day');
        $blockedDates[] = $date->format('Y-m-d'); // Store the modified date
    }
}

echo implode(',', $blockedDates);

$conn->close();
?>
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
        // Subtract one day from the blocked date
        $date = new DateTime($row['date']);
        $date->modify('-1 day');
        $blockedDates[] = $date->format('Y-m-d'); // Store the modified date
    }
}

echo implode(',', $blockedDates);

$conn->close();
?>

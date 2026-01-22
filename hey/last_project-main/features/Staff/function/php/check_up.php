<?php
include '../../../../db.php'; 



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_name = $_POST['owner_name'];
    $date = $_POST['date'];
    $address = $_POST['address'];
    $active_number = $_POST['active_number'];
    $pet_name = $_POST['petName'];
    $species = $_POST['species'];
    $color = $_POST['petColor'];
    $pet_birthdate = $_POST['pet_birthdate'];
    $gender = $_POST['gender'];
    $breed = $_POST['breed'];
    $diet = $_POST['diet'];
    $bcs = $_POST['bcs'];
    $stool = $_POST['stool'];
    $chief_complaint = $_POST['chief_complaint'];
    $treatment = $_POST['treatment'];
    $vomiting = $_POST['vomiting'];
    $ticks_fleas = $_POST['ticks_fleas'];
    $lepto = $_POST['lepto'];
    $chw = $_POST['chw'];
    $cpv = $_POST['cpv'];
    $cdv = $_POST['cdv'];
    $cbc = $_POST['cbc'];


    $sql = "INSERT INTO check_up (owner_name, date, address, active_number, pet_name, species, color, pet_birthdate, gender, breed, diet, bcs, stool, chief_complaint, treatment, vomiting, ticks_fleas, lepto, chw, cpv, cdv, cbc) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);  
    }

    $stmt->bind_param("ssssssssssssssssssssss", $owner_name, $date, $address, $active_number, $pet_name, $species, $color, $pet_birthdate, $gender, $breed, $diet, $bcs, $stool, $chief_complaint, $treatment, $vomiting, $ticks_fleas, $lepto, $chw, $cpv, $cdv, $cbc);

    if ($stmt->execute()) {
        header("Location: ../../web/api/check-up.php?message=Data saved successfully!");
        exit(); 
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<?php
    if (isset($_GET['message'])) {
        $message = htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8');

        echo "<script>
            window.onload = function() {
                swal({
                title: 'Success',
                text: '$message',
                icon: 'success',  
                button: 'OK',
            });
            };
        </script>";
    }
    ?>

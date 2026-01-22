<?php
require '../../../../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = $_POST['id'];

    $stmt = $conn->prepare("SELECT product_img, product_name, description, cost, type, quantity FROM product WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentImg = $row['product_img'];
    } else {
        echo "Product not found.";
        exit();
    }

    $productImg = $currentImg;

    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] == 0) {
        $productImg = $_FILES['product_img']['name'];
        $targetDir = "../../../../assets/img/product/";
        $targetFile = $targetDir . basename($productImg);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES['product_img']['tmp_name']);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES['product_img']['size'] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        if ($uploadOk == 1) {
            if (!move_uploaded_file($_FILES['product_img']['tmp_name'], $targetFile)) {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    $productName = $_POST['product_name'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];
    $type = $_POST['type'];
    $quantity = $_POST['quantity'];


    $stmt = $conn->prepare("UPDATE product SET product_img=?, product_name=?, description=?, cost=?, type=?, quantity=? WHERE id=?");
    $stmt->bind_param("sssssii", $productImg, $productName, $description, $cost, $type, $quantity, $productId);


    if ($stmt->execute()) {
      
          header("Location: ../../web/api/product.php?updated=" . urlencode($productName));
        exit(); 
    } else {
        echo "Error updating product: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}

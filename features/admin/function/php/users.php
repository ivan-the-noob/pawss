<?php
include '../../../../db.php';

// Define the number of records per page
$recordsPerPage = 5;

// Determine the current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure the page is at least 1

// Calculate the offset
$offset = ($page - 1) * $recordsPerPage;

// Fetch the records with a LIMIT and OFFSET
$sql = "SELECT id, name, email FROM users LIMIT $recordsPerPage OFFSET $offset";
$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        $count = $offset + 1;
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            echo "<tr>";
            echo "<td>$count</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";

            echo "<td class='d-flex gap-1 justify-content-center'>";
            echo "<button class='btn btn-warning text-white' data-bs-toggle='modal' data-bs-target='#updateModal' data-id='$id' data-name='" . htmlspecialchars($row['name']) . "' data-email='" . htmlspecialchars($row['email']) . "'>Update</button>";
            echo "<form action='../../function/php/delete_user.php' method='POST'>"; 
            echo "<input type='hidden' name='user_id' value='" . $id . "' />";
            echo "<input type='submit' value='Delete' class='btn btn-danger' />";
            echo "</form>";
           
            echo "</td>";

            echo "</tr>";
            $count++;
        }
    } else {
        echo "<tr><td colspan='4'>No users found</td></tr>";
    }
    $result->free();
}

// Get the total number of records
$totalSql = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$totalResult = $conn->query($totalSql);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $recordsPerPage);

$totalResult->free();
?>

<!-- Bootstrap Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateModalLabel">Update User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateForm" action="../../function/php/update_user.php" method="POST">
          <input type="hidden" name="user_id" id="user_id">
          <div class="mb-3">
            <label for="user_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="user_name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="user_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="user_email" name="email" required>
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>




<!-- Include SweetAlert -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
    // Bootstrap 5 modal data population
    const updateModal = document.getElementById('updateModal');
    updateModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const userId = button.getAttribute('data-id');
        const userName = button.getAttribute('data-name');
        const userEmail = button.getAttribute('data-email');

        // Populate modal fields with data
        const modalUserId = updateModal.querySelector('#user_id');
        const modalUserName = updateModal.querySelector('#user_name');
        const modalUserEmail = updateModal.querySelector('#user_email');

        modalUserId.value = userId;
        modalUserName.value = userName;
        modalUserEmail.value = userEmail;
    });
</script>


<script>
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('msg');

    if (message) {
        if (message === "User deleted successfully") {
            swal("Success!", message, "success");
        } else if (message === "Error deleting user") {
            swal("Error!", message, "error");
        }
    }
</script>

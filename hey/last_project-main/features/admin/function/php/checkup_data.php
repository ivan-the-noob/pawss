<?php
include '../../../../db.php'; 


$checkupCards = ''; 


$sql = "SELECT * FROM check_up"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $checkupCards .= "<div class='row checkup-list'>";
    while ($row = $result->fetch_assoc()) {
        $ownerName = htmlspecialchars($row['owner_name'], ENT_QUOTES, 'UTF-8');
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
        $date = htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); 
        $address = htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8');
        $activeNumber = htmlspecialchars($row['active_number'], ENT_QUOTES, 'UTF-8');
        $petName = htmlspecialchars($row['pet_name'], ENT_QUOTES, 'UTF-8');
        $petBirthdate = htmlspecialchars($row['pet_birthdate'], ENT_QUOTES, 'UTF-8');
        $breed = htmlspecialchars($row['breed'], ENT_QUOTES, 'UTF-8');
        $diet = htmlspecialchars($row['diet'], ENT_QUOTES, 'UTF-8');
        $species = htmlspecialchars($row['species'], ENT_QUOTES, 'UTF-8');
        $color = htmlspecialchars($row['color'], ENT_QUOTES, 'UTF-8');
        $gender = htmlspecialchars($row['gender'], ENT_QUOTES, 'UTF-8');
        $bcs = htmlspecialchars($row['bcs'], ENT_QUOTES, 'UTF-8');
        $stool = htmlspecialchars($row['stool'], ENT_QUOTES, 'UTF-8');
        $vomiting = htmlspecialchars($row['vomiting'], ENT_QUOTES, 'UTF-8');
        $ticks_fleas = htmlspecialchars($row['ticks_fleas'], ENT_QUOTES, 'UTF-8');
        $chief_complaint = htmlspecialchars($row['chief_complaint'], ENT_QUOTES, 'UTF-8');
        $treatment = htmlspecialchars($row['treatment'], ENT_QUOTES, 'UTF-8');
        $lepto = htmlspecialchars($row['lepto'], ENT_QUOTES, 'UTF-8');
        $chw = htmlspecialchars($row['chw'], ENT_QUOTES, 'UTF-8');
        $cpv = htmlspecialchars($row['cpv'], ENT_QUOTES, 'UTF-8');
        $cdv = htmlspecialchars($row['cdv'], ENT_QUOTES, 'UTF-8');
        $cbc = htmlspecialchars($row['cbc'], ENT_QUOTES, 'UTF-8');

        $checkupCards .= "
            <div class='col-md-3 mt-2 card-wrapper'>
                <div class='card mb-3'>
                    <div class='card-body'>
                        <h5 class='card-title text-center'><span id='ownerName'>$ownerName</span></h5>
                        <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#checkupModal-$id'>
                            View Details
                        </button>
                    </div>
                </div>
            </div>";

        $checkupCards .= "
        <div class='modal fade' id='checkupModal-$id' tabindex='-1' aria-labelledby='checkupModalLabel-$id' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='checkupModalLabel-$id'>Check-Up Details</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <div class='form-section'>
                            <div class='form-row d-flex'>
                                <div class='form-group col-md-8'>
                                    <div class='form-floating'>
                                        <input type='text' class='form-control' id='ownerName' name='owner_name' value='$ownerName' required readonly>
                                        <label for='ownerName'>Name of Owner:</label>
                                    </div>
                                </div>
                                <div class='form-group col-md-4'>
                                    <div class='form-floating'>
                                        <input type='hidden' name='date' value='" . (isset($date) ? date('Y-m-d', strtotime($date)) : '') . "'>
                                        <input type='text' class='form-control' id='formatted-checkup-date' value='" . (isset($date) ? date('F j, Y', strtotime($date)) : '') . "' readonly>
                                        <label for='formatted-checkup-date'>Date:</label>
                                    </div>
                                </div>
                            </div>
                            <div class='form-row d-flex'>
                                <div class='form-group col-md-7'>
                                    <div class='form-floating'>
                                        <input type='text' class='form-control' id='complete-address' name='address' value='$address' readonly>
                                        <label for='complete-address'>Complete Address:</label>
                                    </div>
                                </div>
                                <div class='form-group col-md-5'>
                                    <div class='form-floating'>
                                        <input type='number' class='form-control' id='active-number' name='active_number' value='$activeNumber' readonly>
                                        <label for='active-number'>Active Number:</label>
                                    </div>
                                </div>
                            </div>
                            <div class='form-row d-flex'>
                                <div class='form-group col-md-6'>
                                    <div class='form-floating'>
                                        <input type='text' class='form-control' id='petName' name='petName' value='$petName' readonly>
                                        <label for='petName'>Pet Name:</label>
                                    </div>
                                </div>
                                <div class='form-group col-md-3'>
                                    <div class='form-floating'>
                                        <input type='text' class='form-control' id='species' name='species' value='" . (isset($species) ? htmlspecialchars($species) : 'Not specified') . "' readonly>
                                        <label for='species'>Species:</label>
                                    </div>
                                </div>
                                <div class='form-group col-md-3'>
                                    <div class='form-floating'>
                                        <input type='text' class='form-control' id='color' name='color' value='$color' readonly>
                                        <label for='color'>Color:</label>
                                    </div>
                                </div>
                            </div>
                            <div class='form-group col-md-4'>
                                <div class='form-floating'>
                                    <input type='hidden' name='pet_birthdate' value='" . date('Y-m-d', strtotime($petBirthdate)) . "'>
                                    <input type='text' class='form-control' id='formatted-birthdate' value='" . date('F j, Y', strtotime($petBirthdate)) . "' readonly>
                                    <label for='formatted-birthdate'>Birthdate:</label>
                                </div>
                            </div>
                            <div class='form-row d-flex'>
                                <div class='form-group col-md-3'>
                                    <div class='form-floating'>
                                        <input type='text' class='form-control' id='sex' name='sex' value='$gender' readonly>
                                        <label for='sex'>Sex:</label>
                                    </div>
                                </div>
                                <div class='form-group col-md-6'>
                                    <div class='form-floating'>
                                        <input type='text' class='form-control' id='breed' name='breed' value='$breed' readonly>
                                        <label for='breed'>Breed:</label>
                                    </div>
                                </div>
                                <div class='form-group col-md-3'>
                                    <div class='form-floating'>
                                        <input type='text' class='form-control' id='diet' name='diet' value='$diet' readonly>
                                        <label for='diet'>Diet:</label>
                                    </div>
                                </div>
                            </div>
                            <div class='container mt-4'>
                                <div class='row'>
                                    <div class='col-md-4 bordered-box'>
                                        <div class='fw-bold phy-weight mb-2'>Physical Examination Weight</div>
                                        <div class='d-flex'>
                                            <div class='mb-2 col-md-6 border-box'>
                                                <p>BCS: $bcs</p>           
                                            </div>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>Stool: $stool</p>       
                                            </div>
                                        </div>
                                        <div class='d-flex'>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>Vomiting: $vomiting</p>    
                                            </div>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>Ticks Fleas: $ticks_fleas</p>    
                                            </div>
                                        </div>
                                        <div class='fw-bold laboratory mb-2'>Laboratory</div>
                                         <div class='d-flex'>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>Fecalysis:</p>    
                                            </div>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                               <p>lepto: " . ($lepto ? $lepto : 'N/A') . "</p>  
                                            </div>
                                        </div>
                                        <div class='d-flex'>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>Ear swab:</p>    
                                            </div>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>CHW: " . ($chw ? $chw : 'N/A') . "</p>
                                            </div>
                                        </div>
                                        <div class='d-flex'>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>Skin Scrape:</p>    
                                            </div>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                  <p>CPV: " . ($cpv ? $cpv : 'N/A') . "</p> 
                                            </div>
                                        </div>
                                        <div class='d-flex'>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>Vaginal Smear:</p>    
                                            </div>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>CDV: " . ($cdv ? $cdv : 'N/A') . "</p> 
                                            </div>
                                        </div>

                                         <div class='d-flex'>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                               <p>CBC: " . ($cbc ? $cbc : 'N/A') . "</p> 
                                            </div>
                                            <div class='mb-2 col-md-6 d-flex border-box'>
                                                <p>ANA/BAB/EHR:</p>    
                                            </div>
                                        </div>
                                        </div>
                                      
                                         <div class='col-md-4 bordered-box '>
                                            <div class='fw-bold chief'>Chief Complaint</div>
                                            <textarea class='form-control mt-2 input-col' name='chief_complaint' rows='18' readonly>$chief_complaint</textarea>
                                        </div>
                                         <div class='col-md-4 bordered-box'>
                                            <div class='fw-bold treatment'>Treatment</div>
                                            <textarea class='form-control mt-2 input-col' name='treatment' rows='18' readonly>$treatment</textarea>
                                        </div>
                                    </div>
                                </div>
                        </div>
                       
                         </div>
                </div>
            </div>
        </div>";
    }
    $checkupCards .= "</div>"; // Close the row div
} else {
    $checkupCards .= "<p>No records found.</p>"; // Append message for no records
}

$conn->close(); 
?>

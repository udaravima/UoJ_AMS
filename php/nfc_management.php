<?php
require_once '../config.php';
include_once ROOT_PATH . '/php/config/Database.php';
include_once ROOT_PATH . '/php/class/User.php';
include_once ROOT_PATH . '/php/class/NFC.php';
include_once ROOT_PATH . '/php/class/Utils.php';

$utils = new Utils();
$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$nfc = new NFC($conn);

// Initialize Lecturer class (required by nav.php > modal_form.php)
include_once ROOT_PATH . '/php/class/Lecturer.php';
$lecr = new Lecturer($conn);

if (!($user->isLoggedIn()) || !$user->isAdmin()) {
    header("Location: " . SERVER_ROOT . "/index.php");
    exit();
}

// Handle Form Submission
$message = '';
$msgType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token Validation
    include_once ROOT_PATH . '/php/class/CSRF.php';
    CSRF::requireValidToken();

    if (isset($_POST['register_card'])) {
        $stdId = $_POST['student_id'];
        $nfcUid = trim($_POST['nfc_uid']);

        if (!empty($stdId) && !empty($nfcUid)) {
            $result = $nfc->registerCard($stdId, $nfcUid);
            $message = $result['message'];
            $msgType = $result['success'] ? 'success' : 'danger';
        } else {
            $message = "Please provide both Student ID and NFC UID.";
            $msgType = 'warning';
        }
    } elseif (isset($_POST['revoke_card'])) {
        $nfcUid = $_POST['nfc_uid_to_revoke'];
        if ($nfc->revokeCard($nfcUid)) {
            $message = "Card revoked successfully.";
            $msgType = 'success';
        } else {
            $message = "Failed to revoke card.";
            $msgType = 'danger';
        }
    }
}

include_once ROOT_PATH . '/php/include/header.php';
echo "<title>NFC Management</title>";
include_once ROOT_PATH . '/php/include/content.php';
$activeDash = 5; // NFC Management navigation index
include_once ROOT_PATH . '/php/include/nav.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">NFC Card Management</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Registration Form -->
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Register New Card</h5>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <?php echo CSRF::getTokenField(); ?>
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select" id="student_id" name="student_id" required>
                                <option value="">Select Student...</option>
                            </select>
                            <div class="form-text">Search by registration number or name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="nfc_uid" class="form-label">NFC UID (Hex)</label>
                            <input type="text" class="form-control" id="nfc_uid" name="nfc_uid" required
                                placeholder="e.g. 04:A2:5C:1B">
                        </div>
                        <button type="submit" name="register_card" class="btn btn-primary w-100">Register Card</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Registered Cards List -->
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Registered Cards</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Reg No</th>
                                    <th>NFC UID</th>
                                    <th>Assigned At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cards = $nfc->getAllCards();
                                if ($cards && $cards->num_rows > 0) {
                                    while ($row = $cards->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['std_fullname'] . "</td>";
                                        echo "<td>" . $row['std_index'] . "</td>";
                                        echo "<td><code>" . $row['nfc_uid'] . "</code></td>";
                                        echo "<td>" . $row['assigned_at'] . "</td>";
                                        echo "<td>
                                                <form action='' method='post' onsubmit=\"return confirm('Are you sure you want to revoke this card?');\">
                                                    " . CSRF::getTokenField() . "
                                                    <input type='hidden' name='nfc_uid_to_revoke' value='" . $row['nfc_uid'] . "'>
                                                    <button type='submit' name='revoke_card' class='btn btn-sm btn-danger'>Revoke</button>
                                                </form>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No NFC cards registered.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const serverRoot = '<?php echo SERVER_ROOT; ?>';

    // Load all students for NFC registration
    fetch(serverRoot + '/php/api/report_data.php?action=get_students')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('student_id');
            data.forEach(student => {
                const option = new Option(student.text, student.id);
                select.add(option);
            });
        })
        .catch(error => console.error('Error loading students:', error));
});
</script>

<?php
include_once ROOT_PATH . '/php/include/footer.php';
?>
<?php
class Utils
{
    public function __construct()
    {

    }

    public function processNameInitials($fullName)
    {
        // Processing Name with Initial
        $nameParts = explode(" ", $fullName);
        $processedName = '';
        foreach ($nameParts as $index => $namePart) {
            if ($index === count($nameParts) - 1) {
                $processedName .= $namePart . ".";
            } else {
                $processedName .= substr($namePart, 0, 1) . ". ";
            }
        }
        return $processedName;
    }

    public function setMessage($message, $type, $color)
    {
        $messageModal = "
        <div class='modal fade' id='log_info' tabindex='-1' aria-labelledby='login-status' aria-hidden='true'>
            <div class='modal-dialog modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h3>AMS Systems</h3>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <div id='login-alert' class='" . (($type != '') ? (($color != '') ? "$type $type-$color" : "$type") : " ") . "'>
                            <p class=''>$message</p>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                        <!-- <button type='button' class='btn btn-primary'>Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
        <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var loginModal = new bootstrap.Modal(document.getElementById('log_info'));
                    loginModal.show();
                });
        </script>
        ";
        return $messageModal;
    }

    public function storeProfilePic($targetDir, $fileId, $nic)
    {
        $targetLocation = $targetDir . (($nic) ? $nic . "_" : "") . "photo." . pathinfo(basename($_FILES[$fileId]["name"])['extension']);

        if (move_uploaded_file($_FILES[$fileId]["tmp_name"], $targetLocation)) {
            return $targetLocation;
        } else {
            return false;
        }
    }
}
?>
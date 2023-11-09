<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">AMS System</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <p>Message</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
</div>
<script>
    function sendMessage(message, level, reload = false) {
        let messageModalBody = document.getElementById('messageModalBody');
        messageModalBody.innerHTML = "<div class='alert alert-" + level + "'><p>" + message + "</p></div>";
        $('#messageModal').modal('show');
        $('#messageModal').on('hidden.bs.modal', function() {
            $('#messageModalBody').empty();
            if (reload) {
                location.reload();
            }
        });
    }
</script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
</script> -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="<?php echo SERVER_ROOT; ?>/js/bootstrap.bundle.min.js">
</script>
<script src="<?php echo SERVER_ROOT; ?>/js/jquery-3.6.0.min.js"></script>
<script src="<?php echo SERVER_ROOT; ?>/js/bootstrap-select.min.js"></script>
</body>

</html>
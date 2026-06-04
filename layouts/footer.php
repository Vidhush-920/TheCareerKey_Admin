        <?php if(!isset($skip_scripts) || !$skip_scripts): ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js"></script>
        <!-- utilities for computing stats used by chart.js -->
        <script src="./assets/js/statmod.js?v=<?php echo time(); ?>"></script>
        <script src="./assets/js/chart.js?v=<?php echo time(); ?>"></script>
        <script src="./assets/js/app.js?v=<?php echo time(); ?>"></script>
        <?php endif; ?>
    </body>
</html>

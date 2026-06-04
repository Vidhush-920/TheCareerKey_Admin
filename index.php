<?php 
    require_once __DIR__ . '/actions/fetch.php';
    include 'layouts/header.php'; 
?>

    <div class="system">
        <div class="admin-layout d-flex">
            <?php include 'layouts/sidebar.php'; ?>
            <div class="main-container">
                <?php include 'layouts/topbar.php'; ?>
                <div class="main-content">
                    <?php include 'pages/dashboard.php'; ?>
                    <?php include 'pages/records.php'; ?>
                    <?php include 'pages/staffs.php'; ?>
                    <?php include 'pages/profile.php'; ?>
                    
                    <div class="copyright">
                        <p>Copyright &copy; <?php echo date("Y"); ?> <strong>VCOT</strong> | Built for You.</p>
                        <p>Developed by <strong>Vidhush Thamilchelvan</strong>, from <strong>Amirda (Pvt) Ltd.</strong></p>
                    </div>
                </div>
                
            </div>
        </div>
        <?php include 'components/confirm-modal.php'; ?>
        <?php include 'components/alert-modal.php'; ?>
        <?php include 'components/pw-change-modal.php'; ?>
    </div>


    <?php include 'layouts/footer.php'; ?>
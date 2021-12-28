<?php
$active_badge_class = "class='badge badge-success'";
?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="far fa-comments"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Alucio Net Messaging Console</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span <?php if ($active_menu == "dashboard") echo $active_badge_class; ?>>Dashboard</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="waconsole/index.php" target="_blank">
            <i style='color:#25d366' class="fab fa-whatsapp"></i>
            <span <?php if ($active_menu == "waconsole") echo $active_badge_class; ?>>Whatsapp Console</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="kirim.php">
            <i class="fas fa-fw fa-comment-alt"></i>
            <span <?php if ($active_menu == "sendwa") echo $active_badge_class; ?>>Kirim Pesan</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="auto_reply.php">
            <i class="fas fa-reply-all"></i>
            <span <?php if ($active_menu == "autoreply") echo $active_badge_class; ?>>Auto Response</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="nomor.php">
            <i class="fas fa-fw fa-phone-alt"></i>
            <span <?php if ($active_menu == "contactmgmt") echo $active_badge_class; ?>>Data Kontak</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="pengaturan.php">
            <i class="fas fa-fw fa-cogs"></i>
            <span <?php if ($active_menu == "settings") echo $active_badge_class; ?>>Pengaturan</span></a>
    </li>
</ul>
<!-- End of Sidebar -->
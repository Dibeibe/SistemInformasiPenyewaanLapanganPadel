<?php
if (isset($_SESSION['pesan_sukses'])) {
    echo '<div class="alert alert-success text-center">'.$_SESSION['pesan_sukses'].'</div>';
    unset($_SESSION['pesan_sukses']);
}

if (isset($_SESSION['pesan_gagal'])) {
    echo '<div class="alert alert-danger text-center">'.$_SESSION['pesan_gagal'].'</div>';
    unset($_SESSION['pesan_gagal']);
}
?>

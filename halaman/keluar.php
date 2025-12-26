<?php
require_once '../konfigurasi/fungsi.php';
require_once '../konfigurasi/otentikasi.php';

logout(); // hapus session dan regenerate id
set_flash('ok', 'Kamu sudah keluar.');

header('Location: masuk.php?logout=1');
exit;

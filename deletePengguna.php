<?php
require 'koneksi.php';

if (!isset($_GET['id'])) {
    die("ID Pengguna tidak diberikan.");
}

$id = $_GET['id'];

pg_query($conn, "DELETE FROM mahasiswa WHERE id_pengguna = $id");
pg_query($conn, "DELETE FROM dosen WHERE id_pengguna = $id");
pg_query($conn, "DELETE FROM kegiatan WHERE id_pengguna = $id");

$result = pg_query($conn, "DELETE FROM pengguna WHERE id_pengguna = $id");

if ($result) {
    echo "Pengguna berhasil dihapus.";
} else {
    echo "Gagal menghapus pengguna.";
}
?>

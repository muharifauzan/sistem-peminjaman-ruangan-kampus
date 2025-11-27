<?php
session_start();
if(!isset($_SESSION['id_pengguna'])){
    header("Location: login.php");
    exit;
}
include 'dbPengguna.php';

$id_ruangan = $_GET['id_ruangan'] ?? null;
if(!$id_ruangan) die("Ruangan tidak ditemukan");

// Submit peminjaman
if(isset($_POST['submit'])){
    $nama_kegiatan = trim($_POST['nama_kegiatan']);
    $tanggal = $_POST['tanggal'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];

    // Insert kegiatan
    $res1 = pg_query_params($conn, 
        "INSERT INTO kegiatan(id_pengguna, nama_kegiatan, tangal_kegiatan) VALUES($1,$2,$3) RETURNING id_kegiatan",
        [$_SESSION['id_pengguna'], $nama_kegiatan, $tanggal]
    );
    $id_kegiatan = pg_fetch_result($res1,0,0);

    // Insert peminjaman, default status = 'menunggu', id_admin = null
    $res2 = pg_query_params($conn,
        "INSERT INTO peminjaman(id_ruangan, id_kegiatan, tanggal_peminjaman, waktu_mulai, waktu_selesai) 
        VALUES($1,$2,$3,$4,$5)",
        [$id_ruangan, $id_kegiatan, $tanggal, $waktu_mulai, $waktu_selesai]
    );

    echo "Peminjaman berhasil diajukan!";
}
?>
<h2>Form Peminjaman Ruangan</h2>
<form method="POST">
    Nama Kegiatan: <input name="nama_kegiatan" required><br>
    Tanggal: <input type="date" name="tanggal" required><br>
    Waktu Mulai: <input type="time" name="waktu_mulai" required><br>
    Waktu Selesai: <input type="time" name="waktu_selesai" required><br>
    <button name="submit">Ajukan Peminjaman</button>
</form>

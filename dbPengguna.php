<?php
$host = 'localhost';
$dbname = 'Peminjaman Ruangan';
$user = 'postgres';
$password = 'sman4tng';
$conn = pg_connect("host=$host dbname='$dbname' user=$user password=$password");

if (!$conn) {
    die("Koneksi gagal: " . pg_last_error());
}

pg_query($conn, "SET search_path TO public");
?>

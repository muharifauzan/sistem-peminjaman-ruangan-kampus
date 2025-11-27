<?php
include 'dbPengguna.php';

$id = $_GET['id'];

$q = pg_query($conn, "
    SELECT * FROM ruangan WHERE id_ruangan = $id
");
$r = pg_fetch_assoc($q);

$fasilitas = pg_query($conn, "
    SELECT f.nama_fasilitas, d.jumlah_tersedia, d.kondisi
    FROM detail_fasiitas d
    JOIN fasilitas f ON f.id_fasilitas = d.id_fasilitas
    WHERE d.id_ruangan = $id
");
$fasilitas = pg_fetch_all($fasilitas);
?>

<h2><?=htmlspecialchars($r['nama_ruangan'])?></h2>
<p><b>Jenis:</b> <?=$r['jenis_ruangan']?></p>
<p><b>Kapasitas:</b> <?=$r['kapasitas']?> orang</p>
<p><b>Biaya:</b> Rp <?=number_format($r['biaya_sewa'],0,',','.')?></p>

<h3>Fasilitas</h3>
<ul>
<?php if($fasilitas){ foreach($fasilitas as $f){ ?>
    <li><?=htmlspecialchars($f['nama_fasilitas'])?> – <?=$f['jumlah_tersedia']?> (<?=$f['kondisi']?>)</li>
<?php }} else { ?>
    <li>Tidak ada fasilitas</li>
<?php } ?>
</ul>

<button onclick="document.getElementById('modal').style.display='none'">Tutup</button>

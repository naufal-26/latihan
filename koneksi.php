<?php
$host = "localhost";
$user = "root";
$pw = "";
$db = "pw_24ti087";

$koneksi = new mysqli($host, $user, $pw, $db);

if ($koneksi->connect_error) { 
    die("koneksi databases gagal: " . $koneksi->connect_error);
} else {
    echo "koneksi database berhasil";
}
?>
<?php

function salam()
{
    echo "Assalamualaikum <br>";
}
salam();

function pembagian(int $a, int $b)
{
    if ($b == 0) {
        echo "Tidak bisa dibagi dengan 0!";
    } else {
        $hasil = $a / $b;
        echo "Hasil pembagian: " . $hasil;
    }
}

// contoh langsung
pembagian(10, 2);

?>

<form method="POST">
    <input type="number" name="angka1" required>
    <input type="number" name="angka2" required>
    <button type="submit" name="kirim">Kirim</button>
</form>

<?php

if (isset($_POST['kirim'])) {
    $angka1 = $_POST['angka1'];
    $angka2 = $_POST['angka2'];

    pembagian($angka1, $angka2);
}
?>
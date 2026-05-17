<?php

function salam()
{
    echo "Assalamualaikum <br>";
}
salam();

function pengurangan(int $a, int $b)
{
    $hasil = $a - $b;
    echo "Hasil pengurangan: " . $hasil;
}

// contoh langsung
pengurangan(10, 2);

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

    pengurangan($angka1, $angka2);
}
?>
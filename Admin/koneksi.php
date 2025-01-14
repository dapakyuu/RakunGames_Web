<?php

$namaserver = "localhost";
$username = "root";
$password = "";
$namadatabase = "rakungames";
$koneksi = mysqli_connect($namaserver, $username, $password, $namadatabase);

if ($db->connect_error) {
    echo "Koneksi database rusak";
    die("error!");
}

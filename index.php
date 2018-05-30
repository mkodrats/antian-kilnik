<?php 
include 'proses.php';

    if (isset($_GET['login'])) {
        
        $norm  = $_GET['norm'];
        $password = $_GET['password'];

        Login($norm, $password);
    }

    if (isset($_GET['daftar'])) {
        $norm      = $_GET['norm'];
        $tgl_lahir = $_GET['tgl_lahir'];
        $password  = $_GET['password'];

        Daftar($norm, $tgl_lahir, $password);
    }

    if (isset($_GET['jadwal'])) {
       LihatJadwal();
    }
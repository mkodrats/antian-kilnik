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

    if (isset($_GET['detailjadwal'])) {
        $kode_ruang = $_GET['kode_ruang'];
        DetailJadwal($kode_ruang);
    }

    if (isset($_GET['daftarperiksa'])) {
        $norm = $_GET['norm'];

        DaftarPeriksa($norm);
    }

    if (isset($_GET['daftarp'])) {
        $norm           = $_GET['norm'];
        $kdcarapem      = $_GET['kdcarapem'];
        // $tgl_daftar     = $_GET['tgl_daftar'];
        $tgl_daftar     = date("Y-m-d H:i:s");
        // $tgl_periksa    = date("Y-m-d H:i:s");
        $tgl_periksa    = $_GET['tgl_periksa'];
        // $date           = $_GET['date'];
        $nokartu        = $_GET['nokartu'];
        $kunjungan      = $_GET['kunjungan'];
        $kode_ruang     = $_GET['kode_ruang'];
        $kode_dokter    = $_GET['kode_dokter'];
        $keluhan        = $_GET['keluhan'];
        SimpanDaftar($norm,$kdcarapem,$tgl_daftar,$tgl_periksa,$nokartu,$kunjungan,$kode_ruang,$kode_dokter,$keluhan);
    }

    if (isset($_GET['poli'])) {
        DaftarPoliklinik();
    }

    if (isset($_GET['dokter'])) {
        $koderuang = $_GET['koderuang'];
        Dokter($koderuang);
    }
    if (isset($_GET['resdaf'])) {
        $norm = $_GET['norm'];
        resDaftar($norm);
    }

    if (isset($_GET['carabayar'])) {
        CaraBayar();
    }
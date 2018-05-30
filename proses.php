<?php 

    function Connection()
    {
        static $conn;
        $conn = mysqli_connect('localhost','root','root','simrsst');
        // $conn = mysqli_connect('den1.mysql2.gear.host','simrsst','Ca80ec!~R32L','simrsst');
        return $conn;
    }

    function Login($norm, $password)
    {
        $conn = Connection();
        
        $sql = "SELECT * FROM t_user WHERE NomorRM = '$norm' AND password = '$password'";
        // echo $sql;

        $res = mysqli_query($conn, $sql);
        $data = [];

        if (mysqli_fetch_row($res) > 0 ) {
            foreach ($res as $key) {
                $data = array(
                    'norm' => $key['NomorRM'],
            ); 
            }
            echo json_encode($data);
        }else {
            echo 'Data Tidak Ada';
        }
        

    }

    function Daftar($norm, $tgl_lahir, $password)
    {
        $conn   = Connection();
        $sql    = "SELECT NomorRM, TglLahir FROM t_pasien WHERE NomorRM = '$norm' AND TglLahir = '$tgl_lahir' ";
        $res    = mysqli_query($conn, $sql);
        $query  = "SELECT NomorRM FROM t_user WHERE NomorRM = '$norm'";
        $result = mysqli_query($conn, $query);
        $tes = mysqli_fetch_row($res);
        // var_dump($tes);

        $alert = [];
        if (mysqli_fetch_row($res) > 0 &&  mysqli_fetch_row($result) == 0) {
            $sql  = "INSERT INTO t_user(NomorRM, password ) VALUES ('$norm', '$password') ";
            $res = mysqli_query($conn, $sql);
            if ($res == true) {
               $alert = array('alert' => 'Daftar Berhasil Silahkan Login');
               echo json_encode($alert);
            }
        }else if (mysqli_fetch_row($result) > 0) {
           $alert = array('alert' => 'Anda Sudah Terdaftar Silahkan Login');
           echo json_encode($alert);
        }else{
            $alert = array(
                'alert' => 'Terjadi Kesalahan'
            );
            echo json_encode($alert);
        }
    }

    function LihatJadwal()
    {
        $conn = Connection();
        $sql = "SELECT * FROM t_ruang";
        $res = mysqli_query($conn, $sql);
        $jsonArray = array();
        while ($row = mysqli_fetch_assoc($res)) {
           $data = array(
               'kode_ruang' => $row['KodeRuang'],
               'ruang'      => $row['Ruang'],
           );
           $jsonArray[]=$data;
        }
        
        echo json_encode($jsonArray);
    }

    // function DetailJadwal($kode_ruang)
    // {
    //     $conn = Connection();
    //     $sql  = "SELECT * 
    //              FROM t_jadwal_dokter, t_ruang, t_dokter
    //              WHERE t_jadwal_dokter.KodeRuang = '$kode_ruang' 
    //                    AND t_ruang.KodeRuang     = '$kode_ruang' 
    //                    AND t_jadwal_dokter.KodeTKesehatan = t_dokter.KodeDokter";
    //     $res = mysqli_query($conn, $sql);
    //     $jsonArray= array();
    //     while ($row = mysqli_fetch_assoc($res)) {
    //         $data = array(
    //             'nama_dokter' => $row['Nama']
    //         );
    //         $jsonArray[]=$data;
    //     }
    //     echo json_encode
    // }
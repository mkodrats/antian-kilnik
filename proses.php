<?php 

    function Connection()
    {
        static $conn;
        // $conn = mysqli_connect('localhost','root','root','simrsst');
        $conn = mysqli_connect('den1.mysql2.gear.host','simrsst','Ca80ec!~R32L','simrsst');
        return $conn;
    }

    function Login($norm, $password)
    {
        $conn = Connection();
        
        $sql = "SELECT * FROM t_user WHERE NomorRM = '$norm' AND password = '$password'";
        $res = mysqli_query($conn, $sql);

        foreach ($res as $key => $value) {
            $norm =  $value[NomorRM];
        }
        echo json_encode($norm);

    }

    function Daftar($norm, $tgl_lahir, $password)
    {
        $conn = Connection();
        $sql = "SELECT NomorRM, TglLahir FROM t_pasien WHERE NomorRM = '$norm' AND TglLahir = '$tgl_lahir' ";

        $res = mysqli_query($conn, $sql);
        if (mysqli_fetch_row($res) == 0) {
           $alert = 'Pasien Belum Terdaftar';
           echo json_encode($alert);
        }
        if (mysqli_fetch_row($res) > 0) {
           
            $sql  = "INSERT INTO t_user(NomorRM, password ) VALUES ('$norm', '$password') ";
            $res = mysqli_query($conn, $sql);
            if ($res == true) {
               $alert =  'Daftar Berhasil Silahkan Login';
               echo json_encode($alert);
            }
        }else{
            $alert = 'Anda Sudah Terdaftar';
            echo json_encode($alert);
        }
    }

    // function Daftar(Type $var = null)
    // {
    //     # code...
    // }
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
        $data = [];

        if (mysqli_fetch_row($res) > 0 ) {
            foreach ($res as $key) {
                $data = array(
                    'norm' => $key['NomorRM'],
            ); 
            }
            echo json_encode($data);
        }else {
            $alert = array(
                'alert' => 'Data Tidak Ada'
            );
            echo json_encode($alert);
        }
        

    }

    function Daftar($norm, $tgl_lahir, $password)
    {
        $conn   = Connection();
        $sql    = "SELECT NomorRM, TglLahir FROM t_pasien WHERE NomorRM = '$norm' AND TglLahir = '$tgl_lahir' ";
        $res    = mysqli_query($conn, $sql);
        $query  = "SELECT NomorRM FROM t_user WHERE NomorRM = '$norm'";
        $result = mysqli_query($conn, $query);
        $tes    = mysqli_fetch_row($result);
        $tes2   = mysqli_fetch_row($res);

        $alert = [];
        if ($tes == 0 &&  $tes2 > 0) {
            $sql  = "INSERT INTO t_user(NomorRM, password ) VALUES ('$norm', '$password') ";
            $res = mysqli_query($conn, $sql);
            if ($res == true) {
               $alert = array('alert' => 'Daftar Berhasil Silahkan Login');
               echo json_encode($alert);
            }
        }else if ($tes2 > 0) {
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

    function DetailJadwal($kode_ruang)
    {
        $conn = Connection();
        $sql  = "SELECT * 
                 FROM t_jadwal_dokter, t_ruang, t_dokter
                 WHERE t_jadwal_dokter.KodeRuang = '$kode_ruang' 
                       AND t_ruang.KodeRuang     = '$kode_ruang' 
                       AND t_jadwal_dokter.KodeTKesehatan = t_dokter.KodeDokter";
        $res = mysqli_query($conn, $sql);
        $jsonArray= array();
        while ($row = mysqli_fetch_assoc($res)) {
            $data = array(
                'nama_dokter' => $row['Nama'],
                'hari'        => $row['hari']
            );
            $jsonArray[]=$data;
        }
        echo json_encode($jsonArray);
    }

    function DaftarPeriksa($norm)
    {
        $conn = Connection();  
        $sql = "SELECT * FROM t_pasien WHERE NomorRM = '$norm'";
        $res = mysqli_query($conn, $sql);
        $jsonArray = array();
            while($row = mysqli_fetch_assoc($res)){
                $data[] = array(
                        'norm'      => $row['NomorRM'],
                        'nama'      => $row['NamaPasien'],
                        'tgl_lahir' => $row['TglLahir']
                );
        }
        
        echo json_encode($data);
    }

    function CaraBayar()
    {
        $conn = Connection();
        $sql = "SELECT * FROM t_carabayar";
        $res = mysqli_query($conn, $sql);
        $jsonArray = array();
        foreach ($res as $key => $value) {
            $data = array(
                        'Kode'          => $value['KodeCaraPembayaran'],
                        'Keterangan'    => $value['Keterangan']
        );
        $jsonArray[]=$data;
        }
        echo json_encode($jsonArray);
    }

    function SimpanDaftar($norm,$kdcarapem,$tgl_daftar,$tgl_periksa,$nokartu,$kode_ruang,$kode_dokter,$keluhan)
    {
        $conn   = Connection();
        $ruang  = "SELECT Inisial FROM t_ruang WHERE KodeRuang = $kode_ruang";
        $ruang  = mysqli_query($conn, $ruang);
        $ruang  = mysqli_fetch_assoc($ruang);
        $ruang  = $ruang['Inisial'];

        $antrian = "SELECT no_antrian FROM t_antrian WHERE no_antrian LIKE '%$ruang%' ORDER BY no_antrian DESC LIMIT 1";

        $antrian = mysqli_query($conn, $antrian);
        $antrian = mysqli_fetch_assoc($antrian);
        $antrian = $antrian['no_antrian'];


        $query  = "SELECT NamaPasien FROM t_pasien WHERE NomorRM = $norm";
                $result = mysqli_query($conn, $query);
                $data   = mysqli_fetch_assoc($result);
                $nama   =  $data['NamaPasien'];
        if (isset($antrian)) {
            // echo $antrian;

            preg_match_all('([0-9]+|[a-zA-Z]+)', "$antrian", $match);
            $antrian = $match[0][1];
            $antrian = $antrian+1;
            $antrian = $ruang . $antrian;
            
            $kunjungan = "SELECT Kunjungan FROM t_pendaftaran WHERE NomorRM = $norm ORDER BY Kunjungan DESC LIMIT 1";
            $kunjungan = mysqli_query($conn, $kunjungan);
            $kunjungan = mysqli_fetch_assoc($kunjungan);
            $kunjungan = $kunjungan['Kunjungan'];
            if (isset($kunjungan)) {
                $kunjungan = $kunjungan+1;
                //insert to tabel antrian
                $tgl_daftar = substr($tgl_daftar, 0, 10);

                $t_antrian = "INSERT INTO `t_antrian`(`id_ruang`, `NomorRM`, `no_antrian`, `tanggal`) 
                              VALUES ('$kode_ruang','$norm','$antrian','$tgl_daftar')";
                $res1       = mysqli_query($conn, $t_antrian);
                
                //insert to daftar pasien
                $sql   = "INSERT INTO `t_pendaftaran`(`NomorRM`, `KodeCaraPembayaran`, `TglDaftar`, `TglPeriksa`, `NoKartu`, `Kunjungan`, `KodeRuang`, `KodeDokter`, `Keluhan`) 
                          VALUES ('$norm','$kdcarapem','$tgl_daftar','$tgl_periksa','$nokartu','$kunjungan','$kode_ruang','$kode_dokter','$keluhan')";
                $res   = mysqli_query($conn, $sql);
                
                if (isset($res) && isset($res1)) {
                    echo json_encode(array('alert' => "Pasien $nama Berhasil Disimpan"));
                }else {
                    echo json_encode(array('alert' => 'Form Belum Lengkap'));
                }

            }else{
                $kunjungan = 1;

                $tgl_daftar = substr($tgl_daftar, 0, 10);

                $t_antrian = "INSERT INTO `t_antrian`(`id_ruang`, `NomorRM`, `no_antrian`, `tanggal`) 
                              VALUES ('$kode_ruang','$norm','$antrian','$tgl_daftar')";
                $res1       = mysqli_query($conn, $t_antrian);

                $sql   = "INSERT INTO `t_pendaftaran`(`NomorRM`, `KodeCaraPembayaran`, `TglDaftar`, `TglPeriksa`, `NoKartu`, `Kunjungan`, `KodeRuang`, `KodeDokter`, `Keluhan`) 
                          VALUES ('$norm','$kdcarapem','$tgl_daftar','$tgl_periksa','$nokartu','$kunjungan','$kode_ruang','$kode_dokter','$keluhan')";
                $res   = mysqli_query($conn, $sql);
                if (isset($res) && isset($res1)) {
                    echo json_encode(array('alert' => "Pasien $nama Berhasil Disimpan"));
                }else {
                    echo json_encode(array('alert' => 'Form Belum Lengkap'));
                }
            }
            
        }else{
            $antrian = "$ruang" . 1;

            $kunjungan = "SELECT Kunjungan FROM t_pendaftaran WHERE NomorRM = $norm ORDER BY Kunjungan DESC LIMIT 1";
            $kunjungan = mysqli_query($conn, $kunjungan);
            $kunjungan = mysqli_fetch_assoc($kunjungan);
            $kunjungan = $kunjungan['Kunjungan'];
            if (isset($kunjungan)) { 
                $kunjungan = $kunjungan+1;
                //insert to tabel antrian
                $tgl_daftar = substr($tgl_daftar, 0, 10);

                $t_antrian = "INSERT INTO `t_antrian`(`id_ruang`, `NomorRM`, `no_antrian`, `tanggal`) 
                              VALUES ('$kode_ruang','$norm','$antrian','$tgl_daftar')";
                $res1       = mysqli_query($conn, $t_antrian);
                
                //insert to daftar pasien
                $sql   = "INSERT INTO `t_pendaftaran`(`NomorRM`, `KodeCaraPembayaran`, `TglDaftar`, `TglPeriksa`, `NoKartu`, `Kunjungan`, `KodeRuang`, `KodeDokter`, `Keluhan`) 
                          VALUES ('$norm','$kdcarapem','$tgl_daftar','$tgl_periksa','$nokartu','$kunjungan','$kode_ruang','$kode_dokter','$keluhan')";
                $res   = mysqli_query($conn, $sql);
                if (isset($res) && isset($res1)) {
                    echo json_encode(array('alert' => "Pasien $nama Berhasil Disimpan"));
                }else {
                    echo json_encode(array('alert' => 'Form Belum Lengkap'));
                }

            }else{
                $kunjungan = 1;

                $tgl_daftar = substr($tgl_daftar, 0, 10);

                $t_antrian = "INSERT INTO `t_antrian`(`id_ruang`, `NomorRM`, `no_antrian`, `tanggal`) 
                              VALUES ('$kode_ruang','$norm','$antrian','$tgl_daftar')";
                $res1       = mysqli_query($conn, $t_antrian);

                $sql   = "INSERT INTO `t_pendaftaran`(`NomorRM`, `KodeCaraPembayaran`, `TglDaftar`, `TglPeriksa`, `NoKartu`, `Kunjungan`, `KodeRuang`, `KodeDokter`, `Keluhan`) 
                          VALUES ('$norm','$kdcarapem','$tgl_daftar','$tgl_periksa','$nokartu','$kunjungan','$kode_ruang','$kode_dokter','$keluhan')";
                $res   = mysqli_query($conn, $sql);

                if (isset($res) && isset($res1)) {
                    echo json_encode(array('alert' => "Pasien $nama Berhasil Disimpan"));
                }else {
                    echo json_encode(array('alert' => 'Form Belum Lengkap'));
                }
            }
            

           
        }
        
  

        // if ($res ==  true) {
        //    echo json_encode(array('alert' => "Pasien $nama Berhasil Disimpan"));
        
    }

    function DaftarPoliklinik()
    {
        $conn = Connection();
        $sql = "SELECT * FROM t_ruang";
        $res = mysqli_query($conn, $sql);
        $jsonArray = array();
        foreach ($res as $key => $value) {
            $data = array(
                'KodeRuang' => $value['KodeRuang'],
                'Ruang'     => $value['Ruang']
            ); 
            $jsonArray[]=$data;
        }
        echo json_encode($jsonArray);
    }

    function Dokter($koderuang)
    {
        $conn = Connection();
        $sql  = "SELECT * 
        FROM t_ruangdokter, t_ruang, t_dokter
        WHERE t_ruangdokter.KodeRuang = '$koderuang' 
            AND t_ruang.KodeRuang     = '$koderuang' 
            AND t_ruangdokter.KodeDokter = t_dokter.KodeDokter";
        $res  = mysqli_query($conn, $sql);
        $jsonArray = array();
        foreach ($res as $key => $value) {
            $data = array(
                'KodeRuang'  => $value['KodeRuang'],
                'KodeDokter' => $value['KodeDokter'],
                'Nama'       => $value['Nama']
            );
            $jsonArray[]=$data;
        }
        echo json_encode($jsonArray);
    }

    function resDaftar($norm)
    {
        $conn = Connection();
        $sql  = "SELECT * FROM t_pasien, t_pendaftaran WHERE t_pasien.NomorRm = $norm AND t_pendaftaran.NomorRM = $norm ORDER BY TglDaftar DESC LIMIT 1";
        $res  = mysqli_query($conn, $sql);
        $json= array();
        foreach ($res as $key => $value) {
            $data = array(
                'id_pendaftaran'        => $value['id_pendaftaran'],
                'NomorRM'               => $value['NomorRM'],
                'KodeCaraPembayaran'    => $value['KodeCaraPembayaran'],
                'TglDaftar'             => $value['TglDaftar'],
                'TglPeriksa'            => $value['TglPeriksa'],
                'JamPeriksa'            => $value['JamPeriksa'],
                'NoKartu'               => $value['NoKartu'],
                'Kunjungan'             => $value['Kunjungan'],
                'KodeRuang'             => $value['KodeRuang'],
                'KodeDokter'            => $value['KodeDokter'],
                'NamaPasien'            => $value['NamaPasien']
            );
            $json[]=$data;
            
        }
        echo json_encode($json);
    }

    
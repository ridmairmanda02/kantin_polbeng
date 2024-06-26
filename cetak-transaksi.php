<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Cetak Rekapitulasi Transaksi</title>
    <link rel="shorcut icon" type="text/css" href="img/logo-02-min.jpg">
    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap-4_4_1.min.css"/>
    <style>
      tr>th{text-align: center; height: 35px; border: 2px solid;}
      tr>td{padding-left: 5px; vertical-align: middle!important;}
      tr>td>img{margin-top: 3px; margin-bottom: 3px;}
    </style>
  </head>
  <body onload="window.print(); window.onafterprint = window.close; ">
    <?php 
    include "koneksi.php";
    $periodeDari   = $_POST['periodeDari'];
    $periodeSampai = $_POST['periodeSampai'];
    $tgl1=date_create($periodeDari);
    $tgl2=date_create($periodeSampai);
    $kode_member   = $_POST['kode_member'];
    if($kode_member!=""){
      $sql = "SELECT * FROM tbl_member WHERE kode_member = '$kode_member' AND kode = 1 ORDER BY nama_member";
      $query  = mysqli_query($koneksi, $sql);
      $d      = mysqli_fetch_array($query);
      $nama_member = $d['nama_member'];
    }?>

    <span style="margin-left: 25px; font-size: 20px; font-weight:bold;">REKAPITULASI TRANSAKSI</span><br>
    <span style="margin-left: 25px; font-size: 16px;">Periode dari tanggal: <?= date_format($tgl1, 'd M Y');?> s.d. tanggal: <?= date_format($tgl2, 'd M Y');?></span>
    <?php 
    if($kode_member!=""){?><br>
      <span style="margin-left: 25px; font-size: 16px;">Nama Member Mahasiswa: <?= $nama_member;?></span>
      <?php
    }?>
    <table class="table table-bordered table-hover mb-5 ml-4" style="width: 95%;">
      <thead>
        <tr class="text-center">
          <th width="5%">No.</th>
          <th width="10%">Tgl</th>
          <th width="15%">No Transaksi</th>
          <?php 
          if($kode_member==""){?>
            <th>Nama Member</th>
            <?php 
          }?>
          <th>Detail</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $ttl = 0;
        if($kode_member==""){
          $sql = "SELECT * FROM tbl_transaksi a INNER JOIN tbl_member b ON a.kode_member = b.kode_member WHERE a.tgl_transaksi >= '$periodeDari' AND a.tgl_transaksi <= '$periodeSampai' ORDER BY a.tgl_transaksi, a.no_transaksi";
        }else{
          $sql = "SELECT * FROM tbl_transaksi a INNER JOIN tbl_member b ON a.kode_member = b.kode_member WHERE a.tgl_transaksi >= '$periodeDari' AND a.tgl_transaksi <= '$periodeSampai' AND a.kode_member = '$kode_member' AND b.kode = 1 ORDER BY a.tgl_transaksi, a.no_transaksi";
        }
        $query = mysqli_query($koneksi, $sql);
        if($a=mysqli_num_rows($query)>0){
          while ($data = mysqli_fetch_array($query)) {
            $no_transaksi = $data['no_transaksi'];
            $nama_member   = $data['nama_member'];
            $ttl = $ttl + $data['total_transaksi'];
            $tanggal      = date_create($data['tgl_transaksi']); ?>
            <tr>
              <td align="center"><?= $no++; ?>.</td>
              <td align="center"><?= date_format($tanggal, "d-m-Y"); ?></td>
              <td><?= $no_transaksi; ?></td>
              <?php 
              if($kode_member==""){?>
                <td><?= $nama_member; ?></td>
                <?php 
              }?>
              <td>
                <table class="table table-bordered table-sm">
                  <thead>
                    <tr class="text-center">
                      <th width="5%">No.</th>
                      <th>Nama Menu</th>
                      <th>Harga</th>
                      <th>Qty</th>
                      <th>Sub</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $nomer = 1;
                    $sql1 = "SELECT a.harga, a.qty, b.nama_menu FROM tbl_transaksi_detail a INNER JOIN tbl_menu b ON a.id_menu = b.id_menu WHERE a.no_transaksi = '$no_transaksi' ORDER BY a.id_detail";
                    $query1 = mysqli_query($koneksi, $sql1);
                    while ($data1 = mysqli_fetch_array($query1)) { ?>
                      <tr>
                        <td align="center"><?= $nomer++; ?>.</td>
                        <td><?= $data1['nama_menu']; ?></td>
                        <td align="right"><?= number_format($data1['harga']); ?></td>
                        <td align="right"><?= number_format($data1['qty']); ?></td>
                        <td align="right"><?= number_format($data1['harga'] * $data1['qty']); ?>
                        </td>
                      </tr>
                      <?php
                    } ?>
                  </tbody>
                </table>
              </td>
              <td align="right"><?= number_format($data['total_transaksi']); ?></td>
            </tr>
            <?php
          }?>
          <tr>
            <td align="right" colspan="4">Total </td>
            <td align="right"><?= number_format($ttl); ?></td>
            <?php 
            if($kode_member==""){?>
              <td></td>
              <?php 
            }?>
          </tr>
          <?php
        }else{?>
          <tr>
              <td align="center" colspan="7"><b>DATA TIDAK DITEMUKAN</b></td>
          </tr>
          <?php
        }?>
      </tbody>
    </table>
  </body>
</html>


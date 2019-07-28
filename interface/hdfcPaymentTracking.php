<?php
$config = parse_ini_file(__DIR__ . "/../../config/database.ini", true);

// Create connection
$conn = new mysqli($config['DATA_BASE']['HOST'], $config['DATA_BASE']['USERNAME'], $config['DATA_BASE']['AUTHENTICATION'], $config['DATA_BASE']['DATABASENAME']);
// Check connection
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

if (count($_GET) > 0) {
   $request_id        = $_GET['requestId'];
   $transactionId     = $_GET['transactionId'];
   $status            = $_GET['status'];
   $product           = $_GET['product'];
   $requestedDate     = $_GET['requestedDate'];

   $sql    = "SELECT *
   FROM common_pg_hash_validation_master";
   $flag = false;
   foreach ($_GET as $key => $value) {
      if (!empty($value)) {
         $sql    = "SELECT * FROM common_pg_hash_validation_master WHERE";
         $flag   = true;
      }
   }
   if ($flag && !empty($request_id)) {
      $sql .= " request_id = '" . $request_id . "' AND ";
   }

   if ($flag && !empty($transactionId)) {
      $sql .= " txn_id = '" . $transactionId . "' AND ";
   }

   if ($flag && !empty($status)) {
      $sql .= " payment_status = '" . $status . "' AND ";
   }

   if ($flag && !empty($product)) {
      $sql .= " product_info = '" . $product . "' AND ";
   }

   if ($flag && !empty($requestedDate)) {
      $requestedDate  = explode(' - ', $requestedDate);
      $sql .= " request_time BETWEEN '" . $requestedDate[0] . "' AND '" . $requestedDate[1] . "' AND ";
   }

   if ($flag == true) {
      $sql = substr($sql, 0, -4);
   }

   $result = $conn->query($sql);

   $dataArray = array();
   if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
         $dataArray[] = $row;
      }
   } 
   if (mysqli_error($conn)) {
      $message = "MySql Query ERROR";
      echo "<script type='text/javascript'>alert('$message');</script>";
      echo mysqli_error($conn);
   }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <title>CommonWebservice Interface</title>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="icon" href="http://infinitisoftware.net/images/favicon.ico" type="image/x-icon" />
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
   <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" />
   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap.min.css" />

   <style>
      .select2-container .select2-selection--single {
         height: 35px;
      }

      .select2-container--default .select2-selection--single .select2-selection__rendered {
         height: 35px;
      }
   </style>
</head>

<body>
   <?php require_once "./menuNavbar.php"; ?>
   <div class="container-fluid" style="margin-top: 100px">
      <div class="row">
         <form action="hdfcPaymentTracking.php">
            <div class="col-lg-12">
               <div class="col-lg-3">
                  <div class="form-group">
                     <label for="sel1">Request ID:</label>
                     <input type="text" class="form-control" name="requestId" id="requestId" />
                  </div>
               </div>
               <div class="col-lg-3">
                  <div class="form-group">
                     <label for="sel1">Transaction Id:</label>
                     <input type="text" class="form-control" name="transactionId" id="transactionId" />
                  </div>
               </div>
               <div class="col-lg-3">
                  <div class="form-group">
                     <label for="sel1">Status:</label>
                     <select class="form-control" id="status" name="status">
                        <option value="">Select Status</option>
                        <option value="Y">Success</option>
                        <option value="N">Failure</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-3">
                  <div class="form-group">
                     <label for="sel1">Product:</label>
                     <select class="form-control" id="product" name="product">
                        <option value="">Select Status</option>
                        <option value="ATYOURPRICE">ATYOURPRICE</option>
                        <option value="ATYOURPRICE1">ATYOURPRICE1</option>
                        <option value="ATYOURPRICE2">ATYOURPRICE2</option>
                        <option value="PERSONAL">PERSONAL</option>
                     </select>
                  </div>
               </div>
               <div class="col-lg-3">
                  <div class="form-group">
                     <label for="sel1">Payment Date</label>
                     <input type="text" class="form-control" name="requestedDate" id="dateTimePicker1" />
                  </div>
               </div>
            </div>
            <div class="col-lg-5"></div>
            <div class=" col-lg-2">
               <div class="form-group" style="padding-top: 25px;align-content: right;">
                  <label for="sel1"></label>
                  <input type="submit" value="Search" class="btn btn-primary">
               </div>
            </div>
            <div class="col-lg-3"></div>
         </form>
      </div>
      <table id="example" class="table table-striped table-bordered" style="width:100%;">
         <thead style="font-size : 14px;">
            <tr>
               <th>Request Id</th>
               <th>Transaction Id</th>
               <th>Product</th>
               <th>Request Amount</th>
               <th>Response Amount</th>
               <th>Request Time</th>
               <th>Response Time</th>
               <th>PaymentStatus</th>
               <th>ValidationStatus</th>
               <th>ErrorMessage</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($dataArray as $key => $value) {  ?>
               <tr>
                  <td><?php echo $value['request_id'] ?></td>
                  <td><?php echo $value['txn_id'] ?></td>
                  <td><?php echo $value['product_info'] ?></td>
                  <td><?php echo $value['request_amount'] ?></td>
                  <td><?php echo $value['response_amount'] ?></td>
                  <td><?php echo $value['request_time'] ?></td>
                  <td><?php echo $value['response_time'] ?></td>
                  <td><?php echo $value['payment_status'] ?></td>
                  <td><?php echo $value['validated_status'] ?></td>
                  <td><?php echo $value['error_message'] ?></td>
                  <h1>
               </tr>
            <?php }  ?>
         </tbody>
      </table>
   </div>
   <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
   <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
   <script defer type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

   <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
   <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
   <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
   <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
   <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
   <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
</body>
<script>
   $(document).ready(function() {
      $('#select1,#select2,#select3,#select4').select2();
      $('#dateTimePicker,#dateTimePicker1').daterangepicker({
         timePicker: true,
         timePickerSeconds: true,
         startDate: moment().format(),
         endDate: moment().add(1, 'seconds').format(),
         locale: {
            format: 'YYYY-MM-DD HH:mm:ss',
            cancelLabel: 'Clear'
         }
      });
      setTimeout(function() {
         $('#dateTimePicker,#dateTimePicker1').val('');
      }, 100);

      $('#dateTimePicker,#dateTimePicker1').on('cancel.daterangepicker', function(ev, picker) {
         $(this).val('');
      });
   });

   $(document).ready(function() {
      $('#sel1').select2();
      $("#checkbox").click(function() {
         if ($("#checkbox").is(':checked')) {
            $("#sel1 > optgroup > option").prop("selected", "selected"); // Select All Options
            $("#sel1").trigger("change"); // Trigger change to select 2
         } else {
            $("#sel1 > optgroup > option").removeAttr("selected");
            $("#sel1").trigger("change"); // Trigger change to select 2
         }
      });
   });
   $(document).ready(function() {
      var table = $('#example').DataTable({
         lengthChange: true,
         buttons: ['copy', 'excel', 'pdf', 'print', 'colvis']
      });

      table.buttons().container()
         .appendTo('#example_wrapper .col-sm-6:eq(0)');
   });


   <?php
   if (!empty($_GET['requestId'])) {
      echo "$('#requestId').val('" . $_GET['requestId'] . "');";
   }

   if (!empty($_GET['transactionId'])) {
      echo "$('#transactionId').val('" . $_GET['transactionId'] . "');";
   }

   if (!empty($_GET['status'])) {
      echo "$('#status').val('" . $_GET['status'] . "');";
   }

   if (!empty($_GET['product'])) {
      echo "$('#product').select2().val('" . $_GET['product'] . "').trigger('change');";
   }

   if (!empty($_GET['dateTimePicker1'])) {
      $dt = explode(' - ', $_GET['dateTimePicker1']);
      echo 'setTimeout(function(){';
      echo "$('#dateTimePicker1').data('daterangepicker').setStartDate('" . $dt[0] . "');";
      echo "$('#dateTimePicker1').data('daterangepicker').setEndDate('" . $dt[1] . "');";
      echo '},100);';
   }
   ?>
</script>

</html>
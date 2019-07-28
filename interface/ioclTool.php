<?php
$UID_Number = 0;
$dataArray = array();

$config = parse_ini_file(__DIR__ . "/../../config/database.ini", true);
$conn = new mysqli($config['DATA_BASE']['HOST'], $config['DATA_BASE']['USERNAME'], $config['DATA_BASE']['AUTHENTICATION'], $config['DATA_BASE']['DATABASENAME']);
// Check connection
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT
      csm.service_name,
      csm.service_id
   FROM
      core_agency_service_mapping_master casmm,
      core_service_master csm,
      core_agency_reservation_mapping_master cgrmm,
      core_travel_mode_master ctmm
   WHERE
      ctmm.travel_mode_id = cgrmm.travel_mode_id AND
      cgrmm.agency_reservation_id = casmm.agency_reservation_id AND
      casmm.service_id = csm.service_id AND
      ctmm.travel_mode_name = 'IOCL'";
$result = $conn->query($sql);

$dataArray = array();
if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
      $serviceNameArray[$row['service_id']] = $row['service_name'];
   }
}
if (mysqli_error($conn)) {
   $message = "MySql Query ERROR";
   echo "<script type='text/javascript'>alert('$message');</script>";
   echo mysqli_error($conn);
}

if (count($_GET) > 0) {
   $serviceName = $_GET['serviceName'];
   $UIDnumber = $_GET['UIDnumber'];
   $serviceId = $_GET['serviceId'];
   $serviceRequestId = $_GET['serviceRequestId'];
   $tripNo = $_GET['tripNo'];
   $ioclUidNumber = $_GET['ioclUidNumber'];
   $status = $_GET['status'];
   $errorMessage = $_GET['errorMessage'];
   $reqDateTime = $_GET['reqDateTime'];
   $ackDateTime = $_GET['ackDateTime'];

   $sql = "SELECT *
      FROM common_iocl_log_details";
   $flag = false;
   foreach ($_GET as $key => $value) {
      if (!empty($value)) {
         $sql = "SELECT * FROM common_iocl_log_details WHERE";
         $flag = true;
      }
   }
   if ($flag && !empty($UIDnumber)) {
      $sql .= " UID_Number = '" . $UIDnumber . "' AND ";
   }

   if ($flag && !empty($serviceName)) {
      $sql .= " service_id = '" . $serviceName . "' AND ";
   }

   if ($flag && !empty($serviceId)) {
      $sql .= " service_id = '" . $serviceId . "' AND ";
   }

   if ($flag && !empty($serviceRequestId)) {
      $sql .= " service_request_id = '" . $serviceRequestId . "' AND ";
   }

   if ($flag && !empty($tripNo)) {
      $sql .= " trip_no = '" . $tripNo . "' AND ";
   }

   if ($flag && !empty($ioclUidNumber)) {
      $sql .= " IOCL_UID_Number = '" . $ioclUidNumber . "' AND ";
   }

   if ($flag && !empty($status)) {
      $sql .= " return_status = '" . $status . "' AND ";
   }

   if ($flag && !empty($errorMessage)) {
      $sql .= " error_message  LIKE '%" . $errorMessage . "%' AND ";
   }

   if ($flag && !empty($reqDateTime)) {
      $reqDateTime = explode(' - ', $reqDateTime);
      $sql .= " requested_date_time BETWEEN '" . $reqDateTime[0] . "' AND '" . $reqDateTime[1] . "' AND ";
   }

   if ($flag && !empty($ackDateTime)) {
      $ackDateTime = explode(' - ', $ackDateTime);
      $sql .= " ack_date_time BETWEEN '" . $ackDateTime[0] . "' AND '" . $ackDateTime[1] . "' AND ";
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
   } else {
      echo "0 results";
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
         <form action="ioclTool.php">
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">Select Service list:</label>
                  <select class="form-control serviceName" name="serviceName" id="serviceName">
                     <option class="" id="" value="">Select Service List</option>
                     <?php foreach ($serviceNameArray as $key => $value) {
                        echo "<option value='" . $key . "'>" . $value . "</option>";
                     }
                     ?>
                  </select>
               </div>
            </div>
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">UID Number:</label>
                  <input type="text" class="form-control" name="UIDnumber" id="UIDnumber" />
               </div>
            </div>
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">Service Id:</label>
                  <input type="text" class="form-control" name="serviceId" id="serviceId" />
               </div>
            </div>
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">Service request Id:</label>
                  <input type="text" class="form-control" name="serviceRequestId" id="serviceRequestId" />
               </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">Trip no:</label>
                  <input type="text" class="form-control" name="tripNo" id="tripNo" />
               </div>
            </div>
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">IOCL UID number</label>
                  <input type="text" class="form-control" name="ioclUidNumber" id="ioclUidNumber" />
               </div>
            </div>
            <!-- <div class="clearfix"></div> -->
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">Status</label>
                  <select class="form-control status" id="status" name="status">
                     <option id="" value="">Select Status</option>
                     <option value="S">S</option>
                     <option value="F">F</option>
                  </select>
               </div>
            </div>
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">Error Message</label>
                  <input type="text" class="form-control" name="errorMessage" id="errorMessage" />
               </div>
            </div>
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">Requested date time</label>
                  <input type="text" class="form-control" name="reqDateTime" id="dateTimePicker" />
               </div>
            </div>
            <div class="col-lg-3">
               <div class="form-group">
                  <label for="sel1">Acknowledgment date time</label>
                  <input type="text" class="form-control" name="ackDateTime" id="dateTimePicker1" />
               </div>
            </div>
            <div class="col-lg-12">
               <div class="form-group text-center">
                  <input type="submit" value="Search" class="btn btn-primary">
               </div>
            </div>
         </form>
      </div>
      <?php if (!empty($dataArray)) : ?>
         <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
               <thead>
                  <tr>
                     <th>Service Id</th>
                     <th>UID number</th>
                     <th>Service Id</th>
                     <th>Service request Id</th>
                     <th>Trip no</th>
                     <th>IOCL UID Number</th>
                     <th>return status</th>
                     <th>error message</th>
                     <th>requested_date_time</th>
                     <th>ack_date_time</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($dataArray as $key => $value) {
                     $date = explode(" ", $value['requested_date_time']);
                     $month = strtotime($date[0]);
                     $month = strtoupper(date('M ', $month));
                     $year = date_parse_from_format("Y-m-d", $date[0]);
                     $months = str_replace(" ", "", $month . '_' . $year['year']);
                     ?>
                     <tr>
                        <td><?php echo $value['service_id'] ?></td>
                        <td><?php echo $value['UID_Number'] ?></td>
                        <td><?php echo $value['service_id'] ?></td>
                        <td><?php echo $value['service_request_id'] ?></td>
                        <td><?php echo $value['trip_no'] ?></td>
                        <td><?php echo $value['IOCL_UID_Number'] ?></td>
                        <td><?php echo $value['return_status'] ?></td>
                        <td><?php echo $value['error_message'] ?></td>
                        <td><?php echo $value['requested_date_time'] ?></td>
                        <td><?php echo $value['ack_date_time'] ?></td>
                        <td><?php echo '<a href="http://' . $_SERVER['HTTP_HOST'] . '/CommonWebServices1.0/application/data/interface/FileWriteViewer.php?reservation=SAP&mode=IOCL&version=V1&months=' . $months . '&requestId=' . $value['service_request_id'] . '&dateTimePicker=" class="btn btn-primary text-center badge" target="_blank">View File Write</a>' ?></td>
                        <h1>
                     </tr>
                  <?php } ?>
               </tbody>
               <tfoot>
                  <tr>
                     <th>Service name</th>
                     <th>UID number</th>
                     <th>Service Id</th>
                     <th>Service request Id</th>
                     <th>trip no</th>
                     <th>IOCL UID Number</th>
                     <th>return status</th>
                     <th>error message</th>
                     <th>requested_date_time</th>
                     <th>ack_date_time</th>
                     <th>Action</th>
                  </tr>
               </tfoot>
            </table>
         </div>
      <?php endif; ?>
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
   <link rel="icon" href="http://infinitisoftware.net/images/favicon.ico" type="image/x-icon" />
</body>
<script>
   $(document).ready(function() {
      $('#serviceName').select2();
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
   if (!empty($_GET['UIDnumber'])) {
      echo "$('#UIDnumber').val('" . $_GET['UIDnumber'] . "').trigger('change');";
   }

   if (!empty($_GET['serviceName'])) {
      echo "$('#serviceName').val('" . $_GET['serviceName'] . "').trigger('change');";
   }

   if (!empty($_GET['serviceId'])) {
      echo "$('#serviceId').val('" . $_GET['serviceId'] . "').trigger('change');";
   }

   if (!empty($_GET['serviceRequestId'])) {
      echo "$('#serviceRequestId').val('" . $_GET['serviceRequestId'] . "').trigger('change');";
   }

   if (!empty($_GET['tripNo'])) {
      echo "$('#tripNo').val('" . $_GET['tripNo'] . "').trigger('change');";
   }

   if (!empty($_GET['ioclUidNumber'])) {
      echo "$('#ioclUidNumber').val('" . $_GET['ioclUidNumber'] . "').trigger('change');";
   }

   if (!empty($_GET['status'])) {
      echo "$('#status').val('" . $_GET['status'] . "').trigger('change');";
   }

   if (!empty($_GET['errorMessage'])) {
      echo "$('#errorMessage').val('" . $_GET['errorMessage'] . "').trigger('change');";
   }

   if (!empty($_GET['reqDateTime'])) {
      $dt = explode(' - ', $_GET['reqDateTime']);
      echo 'setTimeout(function(){';
      echo "$('#dateTimePicker').data('daterangepicker').setStartDate('" . $dt[0] . "');";
      echo "$('#dateTimePicker').data('daterangepicker').setEndDate('" . $dt[1] . "');";
      echo '},1000);';
   }

   if (!empty($_GET['ackDateTime'])) {
      $dt = explode(' - ', $_GET['ackDateTime']);
      echo 'setTimeout(function(){';
      echo "$('#dateTimePicker1').data('daterangepicker').setStartDate('" . $dt[0] . "');";
      echo "$('#dateTimePicker1').data('daterangepicker').setEndDate('" . $dt[1] . "');";
      echo '},1000);';
   }
   ?>
</script>

</html>
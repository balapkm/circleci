<?php
$finalData = array();

function date_compare($a, $b)
{
    $t1 = strtotime($a['datetime']);
    $t2 = strtotime($b['datetime']);
    return $t1 - $t2;
}    

if (count($_GET) != 0) {
    $requestArray = array();
    $fileCount = 1;
    if (empty($_GET['requestId'])) {
        $fileName = __DIR__ . "/../../log/" . $_GET['reservation'] . "/" . $_GET['mode'] . "/" . $_GET['version'] . "/" . $_GET['months'];
        if (is_dir($fileName) && file_exists($fileName)) {
            $ffs1 = scandir($fileName);
            unset($ffs1[array_search('.', $ffs1, true)]);
            unset($ffs1[array_search('..', $ffs1, true)]);
            $ffs1 = array_values($ffs1);
            sort($ffs1);
            foreach ($ffs1 as $ffs1Key => $ffs1Value) {
                if (!empty(trim($ffs1Value))) {
                    $v = explode('_', $ffs1Value);
                    if (!empty($v)) {
                        $fileNameM = $fileName . "/" . $ffs1Value;
                        if (file_exists($fileNameM)) {
                            $requestArray[$v[1]] = date("Y-m-d H:i:s", filemtime($fileNameM));
                        }
                    }
                }
            }
        } else {
            echo "<script>alert('Invalid directory')</script>";
        }
    } else {
        for ($i = 1; $i < 100; $i++) {
            $fileName = __DIR__ . "/../../log/" . $_GET['reservation'] . "/" . $_GET['mode'] . "/" . $_GET['version'] . "/" . $_GET['months'] . "/SERVICE_" . $_GET['requestId'] . "_" . $i . ".txt";
            if (file_exists($fileName)) {
                $requestArray[$_GET['requestId']] = date("Y-m-d h:i:s", filemtime($fileName));
            } else {
                if ($i == 1) {
                    echo "<script>alert('file is not found')</script>";
                }
                break;
            }
        }
    }

    if (empty($requestArray)) {
        echo "<script>alert('No file found')</script>";
    }

    if (!empty($_GET['dateTimePicker'])) {
        $dateTime = explode(' - ', $_GET['dateTimePicker']);
        foreach ($requestArray as $k => $v) {
            $aTime = strtotime($v);
            if (!((strtotime($dateTime[0]) <= $aTime) && (strtotime($dateTime[1]) >= $aTime))) {
                unset($requestArray[$k]);
            }
        }
    }

    $requestArray1 = array();
    $requestArray2 = $requestArray;
    foreach ($requestArray as $k => $v) {
        $requestArray1[$k] = strtotime($v);
    }

    arsort($requestArray1);
    $requestArray = array();
    foreach($requestArray1 as $key => $value) {
        $requestArray[$key] = $requestArray2[$key];
    }

    $fileBasedFinalData = array();
    $fileBasedFinalDataCount = 0;
    foreach ($requestArray as $RA1 => $RA2) {
        $fileCount = 1;
        $fileName = __DIR__ . "/../../log/" . $_GET['reservation'] . "/" . $_GET['mode'] . "/" . $_GET['version'] . "/" . $_GET['months'] . "/SERVICE_" . $RA1 . "_" . $fileCount . ".txt";
        if (file_exists($fileName)) {
            $data = file_get_contents($fileName);

            for ($i = 2; $i < 100; $i++) {
                $fileName = __DIR__ . "/../../log/" . $_GET['reservation'] . "/" . $_GET['mode'] . "/" . $_GET['version'] . "/" . $_GET['months'] . "/SERVICE_" . $RA1 . "_" . $i . ".txt";
                if (file_exists($fileName)) {
                    $data .= file_get_contents($fileName);
                } else {
                    break;
                }
            }

            $serviceWise = explode("++++++++++", $data);
            $finalData = array();
            $count = 0;
            foreach ($serviceWise as $key => $value) {
                if (!empty(trim($value))) {
                    $classWise = explode("%%%%%%%%%%%", $value);

                    $classWiseArray = [];
                    foreach ($classWise as $cw1 => $cv1) {
                        if (!empty(trim($cv1))) {
                            $classWiseArray[] = $cv1;
                        }
                    }

                    $eCount = count($classWiseArray) - 1;
                    $c1 = 0;
                    foreach ($classWiseArray as $k1 => $v1) {
                        $pos = strpos($v1, ".SERVICE NAME");
                        if ($k1 != 0 && $k1 != 1 && !empty(trim($pos))) {
                            $serviceNameWise = explode("----------", trim($v1));
                            $serviceNameWiseArray = [];
                            foreach ($serviceNameWise as $sw1 => $sv1) {
                                if (!empty(trim($sv1))) {
                                    $serviceNameWiseArray[] = $sv1;
                                }
                            }

                            foreach ($serviceNameWiseArray as $k2 => $v2) {
                                if (!empty(trim($v2))) {

                                    $cArray = array(
                                        0 => "className",
                                        2 => "serviceRequest",
                                        1 => "serviceHeader",
                                        4 => "endPoint",
                                        5 => "serviceResponse",
                                        3 => "serviceCurlHeader",
                                        6 => "serviceParseResponse",
                                    );

                                    $zero = explode('^^^^^', $v2);
                                    $zero0 = explode('->', $zero[0]);
                                    $zero1 = explode('||', $zero[1]);
                                    array_unshift($zero1, trim($zero0[1]));
                                    $finalData[$count]['serviceWise'][$c1][$cArray[$k2]] = $zero1;

                                    if (!empty($finalData[$count]['serviceWise'][$c1]['serviceRequest'][3]) && !empty($finalData[$count]['serviceWise'][$c1]['serviceResponse'][3])) {
                                        $to_time = strtotime($finalData[$count]['serviceWise'][$c1]['serviceRequest'][3]);
                                        $from_time = strtotime($finalData[$count]['serviceWise'][$c1]['serviceResponse'][3]);
                                        $finalData[$count]['serviceWise'][$c1]['time'] = ($from_time - $to_time);
                                    }
                                }
                            }
                            $c1++;
                        }

                        if ($k1 == 0) {
                            $zero = explode('^^^^^', $v1);
                            $zero0 = explode('->', $zero[0]);
                            $zero1 = explode('||', $zero[1]);
                            array_unshift($zero1, trim($zero0[1]));
                            $finalData[$count]['serviceName'] = $zero1;
                            $finalData[$count]['dateTime'] = strtotime($zero1[3]);
                        }

                        if (empty($pos) && $k1 == $eCount) {
                            $zero = explode('^^^^^', $v1);
                            $zero0 = explode('->', $zero[0]);
                            $zero1 = explode('||', $zero[1]);
                            array_unshift($zero1, trim($zero0[1]));
                            $finalData[$count]['toolResponse'] = $zero1;
                        }

                        if ($k1 == 1) {
                            $zero = explode('^^^^^', $v1);
                            $zero0 = explode('->', $zero[0]);
                            $zero1 = explode('||', $zero[1]);
                            array_unshift($zero1, trim($zero0[1]));
                            $finalData[$count]['toolRequest'] = $zero1;
                        }

                        if (!empty($finalData[$count]['toolRequest'][3]) && !empty($finalData[$count]['toolResponse'][3])) {
                            $to_time = strtotime($finalData[$count]['toolRequest'][3]);
                            $from_time = strtotime($finalData[$count]['toolResponse'][3]);
                            $finalData[$count]['time'] = ($from_time - $to_time);
                        }
                    }

                    $count++;
                }
            }

            usort($finalData, function($a, $b) {
                return $b['dateTime'] - $a['dateTime'];
            });

            $fileBasedFinalData[$fileBasedFinalDataCount]['requestId'] = $RA1;
            $fileBasedFinalData[$fileBasedFinalDataCount]['time'] = $RA2;
            $fileBasedFinalData[$fileBasedFinalDataCount]['finalData'] = $finalData;
            $fileBasedFinalDataCount++;

        } else {
            echo "<script>alert('file2 is not found')</script>";
        }
    }

    if($_GET['type'] == 'download')
    {
        $finalData = $fileBasedFinalData[0]['finalData'][$_GET['finalDataKey']];
        $fileBasedFinalData[0]['finalData'] = $finalData;
    }
}

function listFolderFiles($dir)
{
    $ffs = scandir($dir);
    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);
    $finalResponse = array();
    $finalResponse['reservation'] = array_values($ffs);
    $finalResponse['mode'] = array();
    $finalResponse['version'] = array();
    $finalResponse['months'] = array();

    foreach ($ffs as $key => $value) {
        $dirs = $dir . "/" . $value;
        if (is_dir($dirs)) {
            $ffs1 = scandir($dirs);
            unset($ffs1[array_search('.', $ffs1, true)]);
            unset($ffs1[array_search('..', $ffs1, true)]);

            $finalResponse['mode'] = array_merge($finalResponse['mode'], array_values($ffs1));
            foreach ($ffs1 as $key1 => $value1) {
                $dirs = $dir . "/" . $value . "/" . $value1;
                if (is_dir($dirs)) {
                    $ffs2 = scandir($dirs);

                    unset($ffs2[array_search('.', $ffs2, true)]);
                    unset($ffs2[array_search('..', $ffs2, true)]);
                    $finalResponse['version'] = array_merge($finalResponse['version'], array_values($ffs2));
                    foreach ($ffs2 as $key2 => $value2) {
                        $dirs = $dir . "/" . $value . "/" . $value1 . "/" . $value2;
                        if (is_dir($dirs)) {
                            $ffs3 = scandir($dirs);

                            unset($ffs3[array_search('.', $ffs3, true)]);
                            unset($ffs3[array_search('..', $ffs3, true)]);

                            $finalResponse['months'] = array_merge($finalResponse['months'], array_values($ffs3));
                        }
                    }
                }
            }
        }
    }

    $finalResponse['reservation'] = array_values(array_unique($finalResponse['reservation']));
    $finalResponse['mode'] = array_values(array_unique($finalResponse['mode']));
    $finalResponse['version'] = array_values(array_unique($finalResponse['version']));
    $finalResponse['months'] = array_values(array_unique($finalResponse['months']));
    return $finalResponse;
}

$lists = listFolderFiles('../../log');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>CommonWebservice Interface</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="http://infinitisoftware.net/images/favicon.ico" type="image/x-icon"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <style>
    .select2-container .select2-selection--single{
      height: 35px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered{
      height: 35px;
    }
  </style>
</head>
<body>

<?php require_once "./menuNavbar.php";?>

<div class="container" style="margin-top: 100px">
  <div class="row">
  <form>
  <div class="col-lg-4">
        <div class="form-group">
          <label for="sel1">Select Reservation:</label>
          <select class="form-control select1" name="reservation" id="select1">
            <?php
foreach ($lists['reservation'] as $k1 => $v1) {
    echo '<option value="' . $v1 . '">' . $v1 . '</option>';
}
?>
          </select>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
          <label for="sel1">Select Travel Mode:</label>
          <select class="form-control select2" name="mode" id="select2">
            <?php
foreach ($lists['mode'] as $k1 => $v1) {
    echo '<option value="' . $v1 . '">' . $v1 . '</option>';
}
?>
          </select>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
          <label for="sel1">Select Version Code:</label>
          <select class="form-control select3" name="version" id="select3">
            <?php
foreach ($lists['version'] as $k1 => $v1) {
    echo '<option value="' . $v1 . '">' . $v1 . '</option>';
}
?>
          </select>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
          <label for="sel1">Select Month:</label>
          <select class="form-control select4" name="months" id="select4">
            <?php
foreach ($lists['months'] as $k1 => $v1) {
    echo '<option value="' . $v1 . '">' . $v1 . '</option>';
}
?>
          </select>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
          <label for="sel1">RequestId</label>
          <input type="text" class="form-control" name="requestId" id="requestId"/>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
          <label for="sel1">Date Time Range</label>
          <input type="text" class="form-control" name="dateTimePicker" id="dateTimePicker"/>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group text-center">
            <input type="submit" class="btn btn-primary">
        </div>
    </div>
    </form>
  </div>
   <?php if (!empty($finalData)): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-group" id="Raccordion">
                <?php
foreach ($fileBasedFinalData as $RA => $RV) {
    echo '
                <div class="panel panel-primary" style="margin-top:10px">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#Raccordion" href="#collapse' . $RA . '">Ref No : <b>' . $RV['requestId'] . '</b>
                            <span style="text-align : right;color : white;position : absolute;right : 550px">Total Request : <b>' . count($RV['finalData']) . '</b></span>
                            <span style="text-align : right;color : white;position : absolute;right : 50px"> <b>' . date('d-M-Y H:i:s', strtotime($RV['time'])) . ' </b></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse' . $RA . '" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="panel-group" id="pccordion' . $RA . $key . '">';
    foreach ($RV['finalData'] as $key => $value) {
        echo '  <div class="panel panel-success" style="margin-top:10px">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#paccordion' . $RA . $key . '" href="#collapse' . $RA . $key . '">' . $value['serviceName'][0] . '
                                            <span style="text-align : right;color : green;position : absolute;right : 450px">' . date('d-M-Y H:i:s', strtotime($value['serviceName'][3])) . '</span>
                                            <span style="text-align : right;color : red;position : absolute;right : 50px">ETA : ' . $value['time'] . 's </span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse' . $RA . $key . '" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <button type="button" class="btn btn-primary btn-xs">Download As zip</button>
                                            <div class="panel-group" id="accordion' . $RA . $key . '">';
        foreach ($value['serviceWise'] as $k1 => $v1) {
            echo '
                                                <div class="panel panel-default" style="margin-top:10px">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a data-toggle="collapse" data-parent="#accordion' . $RA . $key . '" href="#collapseService' . $RA . $key . $k1 . '">' . $v1['className'][0] . '
                                                            <span style="text-align : right;color : green;position : absolute;right : 450px">' . date('d-M-Y H:i:s', strtotime($v1['className'][3])) . '</span>
                                                            <span style="text-align : right;color : red;position : absolute;right : 70px">ETA : ' . $v1['time'] . 's </span>
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div id="collapseService' . $RA . $key . $k1 . '" class="panel-collapse collapse">
                                                        <div class="panel-body">
                                                            <h5><b>ENDPOINT :</b> ' . $v1['endPoint'][0] . '</h5>
                                                            <ul class="nav nav-tabs">
                                                                <li class="active"><a data-toggle="tab" href="#request' . $RA . $key . $k1 . '">Service Request</a></li>
                                                                <li><a data-toggle="tab" href="#response' . $RA . $key . $k1 . '">Service Response</a></li>
                                                                <li><a data-toggle="tab" href="#headers' . $RA . $key . $k1 . '">Service Headers</a></li>
                                                                <li><a data-toggle="tab" href="#curlheaders' . $RA . $key . $k1 . '">Curl Headers</a></li>
                                                                <li><a data-toggle="tab" href="#serviceParseResponse' . $RA . $key . $k1 . '">Class Parse Response</a></li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div id="request' . $RA . $key . $k1 . '" class="tab-pane fade in active">
                                                                </br>
                                                                <p><b>IP Address : </b> ' . $v1['serviceRequest'][1] . '
                                                                <b>Agent : </b> ' . $v1['serviceRequest'][2] . '
                                                                <b>Date Time : </b> ' . $v1['serviceRequest'][3] . '</p>
                                                                <textarea style="margin-top:20px;width: 1055px;height: 500px;border : none;resize: none;background-color: #b5a4a41f;" disabled="disabled">' . $v1['serviceRequest'][0] . '</textarea>
                                                                </div>
                                                                <div id="response' . $RA . $key . $k1 . '" class="tab-pane fade">
                                                                </br>
                                                                <p><b>IP Address : </b> ' . $v1['serviceResponse'][1] . '
                                                                <b>Agent : </b> ' . $v1['serviceResponse'][2] . '
                                                                <b>Date Time : </b> ' . $v1['serviceResponse'][3] . '</p>
                                                                <textarea style="margin-top:20px;width: 1055px;height: 500px;border : none;resize: none;background-color: #b5a4a41f;" disabled="disabled">' . $v1['serviceResponse'][0] . '</textarea>
                                                                </div>
                                                                <div id="headers' . $RA . $key . $k1 . '" class="tab-pane fade">
                                                                </br>
                                                                <p><b>IP Address : </b> ' . $v1['serviceHeader'][1] . '
                                                                <b>Agent : </b> ' . $v1['serviceHeader'][2] . '
                                                                <b>Date Time : </b> ' . $v1['serviceHeader'][3] . '</p>
                                                                <pre style="margin-top:20px">' . $v1['serviceHeader'][0] . '</pre>
                                                                </div>
                                                                <div id="curlheaders' . $RA . $key . $k1 . '" class="tab-pane fade">
                                                                </br>
                                                                <p><b>IP Address : </b> ' . $v1['serviceCurlHeader'][1] . '
                                                                <b>Agent : </b> ' . $v1['serviceCurlHeader'][2] . '
                                                                <b>Date Time : </b> ' . $v1['serviceCurlHeader'][3] . '</p>
                                                                <pre style="margin-top:20px">' . $v1['serviceCurlHeader'][0] . '</pre>
                                                                </div>
                                                                <div id="serviceParseResponse' . $RA . $key . $k1 . '" class="tab-pane fade">
                                                                </br>
                                                                <p><b>IP Address : </b> ' . $v1['serviceParseResponse'][1] . '
                                                                <b>Agent : </b> ' . $v1['serviceParseResponse'][2] . '
                                                                <b>Date Time : </b> ' . $v1['serviceParseResponse'][3] . '</p>
                                                                <textarea style="margin-top:20px;width: 1055px;height: 500px;border : none;resize: none;background-color: #b5a4a41f;" disabled="disabled">' . $v1['serviceParseResponse'][0] . '</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
        }
        /**
         * tools
         */
        echo '
                                                <div class="panel panel-default" style="margin-top:10px">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a data-toggle="collapse" data-parent="#accordion' . ($key + 1) . '" href="#collapseService' . ($key + 1) . ($k1 + 1) . '">User Input
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div id="collapseService' . ($key + 1) . ($k1 + 1) . '" class="panel-collapse collapse">
                                                        <div class="panel-body">
                                                            <ul class="nav nav-tabs">
                                                                <li class="active"><a data-toggle="tab" href="#request' . ($key + 1) . ($k1 + 1) . '">User Request</a></li>
                                                                <li><a data-toggle="tab" href="#response' . ($key + 1) . ($k1 + 1) . '">User Response</a></li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div id="request' . ($key + 1) . ($k1 + 1) . '" class="tab-pane fade in active">
                                                                </br>
                                                                <p><b>IP Address : </b> ' . $value['toolRequest'][1] . '
                                                                <b>Agent : </b> ' . $value['toolRequest'][2] . '
                                                                <b>Date Time : </b> ' . $value['toolRequest'][3] . '</p>
                                                                <textarea style="margin-top:20px;width: 1055px;height: 500px;border : none;resize: none;background-color: #b5a4a41f;" disabled="disabled">' . $value['toolRequest'][0] . '</textarea>
                                                                </div>
                                                                <div id="response' . ($key + 1) . ($k1 + 1) . '" class="tab-pane fade">
                                                                </br>
                                                                <p><b>IP Address : </b> ' . $value['toolResponse'][1] . '
                                                                <b>Agent : </b> ' . $value['toolResponse'][2] . '
                                                                <b>Date Time : </b> ' . $value['toolResponse'][3] . '</p>
                                                                <textarea style="margin-top:20px;width: 1055px;height: 500px;border : none;resize: none;background-color: #b5a4a41f;" disabled="disabled">' . $value['toolResponse'][0] . '</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
        echo '
                                            </div>
                                        </div>
                                    </div>
                                </div>';

    }
    echo '
                            </div>
                        </div>
                    </div>
                </div>';
}
?>
            </div>
        </div>
    </div>
    <?php endif;?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('#select1,#select2,#select3,#select4').select2();
    // $("#checkbox").click(function(){
    //     if($("#checkbox").is(':checked')){
    //         $("#sel1 > optgroup > option").prop("selected","selected");// Select All Options
    //         $("#sel1").trigger("change");// Trigger change to select 2
    //     }else{
    //         $("#sel1 > optgroup > option").removeAttr("selected");
    //         $("#sel1").trigger("change");// Trigger change to select 2
    //     }
    // });
    $('#dateTimePicker').daterangepicker({
        timePicker: true,
        timePickerSeconds: true,
        startDate: moment().format(),
        endDate: moment().add(1, 'seconds').format(),
        locale: {
            format: 'YYYY-MM-DD HH:mm:ss',
            cancelLabel: 'Clear'
        }
    });
    setTimeout(function(){
        $('#dateTimePicker').val('');
    },100);
    
    $('#dateTimePicker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    <?php 
    if(!empty($_GET['reservation'])){
        echo "$('#select1').select2().val('".$_GET['reservation']."').trigger('change');";
    }

    if(!empty($_GET['mode'])){
        echo "$('#select2').select2().val('".$_GET['mode']."').trigger('change');";
    }

    if(!empty($_GET['version'])){
        echo "$('#select3').select2().val('".$_GET['version']."').trigger('change');";
    }

    if(!empty($_GET['months'])){
        echo "$('#select4').select2().val('".$_GET['months']."').trigger('change');";
    }

    if(!empty($_GET['requestId'])){
        echo "$('#requestId').val('".$_GET['requestId']."');";
    }

    if(!empty($_GET['dateTimePicker'])){
        $dt = explode(' - ',$_GET['dateTimePicker']);
        echo 'setTimeout(function(){';
        echo "$('#dateTimePicker').data('daterangepicker').setStartDate('".$dt[0]."');";
        echo "$('#dateTimePicker').data('daterangepicker').setEndDate('".$dt[1]."');";
        echo '},100);';
    }

    ?>
  });
</script>
</body>
</html>

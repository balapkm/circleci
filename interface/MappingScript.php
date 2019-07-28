<?php
if (count($_POST) != 0 && count($_POST['data']) != 0) {
    $config = parse_ini_file(__DIR__ . "/../../config/database.ini", true);
    foreach ($_POST['data'] as $key => $value) {
        $path = explode("||", $value);
        $csvPath = $path[0] . "/" . $path[1];
// print_r($csvPath);exit();
        $file = fopen($csvPath, "r");
        $data = array();
        while (!feof($file)) {
            $data[] = fgetcsv($file);
        }
        fclose($file);

        // Create connection
        $conn = new mysqli($config['DATA_BASE']['HOST'], $config['DATA_BASE']['USERNAME'], $config['DATA_BASE']['AUTHENTICATION'], $config['DATA_BASE']['DATABASENAME']);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // print_r($data);exit;
        foreach ($data as $k1 => $v1) {
            if ($k1 != 0 && !empty($v1) && !empty($v1[1]) && !empty($v1[2]) && !empty($v1[3]) && !empty($v1[4]) && !empty($v1[5]) && !empty($v1[6]) && !empty($v1[10]) && !empty($v1[11]) && !empty($v1[12]) && !empty($v1[13]) && !empty($v1[14])) {

                $mapping_id = array();

                $v1[1] = strtoupper($v1[1]);
                $sql = "SELECT * FROM core_reservation_master WHERE reservation_name='" . $v1[1] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['reservation_id'] = $row['reservation_id'];
                } else {
                    $sql = "INSERT INTO core_reservation_master (reservation_name, reservation_folder_name, reservation_status)
                    VALUES ('" . $v1[1] . "', '" . $v1[1] . "', 'Y')";

                    if ($conn->query($sql) === true) {
                        $mapping_id['reservation_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }
                /**
                 * core_travel_mode_master
                 */
                $v1[2] = strtoupper($v1[2]);
                $sql = "SELECT * FROM core_travel_mode_master WHERE travel_mode_name='" . $v1[2] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['travel_mode_id'] = $row['travel_mode_id'];
                } else {
                    $sql = "INSERT INTO core_travel_mode_master (travel_mode_name, travel_mode_folder_name, travel_mode_status)
                    VALUES ('" . $v1[2] . "', '" . $v1[2] . "', 'Y')";

                    if ($conn->query($sql) === true) {
                        $mapping_id['travel_mode_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }
                /**
                 * core_version_master
                 */
                $v1[3] = strtoupper($v1[3]);
                $sql = "SELECT * FROM core_version_master WHERE version_code='" . $v1[3] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['version_id'] = $row['version_id'];
                } else {
                    $sql = "INSERT INTO core_version_master (version_code, version_folder_name, version_description,version_status)
                    VALUES ('" . $v1[3] . "', '" . $v1[3] . "','', 'Y')";

                    if ($conn->query($sql) === true) {
                        $mapping_id['version_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }

                /**
                 * core_account_type_master
                 */
                $v1[4] = strtoupper($v1[4]);
                $sql = "SELECT * FROM core_account_type_master WHERE account_type_code='" . $v1[4] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['account_type_id'] = $row['account_type_id'];
                } else {
                    $sql = "INSERT INTO core_account_type_master (account_type_code, account_type_name, account_type_status)
                    VALUES ('" . $v1[4] . "', '', 'Y')";

                    if ($conn->query($sql) === true) {
                        $mapping_id['account_type_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }

                /**
                 * core_url_master
                 */
                $v1[13] = trim($v1[13]);
                $sql = "SELECT * FROM core_url_master WHERE url_name='" . $v1[13] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['url_id'] = $row['url_id'];
                } else {
                    $sql = "INSERT INTO core_url_master (url_name, url_status)
                    VALUES ('" . $v1[13] . "', 'Y')";

                    if ($conn->query($sql) === true) {
                        $mapping_id['url_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }

                /**
                 * core_agency_master
                 */
                $v1[5] = $_POST['agency_code'];
                $sql = "SELECT * FROM core_agency_master WHERE agency_code='" . $v1[5] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['agency_id'] = $row['agency_id'];
                } else {
                    $sql = "INSERT INTO core_agency_master (agency_code, agency_description,agency_status)
                    VALUES ('" . $v1[5] . "','" . $v1[5] . "', 'Y')";

                    if ($conn->query($sql) === true) {
                        $mapping_id['agency_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }

                /**
                 * core_agency_reservation_mapping_master
                 */
                $sql = "SELECT * FROM core_agency_reservation_mapping_master
                            WHERE agency_id='" . $mapping_id['agency_id'] . "' AND
                            reservation_id='" . $mapping_id['reservation_id'] . "' AND
                            travel_mode_id='" . $mapping_id['travel_mode_id'] . "' AND
                            account_type_id='" . $mapping_id['account_type_id'] . "' AND
                            version_id='" . $mapping_id['version_id'] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['agency_reservation_id'] = $row['agency_reservation_id'];
                } else {
                    $sql = "INSERT INTO core_agency_reservation_mapping_master (agency_id, reservation_id,travel_mode_id,account_type_id,version_id,agency_reservation_status)
                    VALUES ('" . $mapping_id['agency_id'] . "','" . $mapping_id['reservation_id'] . "','" . $mapping_id['travel_mode_id'] . "','" . $mapping_id['account_type_id'] . "','" . $mapping_id['version_id'] . "', 'Y')";

                    if ($conn->query($sql) === true) {
                        $mapping_id['agency_reservation_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }

                /**
                 * core_template_master
                 */
                $sql = "SELECT * FROM core_template_master WHERE template_class_name='" . $v1[6] . "' AND template_name='" . $v1[7] . "'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['template_id'] = $row['template_id'];
                } else {
                    $sql = "INSERT INTO core_template_master (template_class_name, template_name,service_action,service_method,template_status)
                    VALUES ('" . $v1[6] . "','" . $v1[7] . "','" . $v1[8] . "','" . $v1[9] . "', 'Y')";

                    if ($conn->query($sql) === true) {
                        $mapping_id['template_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }

                /**
                 * core_service_master
                 */
                $statusData = json_decode($v1[16], 1);

                $serviceName = explode(".", $path[1])[0];
                $sql = "SELECT * FROM core_service_master WHERE service_name='" . $serviceName . "'";
                $result = $conn->query($sql);

                if (empty($statusData)) {
                    $statusData['request_decryption_status'] = 'N';
                    $statusData['response_encryption_status'] = 'N';
                    $statusData['empty_data_status'] = 'N';
                    $statusData['xml_request_status'] = 'N';
                    $statusData['trace_log_status'] = 'N';
                    $statusData['ip_patching_status'] = 'N';
                    $statusData['JWT_status'] = 'N';
                    $statusData['basic_auth_status'] = 'N';
                    $statusData['mail_status'] = 'N';
                } else {
                    $statusData['request_decryption_status'] = empty($statusData['request_decryption_status']) ? 'N' : $statusData['request_decryption_status'];
                    $statusData['response_encryption_status'] = empty($statusData['response_encryption_status']) ? 'N' : $statusData['response_encryption_status'];
                    $statusData['empty_data_status'] = empty($statusData['empty_data_status']) ? 'N' : $statusData['empty_data_status'];
                    $statusData['xml_request_status'] = empty($statusData['xml_request_status']) ? 'N' : $statusData['xml_request_status'];
                    $statusData['trace_log_status'] = empty($statusData['trace_log_status']) ? 'N' : $statusData['trace_log_status'];
                    $statusData['ip_patching_status'] = empty($statusData['ip_patching_status']) ? 'N' : $statusData['ip_patching_status'];
                    $statusData['JWT_status'] = empty($statusData['JWT_status']) ? 'N' : $statusData['JWT_status'];
                    $statusData['basic_auth_status'] = empty($statusData['basic_auth_status']) ? 'N' : $statusData['basic_auth_status'];
                    $statusData['mail_status'] = empty($statusData['mail_status']) ? 'N' : $statusData['mail_status'];
                }

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $mapping_id['service_id'] = $row['service_id'];

                    $sql = "UPDATE core_service_master SET
                            request_decryption_status = '" . $statusData['request_decryption_status'] . "' ,
                            response_encryption_status = '" . $statusData['response_encryption_status'] . "' ,
                            empty_data_status = '" . $statusData['empty_data_status'] . "' ,
                            xml_request_status = '" . $statusData['xml_request_status'] . "' ,
                            trace_log_status = '" . $statusData['trace_log_status'] . "' ,
                            ip_patching_status = '" . $statusData['ip_patching_status'] . "' ,
                            JWT_status = '" . $statusData['JWT_status'] . "' ,
                            basic_auth_status = '" . $statusData['basic_auth_status'] . "' ,
                            mail_status = '" . $statusData['mail_status'] . "'
                            WHERE service_id = " . $row['service_id'] . "
                            ";

                    if ($conn->query($sql) === true) {
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }

                } else {
                    $sql = "INSERT INTO core_service_master (service_name, service_status, request_decryption_status , response_encryption_status, empty_data_status , xml_request_status , trace_log_status , ip_patching_status , JWT_status , basic_auth_status , mail_status)
                    VALUES ('" . $serviceName . "', 'Y' , '" . $statusData['request_decryption_status'] . "' , '" . $statusData['response_encryption_status'] . "' , '" . $statusData['empty_data_status'] . "' , '" . $statusData['xml_request_status'] . "' , '" . $statusData['trace_log_status'] . "' , '" . $statusData['ip_patching_status'] . "' , '" . $statusData['JWT_status'] . "' , '" . $statusData['basic_auth_status'] . "' , '" . $statusData['mail_status'] . "' )";
                    if ($conn->query($sql) === true) {
                        $mapping_id['service_id'] = $conn->insert_id;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                        exit();
                    }
                }

                /**
                 * core_agency_service_mapping_master
                 */
                $serviceName = explode(".", $path[1])[0];
                $sql = "SELECT * FROM core_agency_service_mapping_master WHERE
                agency_reservation_id='" . $mapping_id['agency_reservation_id'] . "' AND
                service_id='" . $mapping_id['service_id'] . "'
                ";
                if ($k1 == 1) {
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $sql = "DELETE FROM core_agency_service_mapping_master WHERE agency_service_mapping_id = " . $row['agency_service_mapping_id'];

                            if ($conn->query($sql) === true) {
                                // $mapping_id['agency_service_mapping_id'] = $conn->insert_id;
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                                exit();
                            }
                        }

                        $mapping_id['agency_service_mapping_id'] = $row['agency_service_mapping_id'];
                    }
                }

                $sql = "INSERT INTO core_agency_service_mapping_master
                (agency_reservation_id, service_id,template_id,url_id,execution_order,execution_type,file_write_status,status)
                VALUES ('" . $mapping_id['agency_reservation_id'] . "','" . $mapping_id['service_id'] . "','" . $mapping_id['template_id'] . "','" . $mapping_id['url_id'] . "','" . $v1[10] . "','" . $v1[12] . "','" . $v1[11] . "', '" . $_POST['active_status'] . "')";

                if ($conn->query($sql) === true) {
                    $mapping_id['agency_service_mapping_id'] = $conn->insert_id;
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                    exit();
                }

                /**
                 * insert validation master
                 */
                if ($k1 == 1 && !empty($v1[14]) && !empty(json_decode($v1[14], 1))):
                    $sql = "SELECT * FROM core_validation_master WHERE
			                service_id='" . $mapping_id['service_id'] . "' AND
			                agency_reservation_id='" . $mapping_id['agency_reservation_id'] . "' AND
			                account_type_id='" . $mapping_id['account_type_id'] . "'";

                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $mapping_id['default_version_id'] = $row['default_version_id'];

                        $sql = "UPDATE core_validation_master SET
			                    validation_schema = '" . $v1[14] . "'
			                    WHERE default_version_id = " . $row['default_version_id'] . "
			                    ";

                        if ($conn->query($sql) === true) {
                            // $mapping_id['agency_service_mapping_id'] = $conn->insert_id;
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                            exit();
                        }

                    } else {
                        $sql = "INSERT INTO core_validation_master (service_id, agency_reservation_id,account_type_id,validation_schema,validation_status)
			                    VALUES ('" . $mapping_id['service_id'] . "','" . $mapping_id['agency_reservation_id'] . "','" . $mapping_id['account_type_id'] . "','" . $v1[14] . "', 'Y')";

                        if ($conn->query($sql) === true) {
                            $mapping_id['default_version_id'] = $conn->insert_id;
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                            exit();
                        }
                    }
                endif;

                /**
                 * core_access_credentials_master
                 */

                if (!empty($v1[15]) && $k1 == 1) {
                    $credentials = json_decode($v1[15], 1);
                    if (!empty($credentials)) {
                        $sql = "SELECT * FROM core_access_credentials_master WHERE
                        agency_reservation_id='" . $mapping_id['agency_reservation_id'] . "'";

                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $sql = "DELETE FROM core_access_credentials_master WHERE agency_reservation_id = " . $row['agency_reservation_id'];

                                if ($conn->query($sql) === true) {
                                    // $mapping_id['agency_service_mapping_id'] = $conn->insert_id;
                                } else {
                                    echo "Error: " . $sql . "<br>" . $conn->error;
                                    exit();
                                }
                            }
                        }

                        foreach ($credentials as $ck => $cv) {
                            $sql = "INSERT INTO core_access_credentials_master (agency_reservation_id,access_credentials_key,access_credentials_value,access_credentials_encryption_status,access_credentials_status)
                            VALUES ('" . $mapping_id['agency_reservation_id'] . "','" . $cv["key"] . "','" . $cv["value"] . "','" . $cv["encryption_status"] . "','" . $cv["status"] . "' )";

                            if ($conn->query($sql) === true) {
                                $mapping_id['access_credentials_id'] = $conn->insert_id;
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                                exit();
                            }
                        }
                    }
                }

                $QUERY_FILE_PATH = __DIR__ . "/../../classesTpl";
                $QUERY_FILE_PATH = $QUERY_FILE_PATH . "/" . $v1[1];
                if (!file_exists($QUERY_FILE_PATH)) {
                    mkdir($QUERY_FILE_PATH);
                    chmod($QUERY_FILE_PATH, 0777);
                }

                $QUERY_FILE_PATH = $QUERY_FILE_PATH . "/" . $v1[2];
                if (!file_exists($QUERY_FILE_PATH)) {
                    mkdir($QUERY_FILE_PATH);
                    chmod($QUERY_FILE_PATH, 0777);
                }

                $QUERY_FILE_PATH = $QUERY_FILE_PATH . "/" . $v1[3];
                if (!file_exists($QUERY_FILE_PATH)) {
                    mkdir($QUERY_FILE_PATH);
                    chmod($QUERY_FILE_PATH, 0777);
                }

                $QUERY_FILE_PATH_CT = $QUERY_FILE_PATH . "/" . ucwords($v1[6]) . ".php";
                $QUERY_FILE_PATH_CF = $QUERY_FILE_PATH . "/CommonFunction.php";

                if (!empty($v1[7])) {
                    $QUERY_FILE_PATH = __DIR__ . "/../../view/template";
                    $QUERY_FILE_PATH = $QUERY_FILE_PATH . "/" . $v1[1];
                    if (!file_exists($QUERY_FILE_PATH)) {
                        mkdir($QUERY_FILE_PATH);
                        chmod($QUERY_FILE_PATH, 0777);
                    }

                    $QUERY_FILE_PATH = $QUERY_FILE_PATH . "/" . $v1[2];
                    if (!file_exists($QUERY_FILE_PATH)) {
                        mkdir($QUERY_FILE_PATH);
                        chmod($QUERY_FILE_PATH, 0777);
                    }

                    $QUERY_FILE_PATH = $QUERY_FILE_PATH . "/" . $v1[3];
                    if (!file_exists($QUERY_FILE_PATH)) {
                        mkdir($QUERY_FILE_PATH);
                        chmod($QUERY_FILE_PATH, 0777);
                    }

                    $QUERY_FILE_PATH_TEMP = $QUERY_FILE_PATH . "/" . ucwords($v1[7]) . ".tpl";

                    $txt = "";
                    if (!file_exists($QUERY_FILE_PATH_TEMP)) {
                        $myfile = fopen($QUERY_FILE_PATH_TEMP, "w+") or die("Unable to open file!1");
                        fwrite($myfile, $txt);
                        chmod($QUERY_FILE_PATH_TEMP, 0777);
                    }
                }
                $txt = '<?php
namespace App\application\classesTpl\\' . $v1[1] . '\\' . $v1[2] . '\\' . $v1[3] . ';

/**
 * Infiniti standards framework.
 *
 * @package     Infiniti webservice framework
 * @author      <Author>
 * @copyright   Copyright (c) 2018 Infiniti software solution
 * @version     Release: @package_version@
 * @link        http://www.infinitisoftware.net/
 * @date        ' . date('Y-m-d H:i:s') . '
 **/

class ' . ucwords($v1[6]) . '
{
    /**
     * Inlcude trait classes
     */
    use \App\system\core\AccessServiceController;
    use \App\system\core\CommonFunctionController;
    use \App\system\core\TemplateController;
    use \App\system\core\ResponseController;
    use \App\system\core\FileWriteController;
    use \App\application\classes\COMMON\CustomValidation;
    use \App\application\classesTpl\\' . $v1[1] . '\\' . $v1[2] . '\\' . $v1[3] . '\CommonFunction;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * _validateRequest
     *
     * @return void
     */
    public function _validateRequest()
    {
    }

    /**
     * _setAccessCredentials
     *
     * @return boolean
     */
    public function _setAccessCredentials()
    {
        $this->_AcurlHeader = array(
        );
        return true;
    }

    /**
     * _setHeaderCredentials
     *
     * @return boolean
     */
    public function _setHeaderCredentials()
    {
        $this->_AserviceHeader = array();
        return true;
    }

    /**
     * _processRequest
     *
     * @return void
     */
    public function _processRequest()
    {
        $this->_AserviceRequest = $this->_AuserRequest["data"];
        return true;
    }

    /**
     * _processResponse
     *
     * @return void
     */
    public function _processResponse()
    {
        $finalResponse = $this->_AserviceResponse;
        $this->_AfinalResponse = $this->_returnFinalResponse($finalResponse);
        return true;
    }
}
?>
                ';

                if (!file_exists($QUERY_FILE_PATH_CT)) {
                    $myfile = fopen($QUERY_FILE_PATH_CT, "w+") or die("Unable to open file!2");
                    fwrite($myfile, $txt);
                    chmod($QUERY_FILE_PATH_CT, 0777);
                }

                $txt = '<?php
namespace App\application\classesTpl\\' . $v1[1] . '\\' . $v1[2] . '\\' . $v1[3] . ';

/**
 * Infiniti standards framework.
 *
 * @package     Infiniti webservice framework
 * @author      <Author>
 * @copyright   Copyright (c) 2018 Infiniti software solution
 * @version     Release: @package_version@
 * @link        http://www.infinitisoftware.net/
 * @date        ' . date('Y-m-d H:i:s') . '
 **/

trait CommonFunction
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * _serviceErrorHandling
     *
     * @return void
     */
    public function _serviceErrorHandling()
    {
    }
}
?>
                ';

                if (!file_exists($QUERY_FILE_PATH_CF)) {
                    $myfile = fopen($QUERY_FILE_PATH_CF, "w+") or die("Unable to open file!3");
                    fwrite($myfile, $txt);
                    chmod($QUERY_FILE_PATH_CF, 0777);
                }

                fclose($myfile);
            }
        }
    }

    $_POST = array();
    putenv('COMPOSER_HOME=' . __DIR__ . '/../../../system/plugins/bin/composer');
    $cmd = system('cd ../../../ && pwd && composer -o dump-autoload 2>&1');
    echo "<script>alert('Finished -->" . $cmd . "');</script>";
    // exit;
}

function listFolderFiles($dir)
{
    $ffs = scandir($dir);

    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);

    $finalResponse = array();
    foreach ($ffs as $key => $value) {
        $dirs = $dir . "/" . $value;
        $ffs1 = scandir($dirs);

        unset($ffs1[array_search('.', $ffs1, true)]);
        unset($ffs1[array_search('..', $ffs1, true)]);

        foreach ($ffs1 as $key1 => $value1) {
            $dirs = $dir . "/" . $value . "/" . $value1;
            $ffs2 = scandir($dirs);

            unset($ffs2[array_search('.', $ffs2, true)]);
            unset($ffs2[array_search('..', $ffs2, true)]);
            foreach ($ffs2 as $key2 => $value2) {
                $dirs = $dir . "/" . $value . "/" . $value1 . "/" . $value2;
                $ffs3 = scandir($dirs);

                unset($ffs3[array_search('.', $ffs3, true)]);
                unset($ffs3[array_search('..', $ffs3, true)]);

                $array = array_filter($ffs3, function ($value) {
                    return strpos($value, '.~lock.') === 0;
                });

                foreach ($array as $key4 => $value4) {
                    unset($ffs3[$key4]);
                }
                // unset($ffs3[array_search('.~lock.', $ffs3, true)]);

                $finalResponse[$value . "_" . $value1 . "_" . $value2] = array();
                foreach ($ffs3 as $key3 => $value3) {
                    $finalResponse[$value . "_" . $value1 . "_" . $value2]['dir'] = $dirs;
                    $finalResponse[$value . "_" . $value1 . "_" . $value2]['data'][] = $value3;
                }
            }
        }
    }
    return $finalResponse;
}

$lists = listFolderFiles('./MappingData');
// print_r($lists);exit;
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
<form method="post">
  <div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            <label for="sel1">Agency Code</label>
            <select class="form-control" name="agency_code">
                <option value="101">101</option>
                <option value="102">102</option>
            </select>
        </div>
    </div>
    <div class="col-lg-4">

        <div class="form-group">
          <label for="sel1">Select list:</label>
          <select multiple="multiple" id="sel1" name="data[]">
            <?php
foreach ($lists as $key => $value) {
    echo '<optgroup label="' . $key . '">';

    foreach ($value['data'] as $k1 => $v1) {
        echo '<option value="' . $value['dir'] . '||' . $v1 . '">' . $v1 . '</option>';
    }

    echo "</optgroup>";
}
?>
          </select>
        </div>
        <div class="form-group">
            <input type="checkbox" id="checkbox" >  Select All
        </div>
        <div class="form-group text-center">
          <input type="submit" class="btn btn-primary" name="submit">
        </div>

    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="sel1">Service Active Status</label>
            <select class="form-control" name="active_status">
                <option value="Y">Yes</option>
                <option value="N">No</option>
            </select>
        </div>
    </div>
    </form>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('#sel1').select2();
    $("#checkbox").click(function(){
        if($("#checkbox").is(':checked')){
            $("#sel1 > optgroup > option").prop("selected","selected");// Select All Options
            $("#sel1").trigger("change");// Trigger change to select 2
        }else{
            $("#sel1 > optgroup > option").removeAttr("selected");
            $("#sel1").trigger("change");// Trigger change to select 2
        }
    });
  });
</script>
</body>
</html>

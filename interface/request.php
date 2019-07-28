<?php
/**
 * Infiniti standards framework.
 *
 * @package     Infiniti webservice framework
 * @author      Santhosh.M <santhosh.m@infinitisoftware.net>
 * @copyright   Copyright (c) 2018 Infiniti software solution
 * @version     Release: @V1
 * @link        http://www.infinitisoftware.net/
 * @date        2019-06-28 19:21:04
 **/

class commonRequest
{
    public function __construct()
    {
    }
    /**
     *    @function name : createMemcacheObject
     *    @function desc : createMemcacheObject
     *    @parameter     : none
     **/
    public function _createMemcacheObject()
    {
        $this->memcacheConfig = array(
            "hostName" => "localhost",
            "portNo" => "11211",
            "second" => "1000",
            "from" => "CommonWebServices1.0",
        );
        if (class_exists("\Memcached")) {
            $this->Omemcache = new \Memcached();
            $this->Omemcache->setOption(\Memcached::OPT_COMPRESSION, true);
            $this->Omemcache->setOption(\Memcached::OPT_COMPRESSION_TYPE, \Memcached::COMPRESSION_ZLIB);

            $this->Omemcache->addServer($this->memcacheConfig['hostName'], $this->memcacheConfig['portNo']) or die("Could not connect with memcache");
        } else {
            return array("status" => "N", "response" => "Memcached not installed or connected");
        }
    }

    /**
     * Function Name : _flushMemcacheData
     * Desc : flush overall memcache data where stored before
     * @return none
     */
    public function _flushMemcacheData()
    {
        $this->Omemcache->flush();
        return array("status" => "N", "response" => "Flushed successfully..");
    }

    /**
     * Function Name : _getAllKeysWithData
     * Desc : Get overall stored memcache data with respective key
     * @return $this->memcacheData
     */
    public function _getAllKeysWithData()
    {
        $final = array();
        if (method_exists($this->Omemcache, "getAllKeys")) {
            $keys = $this->Omemcache->getAllKeys();
            if (!is_array($keys)) {
                /**
                 * For PHP5.6
                 */
                $keys = $this->_getMemcachedKeys();
            }
            $this->Omemcache->getDelayed($keys);
            $storedData = $this->Omemcache->fetchAll();
            foreach ($storedData as $key => $value) {
                $final[$key]['key'] = base64_decode($value['key']);
                $final[$key]['enKey'] = str_replace("=", "", $value['key']);
                $final[$key]['value'] = $value['value'];
            }
            if (!empty($final)) {
                return array("status" => "Y", "response" => $final);
            } else {
                return array("status" => "N", "response" => "No data found");
            }
        } else {
            return array("status" => "N", "response" => "Cannot read memcached data.Issue with PHP version.This function applicable for above PHP7.x version");
        }
        return true;
    }

    public function _deleteByKey($key)
    {
        $this->Omemcache->deleteByKey($this->memcacheConfig['hostName'], $key);
    }

    public function _getMemcachedKeys()
    {
        $mem = @fsockopen($this->memcacheConfig['hostName'], $this->memcacheConfig['portNo']);
        if ($mem === false) {
            return -1;
        }

        // retrieve distinct slab
        $r = @fwrite($mem, 'stats items' . chr(10));
        if ($r === false) {
            return -2;
        }

        $slab = array();
        while (($l = @fgets($mem, 1024)) !== false) {
            // sortie ?
            $l = trim($l);
            if ($l == 'END') {
                break;
            }

            $m = array();
            $r = preg_match('/^STAT\sitems\:(\d+)\:/', $l, $m);
            if ($r != 1) {
                return -3;
            }

            $a_slab = $m[1];
            if (!array_key_exists($a_slab, $slab)) {
                $slab[$a_slab] = array();
            }
        }

        // recuperer les items
        reset($slab);
        foreach ($slab as $a_slab_key => &$a_slab) {
            $r = @fwrite($mem, 'stats cachedump ' . $a_slab_key . ' 100' . chr(10));
            if ($r === false) {
                return -4;
            }

            while (($l = @fgets($mem, 1024)) !== false) {
                // sortie ?
                $l = trim($l);
                if ($l == 'END') {
                    break;
                }

                $m = array();
                // ITEM 42 [118 b; 1354717302 s]
                $r = preg_match('/^ITEM\s([^\s]+)\s/', $l, $m);
                if ($r != 1) {
                    return -5;
                }
                $a_key = $m[1];
                $a_slab[] = $a_key;
            }
        }

        // close
        @fclose($mem);
        unset($mem);
        // transform it;
        $keys = array();
        reset($slab);
        foreach ($slab as &$a_slab) {
            reset($a_slab);
            foreach ($a_slab as &$a_key) {
                $keys[] = $a_key;
            }
        }
        unset($slab);
        return $keys;
    }

    public function _saveModifiedData($requestData)
    {
        $this->Omemcache->set($requestData['key'], $requestData['data'], $this->memcacheConfig['second']);
    }

    public function _webSearchEncrypt($requestData)
    {
        $data = array(
            "action" => $requestData['method'],
            "request" => $requestData['request'],
            "types" => array(
                "airlinecode" => "CS",
                "faretype" => "EDCF",
            ),
        );
        $request = array(
            "data" => json_encode($data),
            "mode" => "air",
            "devMode" => true,
            "agencyId" => 101,
            "requestId" => 1,
        );
        $url = "https://preprodaws.atyourprice.net/AllWebServices2.0/webSearch2.1/webServiceSearch.php";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($request),
            CURLOPT_HTTPHEADER => array(
                // "Content-Type: application/json",
                "Content-Type: application/x-www-form-urlencoded",
                "Requestauthorization: ttApXaM3ky0g7VXFUSj7RccmRlWxmwfFOFTBDvsORjUO4FkMlrH1rJd8VXv5izBW4ilyDCajJSXBn5dxkIoBhpA0dTUlil5btgxUIx3hfPIqKJNVh1mnfKbgsVpA4kzlqW4XjX2oABFb8gUe64rbDqhVcwj4EJgSvaFDVyRmXklTp3XlXckotr9tLjnfQrXbprCbNilyswhFn5Qa70pofmHOtTR9aF9oUBpxJyV0S2v5Va3RYg52tDEKkmKkPHwz",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }

    }

}
$OcommonRequest = new commonRequest();
$OcommonRequest->_createMemcacheObject();

if ($result['status'] == 'N') {
    print_r(json_encode($result));
    exit;
}
if (isset($_POST) && !empty($_POST['action'])) {
    $request = $_POST;
    // print_r($request);die;
    switch ($request['action']) {
        case 'getAllKeys':
            $response = $OcommonRequest->_getAllKeysWithData();
            print_r(json_encode($response));
            break;
        case 'flushData':
            $response = $OcommonRequest->_flushMemcacheData();
            print_r(json_encode($response));
            break;
        case 'removeByKey':
            $OcommonRequest->_deleteByKey($request["key"]);
            $response = $OcommonRequest->_getAllKeysWithData();
            print_r(json_encode($response));
            break;
        case 'modifyData':
            $OcommonRequest->_saveModifiedData($request);
            print_r(json_encode($response));
            break;
        case 'webSearchEncrypt':
            $dbEncrypt = array("newEncryption", "oldEncryption", "newDecryption", "oldDecryption");
            if (!in_array($request['method'], $dbEncrypt)) {
                $OcommonRequest->_webSearchEncrypt($request);
            }
            break;
        default:
            print_r(json_encode(array("status" => "N", "response" => "Invalid Action")));
            break;
    }
}

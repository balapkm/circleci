<?php
/**
 * Infiniti standards framework.
 *
 * @package     Infiniti webservice framework
 * @author      Santhosh.M <santhosh.m@infinitisoftware.net>
 * @copyright   Copyright (c) 2018 Infiniti software solution
 * @version     Release: @V1
 * @link        http://www.infinitisoftware.net/
 * @date        2019-07-18 14:59:04
 **/
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>CommonWebservice Interface</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1"/>
      <link rel="icon" href="http://infinitisoftware.net/images/favicon.ico" type="image/x-icon"/>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0" />
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://cdn.rawgit.com/codekraft-studio/angular-page-loader/master/dist/angular-page-loader.css">
      <link rel="stylesheet" href="./custom-style.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
      <script type="text/javascript" src="https://cdn.rawgit.com/codekraft-studio/angular-page-loader/master/dist/angular-page-loader.min.js"></script>
      <script type="text/javascript">
        $(document).on('click','.radio label,.action .radio-inline',function(){
            $(this).addClass('active').siblings().removeClass("active")
        });

        var app = angular.module("webServices",['angular-page-loader']);
        app.controller("Interface",['$scope', '$http', '$httpParamSerializerJQLike','$rootScope',function ($scope, $http,$httpParamSerializerJQLike,$rootScope){
            $scope.splitLog = function(){
                if($scope.zipName=="" || typeof($scope.zipName)=='undefined'){
                    swal("Empty Zip File Name!","","error");
                    return false;
                }else if(typeof($scope.logType)=='undefined'){
                    swal("Select Log Split Type!","","error");
                    return false;
                }else if(typeof($scope.selectedFiles)=='undefined'){
                    swal("Select Zip Files!","","error");
                    return false;
                }else if($scope.selectedFiles.length!=2){
                    swal("Must Select Two Zip Files","","error");
                    return false;
                }
                var fd = new FormData();
                    angular.forEach($scope.selectedFiles,function(file){
                    fd.append('file[]',file);
                    fd.append('method',$scope.logType);
                    fd.append('zipName',$scope.zipName);
                    fd.append('action','splitXMLLog');
                });
                $rootScope.isLoading = true;
                $http({
                    method: 'post',
                    url: 'request.php',
                    data: fd,
                    headers: {'Content-Type': undefined},
                }).then(function(response){
                    console.log(response.data);
                    if(response.data.status=='Y'){
                        $rootScope.isLoading = false;
                        window.location = response.data.response;
                    }else{
                        swal("Error!",response.data.response,"error");
                    }
            });
            };

            $scope.responseDiv = true;
            $scope.submit =function(){
                if($scope.request=="" || typeof($scope.request)=='undefined'){
                    swal("Enter something to encrypt or decrypt","",'error');
                    return false;
                }
                if($scope.method=="" || typeof($scope.method)=="undefined"){
                    swal("Select something to encrypt or decrypt",'','error');
                    return false;
                }
                $rootScope.isLoading = true;
                $scope.responseDiv = true;
                var request = {
                        "action" : "webSearchEncrypt",
                        "method" : $scope.method,
                        "request" : $scope.request
                    };
                console.log(request);
                $http({
                    method : 'POST',
                    url : 'request.php',
                    headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                    data : $httpParamSerializerJQLike(request)
                }).then(function(response)
                {
                    console.log(response.data);
                    $rootScope.isLoading = false;
                    $scope.response = response.data.data;
                    $scope.responseDiv = false;
                });
            };

            $scope.onChange = function()
            {
                $(".action .radio-inline").attr("active",false);
                $scope.responseDiv = true;
                $scope.response = "";
            };

        }]);
      </script>
      <style>
        html{
            height: 100%;
        }
        body{
            min-height: 100%;
        }
        .content-area{
            padding:30px;
        }
        .action{
            padding:20px 0;
        }
        .custom-btn{
            color: #f5f5f5;
            background: #271515;
            border:0;
            outline:none;
            padding:5px 10px;
            border-radius:5px;
        }
        body{
            position: relative;
            padding-bottom:80px;
        }
        .contentarea{
            margin-top: 50px;
            font-size: 16px;
        }
        .custom-btn{
            padding: 5px 15px;
            font-family: arial;
            background:#271515
            border-radius: 5px;
            border: 0;
            color: #fff;
        }
        .sxml{
            display: none;
        }
        textarea{
            resize: none;
        }
        .row .col-sm4,.row .col-sm-8{
            margin-top:60px;
            margin-bottom:30px;
        }
        .radio{
            margin:0;
        }
        .radio input,.action input{
            visibility: hidden;
        }
        .radio label,.action .radio-inline{
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .active{
            background: #271515;
            color: #fff
        }
        [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
            display: none !important;
        }
    </style>
    </head>
    <body ng-app="webServices" ng-controller="Interface" ng-cloak>
        <page-loader flag="isLoading" bg-color="whitesmoke"></page-loader>
        <?php require_once "./menuNavbar.php";?>
        <div class="row endecrypt">
            <div class="content-area col-sm-8 col-sm-offset-2">
                <div class="form-group">
                  <label for="request">Request/Response:</label>
                  <textarea class="form-control" ng-model="request" rows="5" id="request" name="request"></textarea>
                </div>
                <div class="action">
                    <label for="method" style="margin-right: 2%">WebSearch: </label>
                    <label class="radio-inline" style="margin-right: 1%"><input type="radio" name="method" ng-change="onChange()" ng-model="method" value="requestEncrypt">Request Encryption</label>
                    <label class="radio-inline" style="margin-right: 1%"><input type="radio" name="method" ng-change="onChange()" ng-model="method" value="requestDecrypt">Request Decryption</label>
                    <label class="radio-inline" style="margin-right: 1%"><input type="radio" name="method" ng-change="onChange()" ng-model="method" value="responseEncrypt">Response Encryption</label>
                    <label class="radio-inline" style="margin-right: 1%"><input type="radio" name="method" ng-change="onChange()" ng-model="method" value="responseDecrypt">Response Decryption</label>
            		<!-- <br>
            		<br>
                    <label for="method" style="margin-right: 2%">BZ & Base64: </label>
                    <label class="radio-inline" style="margin-right: 1%"><input type="radio" name="method" ng-change="onChange()" ng-model="method" value="bzCompress">BZ Compression</label>
                    <label class="radio-inline" style="margin-right: 1%"><input type="radio" name="method" ng-change="onChange()" ng-model="method" value="bzDecompress">BZ Decompression</label> -->
            		<br>
            	</div>
                <div class="text-center">
                    <button type="submit" class="custom-btn" id="encryptBtn" ng-click="submit()">Submit</button>
                </div>
                <div class="form-group" ng-hide="responseDiv">
                  <label for="response">Response:</label>
                  <textarea class="form-control" rows="5" id="response" name="response" >{{response}}</textarea>
                </div>
            </div>
        </div>
    </body>
</html>

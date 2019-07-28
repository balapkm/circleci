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
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>CommonWebservice Interface</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1"/>
      <link rel="icon" href="http://infinitisoftware.net/images/favicon.ico" type="image/x-icon"/>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
      <script>
        var app = angular.module("myApp",[]);
        app.controller("myCtrl",['$scope','$http', '$httpParamSerializerJQLike',function($scope,$http,$httpParamSerializerJQLike)
        {
            $scope.handleMemcache = function(Action='getAllKeys',Key='')
            {
                var data = {
                    action : Action
                };
                if(Key!='') {
                    data.key = Key;
                }
                // console.log(data);
                if(Action!='getAllKeys') {
                    swal({
                        title: (Action=='flushData') ? "Do you want to flush data?" : "Are you sure to delete?",
                        text: "",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                    if (willDelete) {
                        swal("Success", {
                        icon: "success"
                        });
                        $http({
                            method: 'post',
                            url: 'request.php',
                            headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                            data : $httpParamSerializerJQLike(data)
                        }).then(function(response){
                            // console.log(response.data);
                            $scope.storedKeys = "";
                            $scope.errorMessage = "";
                            if(response.data.status=="Y") {
                                $scope.storedKeys = response.data.response;
                            } else {
                                $scope.errorMessage = response.data.response;
                            }
                        });
                    } else {
                        return false;
                    }
                    });
                } else {
                    $scope.storedKeys = "";
                    $scope.errorMessage = "";
                    $http({
                        method: 'post',
                        url: 'request.php',
                        headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                        data : $httpParamSerializerJQLike(data)
                    }).then(function(response){
                    // console.log(response.data);
                    if(response.data.status=="Y") {
                        $scope.storedKeys = response.data.response;
                    } else {
                        $scope.errorMessage = response.data.response;
                    }
                });
                }
            }
            $scope.handleMemcache();

            $scope.saveModifiedData = function(Key) {
                swal({
                    title: "Do you want to modify?",
                    text: "",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        swal("Success", {
                        icon: "success"
                        });
                        $scope.modifiedData = $("#modifiedData").val();
                        var data = {
                            action : "modifyData",
                            key : Key,
                            data : $scope.modifiedData
                        };
                        // console.log(data);
                        $http({
                            method: 'post',
                            url: 'request.php',
                            headers : {'Content-Type': 'application/x-www-form-urlencoded'},
                            data : $httpParamSerializerJQLike(data)
                        }).then(function(response){
                            // console.log(response.data);
                            if(response.data.status=="Y") {
                                $scope.storedKeys = response.data.response;
                            } else {
                                $scope.errorMessage = response.data.response;
                            }
                        });
                    } else {
                        return false;
                    }
                });
            }
        }]);
      </script>
    </head>
    <body ng-app="myApp" ng-controller="myCtrl">
        <?php require_once "./menuNavbar.php";?>
        <div class="container" style="margin-top:60px;">
            <div class="text-right">
                <button class="btn btn-danger " ng-click="handleMemcache('flushData')" >Flush</button>
            </div>
            <div>
                <label>Memcached stored data:</label>
                <ul class="list-group">
                    <li class="list-group-item" ng-repeat="x in storedKeys">{{x.key}}
                        <span class="badge" style="padding:6px" ng-click="handleMemcache('removeByKey',x.enKey)"><span class="glyphicon glyphicon-remove"></span></span>
                        <a class="badge" style="padding:6px" data-toggle="collapse" data-target="#{{x.enKey}}"><span class="glyphicon glyphicon-edit"></span></a>
                        <div id="{{x.enKey}}" class="collapse div-collapse" style="position: relative;top: 10px;border: 1px solid rgb(118, 118, 118);margin: 10px -15px 0px;">
                            <textarea name="myArea" rows="15" class="form-control" wrap="hard" id="modifiedData">{{x.value}}</textarea>
                            <div class="text-center">
                                <button class="btn btn-primary" style="margin:8px;" ng-click="saveModifiedData(x.enKey)" data-toggle="collapse" data-target="#{{x.enKey}}">Modify</button>
                                <button class="btn btn-default" style="margin:8px;" data-toggle="collapse" data-target="#{{x.enKey}}">Close</button>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item" ng-if="errorMessage">{{errorMessage}}</li>
                </ul>
            </div>
        </div>
    </body>
</html>

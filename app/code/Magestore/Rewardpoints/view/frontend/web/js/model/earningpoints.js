/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';
        var tempAllRewardpointsData = window.rewardPointsInfo;
        var allData = ko.observable(tempAllRewardpointsData);
        return {
            allData: allData,
            getData: function(){
                return allData;
            },

            setData: function(data){
                allData(data);
            }
        }
    }
);

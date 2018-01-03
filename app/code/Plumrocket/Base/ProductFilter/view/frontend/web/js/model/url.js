/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    "jquery"
], function($){
    "use strict";

    return {

        separator: '=',
        isSeoFriendly: true,
        categoryUrlSufix: '',

        getManualUrl: function(options, url)
        {
            var self = this;

            Object.keys(options)
                .sort()
                .forEach(function(request, i) {
                    var params = options[request];
                    params.sort();
                    if (self.isSeoFriendly) {
                        $.each(params, function(key, value) {
                            url = self.getUrl(request, value, null, url);
                        });
                    } else {
                        var value = params.join(',');
                        url = self.getUrl(request, value, null, url);
                    }
                });

            return url;
        },

        beforeProcess: function(url) {
            if (this.isSeoFriendly) {
                url = url.replace(this.categoryUrlSufix,'');
                var p = url.indexOf('?');
                if (p > 0) {
                    url = url.substr(0, p) + this.categoryUrlSufix + url.substr(p);
                } else {
                    url += this.categoryUrlSufix;
                }
            }

            return url;
        },

        removePriceFromUrl: function(url) {
            var priceFilters = url.match(/(\/price-[A-Za-z0-9_.,%]+)/g);
            if (priceFilters) {
                priceFilters = jQuery.unique(priceFilters);
                if (priceFilters.length > 1) {
                    for (var i = 0; i < priceFilters.length - 1; i++ ) {
                        url  = url.replace(priceFilters[i], '');
                    }
                }
            }
            return url;
        },

        getCurrentUrl: function() {
            return window.location.href.replace(window.location.search, '');
        },

        isParams: function() {
            return window.location.search.length;
        },

        getParamsFromUrl: function() {
            var query = location.search.substr(1);
            var result = {};

            if (query) {
                query.split("&").forEach(function(part) {
                    var item = part.split("=");
                    result[item[0]] = [];
                    var params = decodeURIComponent(item[1]).split(',');
                    params.forEach(function(param) {
                        result[item[0]].push(param);
                    });
                });
            }
            return result;
        },

        convertValue: function(val) {
            val = val.toLowerCase();
            val = val.replace('-', '_');
            val = val.replace(' ', '_');
            return val;
        },

        //Method copied from jquery.param function
        param: function( a ) {

            var self = this;

            var prefix,
                s = [],
                add = function( key, value ) {
                    //Add parameters to url
                    value = $.isFunction( value ) ? value() : ( value == null ? "" : value );
                    s[ s.length ] = encodeURIComponent( key ) + self.separator + encodeURIComponent( value );
                };
            //For each parameters
            //Parameter key is name of var
            $.each( a, function(name, value) {
                add( name, value );
            });
            //Check is seo friendly url enabled
            var joinParam = self.isSeoFriendly ? '/' : '&';

            return s.join( joinParam ).replace( "/%20/g", "+" );
        },

        getUrl: function (pName, pValue, defValue, url, remove) {

            if (typeof pName == 'undefined') {
                return url;
            }

            if (typeof url == 'undefined') {
                var urlPaths = document.location.href.split('?');
            } else {
                var urlPaths = url.split('?');
            }
            var pathname = urlPaths[0];

            var decode = window.decodeURIComponent;
                // pathname = window.location.pathname,
                /*urlPaths = document.location.href.split('?');*/

            if (!this.isSeoFriendly) {
                //If seo friendly url not used, then split by ampersant
                var urlParams = urlPaths[1] ? urlPaths[1].split('&') : [];
            } else {
                //If using seo friendly url, then detect parameters
                //And split by slash
                var _urlParams = pathname.split('/')
                    urlParams = [];

                for (var i = _urlParams.length - 1; i > 0 ; i--) {
                    //If param look like our parameters (i mean parameter has our separator)
                    //In this case we consider, that it is parameter
                    if (_urlParams[i].search(this.separator) > 0) {
                        //If paremeter not aplied currently
                        if (!pathname.search(_urlParams[i]))
                            urlParams.push(_urlParams[i]);
                    } else {
                        //Search from the end of url path and if there no sesaparator break loop
                        //It means that next parameters not consided to filter options
                        break;
                    }
                }
            }

            var pData = {},
                baseUrl = urlPaths[0],
                parameters;

            var _separator = this.isSeoFriendly ? this.separator : '=';
            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split(_separator);
                //Creating array with parameters
                //Key is parameter name, value is value =)
                pData[decode(parameters[0])] = parameters[1] !== undefined ? decode(parameters[1].replace(/\+/g, '%20')) : '';
            }
            pData[pName] = pValue;
       /*     if (pValue == defValue) {
                delete pData[pName];
            }*/

            //Split all parameters to sting
            //this.param based on jQuery.param
            pData = this.param(pData);

            //Build final url
            if (this.isSeoFriendly) {

                if (remove) {
                    var regex = new RegExp("(\/" + pName + "-[a-z0-9]+)");
                    baseUrl = baseUrl.replace(regex, "");
                }

                baseUrl = baseUrl.replace("/" + pName + '-' + pValue, "");

                //If last symbol of url not slash
                var slash = pathname[pathname.length-1] != '/' ? '/' :'';
                //Add parameters to base url
                var actionUrl = baseUrl + (pData.length ? slash + pData : '');
                //Add get parameters if it exists
                //
                actionUrl += urlPaths[1] ? '?' + urlPaths[1] : '';
            } else {
                var actionUrl = baseUrl + (pData.length ? '?' + pData : '');
            }

            //Send ajax request
            return actionUrl;
        }
    }
});



  // define is used to register a module in require js 
define([
          "jquery"
     ], function($) {
 
     console.log('called');
        //defining our plugin
    $.fn.mycomponent = function(options) {
         
        // get initialised data here
        console.log(options);
         
 
        // 'your plugin is ready do whatevent you want to do'
                     
    };
 
 
});
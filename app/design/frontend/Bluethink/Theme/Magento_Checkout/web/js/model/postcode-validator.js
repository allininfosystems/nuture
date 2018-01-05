/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(['mageUtils'], function (utils) {
    'use strict';
    return {
        validatedPostCodeExample: [],
        validate: function(postCode, countryId) {
            var patterns = window.checkoutConfig.postCodes[countryId];
            this.validatedPostCodeExample = [];

            if (!utils.isEmpty(postCode) && !utils.isEmpty(patterns)) {
                for (var pattern in patterns) {
				     //  alert('1');
                    if (patterns.hasOwnProperty(pattern)) {
					// Custom Code JQuery 
					
				// alert(postCode); 
					 
			 //test(postCode);
				
	require(["jquery"],function(jQuery) {
        jQuery(document).ready(function() {
		
jQuery.post("http://demo.demotoday.info/nuture/custom/index/index/?postcode="+postCode, { a: 1 })
  .done(function( data ) {
	var obj = JSON.parse(data);
    //alert(obj.city);
		jQuery("input[type='text'][name='city']").val(obj.city);
		
	//$('select[name="zoom_cat[]"] option[value="-1"]').attr('selected', 'selected');

jQuery('select[name="region_id"] option').filter(function() { 
    return (jQuery(this).text() == obj.state); //To select Blue
}).prop('selected', true);

 // jQuery('select[name="region_id"]').val(obj.state);

  });
	
	/* var customurl = "http://demo.demotoday.info/nuture/custom/index/index/?postcode="+postCode;
            jQuery.ajax({
                url: customurl,
                type: 'GET',
            complete: function(response) 
			{ 
			
			//var obj = JSON.parse(response.responseText.city);
    // var obj = JSON.parse();
    // alert(obj.name);
	
	//alert(country); 
	 var json = JSON.parse(response)
    
	
	alert(json.response);
		 
	//input[type=text]
	
	jQuery("input[type='text'][name='city']").val(city);
	
//	jQuery("select[name='region_id']").val(state);
	
	
	//jQuery("input[type='text'][name='city']").val(state);
	//alert(title);
   
  

		
		// console.log(state+' '+country);
				
          // alert('Ajax response State!'+state);
		   
		   },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            }); */
			
        });
    });

	//end  Custom Code JQuery  
                        this.validatedPostCodeExample.push(patterns[pattern]['example']);
                        var regex = new RegExp(patterns[pattern]['pattern']);
                        if (regex.test(postCode)) {
                            return true;
						alert('3');
							
							
                        }
                    }
                }
                return false;
            }
            return true;
        }
    }
});

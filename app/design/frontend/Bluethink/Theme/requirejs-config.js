var config = {
    
   paths: {
       'jquery.owl.carousel': 'Magento_Theme/js/owl.carousel',
       'jquery.mousewheel.min': 'Magento_Theme/js/jquery.mousewheel.min',
        'jquery.bxslider': 'js/jquery.bxslider',
       'jquery.bootstrap.min': 'js/bootstrap.min'
    

    },
    shim: {
        'jquery.owl.carousel':{
            deps:['jquery']
        },
        'jquery.mousewheel.min':{
            deps:['jquery']
        },
        'jquery.bxslider':{
         dep:['jquery']
        },
        'jquery.bootstrap.min':{
         dep:['jquery']
        }
        
    }
};
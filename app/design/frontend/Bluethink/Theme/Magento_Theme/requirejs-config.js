var config = {
  
  paths:{
    "jquery.bootstrap.min":"Magento_Theme/js/bootstrap.min",
    "jquery.mousewheel.min":"Magento_Theme/js/mousewheel.min",
    "jquery.owl.carousel":"Magento_Theme/js/owl.carousel",
    "jquery.main":"Magento_Theme/js/main",
    "jquery.muuri":"Magento_Theme/js/muuri",
    "jquery.slide":"Magento_Theme/js/slide",
    "jquery.theme":"Magento_Theme/js/theme",
    "jquery.velocity.min":"Magento_Theme/js/velocity.min",
    
  },
  shim:{
    'jquery.bootstrap.min':{
        deps:['jquery']
    }
    ,
    'jquery.mousewheel.min':{
        deps:['jquery']
    }
    ,
    'jquery.main':{
        deps:['jquery']
    }
    ,
    'jquery.muuri':{
        deps:['jquery']
    }
    ,
    'jquery.owl.carousel':{
        deps:['jquery']
    }
    ,
    'jquery.theme':{
        deps:['jquery']
    },
    'jquery.velocity.min':{
        deps:['jquery']
    }
    ,
    'jquery.slide':{
        deps:['jquery']
    }
  }
};



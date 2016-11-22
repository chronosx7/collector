(function(){
    'use strict';
    
    angular.module('collector_app', ['formly',  'formlyBootstrap', 'ngResource', 'ui.bootstrap'])
    .controller('AppController', AppController);
    
    function AppController($scope, ModalWindow){
        var vm = this;
        
        vm.form = {};
        vm.request_token;
        vm.errors = [];
        
        vm.form_fields =
    }
    
    
})();










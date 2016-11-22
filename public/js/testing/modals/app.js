(function(){
    'use strict';
    
    angular.module('collector_app', ['ui.bootstrap'])
    .controller('AppController', AppController);
    
    angular.module('collector_app').controller('ModalCtrl', ModalInstanceCtrl);
    angular.module('collector_app').factory('ModalWindow', ModalWindowFactory);
    
    function AppController($scope, ModalWindow){
        $scope.open_alert = function () {
            ModalWindow.open('alert', {
                title: 'Alert',
                message: 'Just chilling calling an Alert Modal Window.',
            });
        };
        $scope.open_error = function () {
            ModalWindow.open('error', {
                title: 'Boredom Error',
                message: 'Going nuts out of boredom.',
            });
        };
        
    }
    
    function ModalWindowFactory($uibModal){
        function open(modal_type, options) {
            var size = options.size || '';
            modal_type = modal_type || 'ALERT';
            options.title = options.title || 'Alert';
            options.message = options.message || '';
            switch(modal_type.toUpperCase()){
                case 'ERROR':{
                    options.bg_color = 'c9302c';
                    options.btn_class = 'danger';
                    break;
                }
                default:{
                    options.bg_color = '286090';
                    options.btn_class = 'primary';
                    break;
                }
            }

            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'ModalContent.html',
                controller: 'ModalCtrl',
                size: size,
                resolve: {
                    params: function () {
                        return options;
                    }
                }
            });
        }
        
        return {
            open: open,
        };
    }
    
    function ModalInstanceCtrl($scope, $uibModalInstance, params){
        $scope.elems = params;
        $scope.ok = function () {
            $uibModalInstance.close();
        };
    }
    
})();










(function(){
    'use strict';
    
    angular.module('collector_app', ['formly',  'formlyBootstrap', 'ngDroplet', 'initialValue', 'ngResource', 'ui.bootstrap'])
    .controller('AppController', AppController);
    
    function AppController($scope, CardResource, ModalWindow, CardInteraction){
        var vm = this;
        var ux = CardInteraction;
        
        vm.request_token;
        vm.card_info = {};
        vm.visible_cards = [];
        vm.current_page = 1;
        vm.user_id = $('#_user_id').val();
        vm.target_user = $('#_target_user').val();

        CardResource.get_user_cards(vm.target_user).then(function(response){
            if(response.status == 'ok'){
                ux.set_all_cards(response.data);
                vm.current_page = 1;
                vm.visible_cards = ux.get_visible_cards(vm.current_page);
            }
            else{
                ModalWindow.open('error', {
                    title: 'Error',
                    message: error_message,
                });
            }
        },
        function(error){
            ModalWindow.open('error', {
                title: 'Error',
                message: error_message,
            });
        });
        vm.card_is_editable = function(current_user, owner){
            return ux.card_is_editable(current_user, owner);
        };

        vm.get_card_info = function(id, image){
            var res = CardResource.get_card_info(id, image);
            if(res.type == 'card_info'){
                vm.card_info = res;
            }
            else{
                var error_message = 'Unable to retrieve card information.';
                res.promise.then(function(response){
                    if(response.status == 'ok'){
                        vm.card_info = response;
                    }
                    else{
                        ModalWindow.open('error', {
                            title: 'Error',
                            message: error_message,
                        });
                    }
                },
                function(error){
                    ModalWindow.open('error', {
                        title: 'Error',
                        message: error_message,
                    });
                });
            }
        };
        
        vm.page_changed = function (){
            ux.display_cards_list();
        };
    }
    
    
})();










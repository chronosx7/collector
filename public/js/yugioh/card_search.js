(function(){
    'use strict';
    
    angular.module('collector_app', ['formly', 'ngResource', 'formlyBootstrap',  'initialValue', 'ui.bootstrap'])
    .controller('AppController', AppController);
    
    function AppController($scope, Form, CardResource, ModalWindow, CardInteraction){
        var vm = this;
        var ux = CardInteraction;
        
        vm.form = {};
        vm.request_token;
        vm.card_info = {};
        vm.visible_cards = [];
        vm.current_page = 1;
        vm.user_id = $('#_user_id').val();

        CardResource.get_form_options().then(function(response){
            if(response.status == 'ok'){
                vm.formFields = Form.getForm(response.data, 'SEARCH');
            }
            else{
                console.log('Response error');
                ModalWindow.open('error', {
                    title: 'Error',
                    message: Form.error_message(),
                });
            }
        },
        function(error){
            console.log('Request error');
            ModalWindow.open('error', {
                title: 'Error',
                message: Form.error_message(),
            });
        });
        
        vm.submit_data = function(){
            var error_message = 'Unable to perform card search.';
            CardResource.get_cards(vm.form).then(function(response){
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
        }
        
        vm.card_is_editable = function(current_user, owner){
            return ux.card_is_editable(current_user, owner);
        };
        
        vm.get_message = function(){
            ux.get_message(vm._message);
        };

        vm.get_card_info = function(id, image){
            var res = CardResource.get_card_info(id, image);
            
            if(res.type == 'card_info'){
                vm.card_info = res;
            }
            else{
                res.promise.then(function(response){
                    if(response.status == 'ok'){
                        vm.card_info = response;
                    }
                    else{
                        ModalWindow.open('error', {
                            title: 'Error',
                            message: response.message,
                        });
                    }
                },
                function(error){
                    ModalWindow.open('error', {
                        title: 'Error',
                        message: response.message,
                    });
                });
            }
        };
        
        vm.page_changed = function (){
            //ux.display_cards_list();
        };
    }
})();










(function(){
    'use strict';
    
    angular.module('collector_app', ['formly', 'ngResource', 'formlyBootstrap',  
    'initialValue', 'ngResource', 'ui.bootstrap', 'dndLists'])
    .controller('AppController', AppController);
    
    function AppController($scope, Form, CardResource, CardInteraction, 
    DeckResource, ModalWindow){
        var vm = this;
        var ux = CardInteraction;
        
        vm.form = {};
        vm.request_token;
        vm.total_items = 0;
        vm.card_info = {};
        vm.deck_name = '';
        vm.visible_cards = [];
        vm.current_page = 1;
        vm.user_id = $('#_user_id').val();

        // Arrays of cards
        vm.main_deck_cards = [];
        vm.extra_deck_cards = [];
        vm.side_deck_cards = [];
        vm.dumped_cards = [];

        vm.selected_deck = 'Main';
        
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
        
        vm.card_is_editable = function(current_user, owner){
            return ux.card_is_editable(current_user, owner);
        };

        vm.page_changed = function (){
            ux.display_cards_list();
        };

        vm.show_info_tab = function(){
            $('#deck_construction_tabs a[href="#card_details"]').tab('show');
        };

        vm.select_deck = function(deck){
            vm.selected_deck = deck;
        }; 
        
        vm.drop_in_deck = function(event, index, item, external, type, deck){
            deck = deck != undefined? deck.toUpperCase(): 'MAIN';
            
            var res = true;
            var error_message = '';
            var main_count = count_card_in_deck(vm.main_deck_cards, item.id);
            var extra_count = count_card_in_deck(vm.extra_deck_cards, item.id);
            var side_count = count_card_in_deck(vm.side_deck_cards, item.id);

            if(main_count + extra_count + side_count >= 3){
                error_message = 'There are 3 copies of this card in the Deck.';
                error_message += main_count + ' in Main. ' + extra_count + ' in Extra. ' + side_count 
                + ' in Side.';
                res = false;
            }
            else{
                switch(deck){
                    case 'MAIN':{
                        if(vm.main_deck_cards.length < 60){
                            add_card_to_deck(vm.main_deck_cards, item);
                        }
                        else{
                            error_message = 'The Main Deck already has 60 cards.';
                            res = false;
                        }
                        break;
                    }
                    case 'EXTRA':{
                        if(vm.extra_deck_cards.length < 15){
                            add_card_to_deck(vm.extra_deck_cards, item);
                        }
                        else{
                            error_message = 'The Extra Deck already has 15 cards.';
                            res = false;
                        }
                        break;
                    }
                    case 'SIDE':{
                        if(vm.side_deck_cards.length < 15){
                            add_card_to_deck(vm.side_deck_cards, item);
                        }
                        else{
                            error_message = 'The Side Deck already has 15 cards.';
                            res = false;
                        }
                        break;
                    }
                }
            }
            if(error_message != ''){
                ModalWindow.open('error', {
                    title: 'Error',
                    message: error_message,
                });
            }
            return res;
        };
        
        vm.remove_from_main_deck = function(card_index){
            remove_card_from_deck(vm.main_deck_cards, card_index);
        };

        vm.remove_from_extra_deck = function(card_index){
            remove_card_from_deck(vm.extra_deck_cards, card_index);
        };

        vm.remove_from_side_deck = function(card_index){
            remove_card_from_deck(vm.side_deck_cards, card_index);
        };

        vm.has_selected_card = function(){
            return ux.has_selected_card();
        };
        
        vm.clear_dumped_cards = function(){
            vm.dumped_cards.splice(0, vm.dumped_cards);
        };

        vm.upload_deck = function(){
            var error_message = 'Could not save deck. Please try again later.';

            var main_deck_cards = [];
            var extra_deck_cards = [];
            var side_deck_cards = [];
            vm.main_deck_cards.forEach(function(element, index){
                main_deck_cards.push(element.id);
            });
            vm.extra_deck_cards.forEach(function(element, index){
                extra_deck_cards.push(element.id);
            });
            vm.side_deck_cards.forEach(function(element, index){
                side_deck_cards.push(element.id);
            });

            var resource = DeckResource.upload_deck({
                _token: vm.request_token,
                deck_name: vm.deck_name,
                main_deck: main_deck_cards,
                extra_deck: extra_deck_cards,
                side_deck: side_deck_cards,
            }).then(
                function(Response){
                    if(response.status == 'ok'){

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
                }
            );
        };

        vm.deck_is_valid = function(){
            var res = true;
            var total_cards = vm.main_deck_cards.length + vm.extra_deck_cards.length + vm.side_deck_cards.length; 
            if(vm.deck_name.length < 5){
                res = false;
            }
            else if(total_cards < 1){
                res = false;
            }

            return res;
        };

        vm.empty_deck = function(deck){
            var items = 0;
            switch(deck){
                case 'MAIN':{
                    items = vm.main_deck_cards.length;
                    for(var i = 0; i < items; i++){
                        vm.main_deck_cards.pop();
                    }
                    break;
                }
                case 'EXTRA':{
                    items = vm.extra_deck_cards.length;
                    for(var i = 0; i < items; i++){
                        vm.extra_deck_cards.pop();
                    }
                    break;
                }
                case 'SIDE':{
                    items = vm.side_deck_cards.length;
                    for(var i = 0; i < items; i++){
                        vm.side_deck_cards.pop();
                    }
                    break;
                }
            }
        };
        
        vm.is_selected_card = function(index, origin){
            return ux.is_selected_card(index, origin);
        };

        vm.select_card = function(card, origin, index){
            ux.select_card(card, origin, index);
        };

        vm.btn_move_to_main_deck = function(){
            var new_card = copy_card_object(ux.get_selected_card());

            switch(ux.get_selected_card().origin){
                case 'SEARCH':
                case 'MAIN':{
                    if(new_card.card_type == 'ExtraCard'){
                        vm.drop_in_deck({}, -1, new_card, false, new_card.card_type, 'EXTRA');
                    }
                    else if(new_card.card_type == 'MainCard'){
                        vm.drop_in_deck({}, -1, new_card, false, new_card.card_type, 'MAIN');
                    }
                    break;
                }
                case 'EXTRA':{
                    vm.drop_in_deck({}, -1, new_card, false, new_card.card_type, 'EXTRA');
                    break;
                }
                case 'SIDE':{
                    vm.remove_from_side_deck(ux.get_selected_card().index);
                    if(new_card.card_type == 'ExtraCard'){
                        vm.drop_in_deck({}, -1, new_card, false, new_card.card_type, 'EXTRA');
                    }
                    else if(new_card.card_type == 'MainCard'){
                        vm.drop_in_deck({}, -1, new_card, false, new_card.card_type, 'MAIN');
                    }
                    break;
                }
            }
        };

        vm.btn_move_to_side_deck = function(){
            var new_card = copy_card_object(ux.get_selected_card());

            switch(ux.get_selected_card().origin){
                case 'MAIN':{
                    vm.remove_from_main_deck(ux.get_selected_card().index);
                    vm.drop_in_deck({}, -1, new_card, false, new_card.card_type, 'SIDE');
                    break;
                }
                case 'EXTRA':{
                    vm.remove_from_extra_deck(ux.get_selected_card().index);
                    vm.drop_in_deck({}, -1, new_card, false, new_card.card_type, 'SIDE');
                    break;
                }
                case 'SEARCH':
                case 'SIDE':{
                    vm.drop_in_deck({}, -1, new_card, false, new_card.card_type, 'SIDE');
                    break;
                }
            }
        };

        vm.btn_remove_from_deck = function(){
            switch(ux.get_selected_card().origin){
                case 'MAIN':{
                    vm.remove_from_main_deck(ux.get_selected_card().index);
                    break;
                }
                case 'EXTRA':{
                    vm.remove_from_extra_deck(ux.get_selected_card().index);
                    break;
                }
                case 'SIDE':{
                    vm.remove_from_side_deck(ux.get_selected_card().index);
                    break;
                }
            }
        };

        function copy_card_object(card){
            var new_card = {
                id: card.id,
                name: card.name,
                card_type: card.card_type,
                active_file: card.active_file,
            };
            return new_card;
        }
        
        function remove_card_from_deck(deck, card_index){
            deck.splice(card_index, 1);
        }
        
        function add_card_to_deck(deck, card){
            deck.push(card);
        }
        
        function count_card_in_deck(deck, card_id){
            var cant = 0;
            for(var i = 0; i < deck.length; i++){
                if(deck[i].id == card_id){
                    cant++;
                }
            }
            return cant;
        }

    }
})();










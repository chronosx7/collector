(function(){
    'use strict';
    angular.module('collector_app').factory('CardInteraction', CardInteractionFactory);
    
    function CardInteractionFactory(){
        // Private attributes. Created with 'var'
        var img_per_page = 20;

        // Public attributes. Prefixed with 'this'
        var all_cards = [];
        var visible_cards = [];
        var current_page = 1;
        var selected_card = null;
        
        ////
        function has_selected_card(){
            return selected_card === null? false: true;
        }

        ////
        function is_selected_card(index, origin){
            var res = false;

            if(selected_card != null && selected_card.index == index 
            && selected_card.origin == origin){
                res = true;
            }
            return res;
        }

        function select_card(card, origin, index){
            selected_card = card;
            selected_card.origin = origin;
            selected_card.index = index;
        }

        function get_visible_cards(current_page){
            var first_index = (current_page - 1) * img_per_page;
            var last_index = first_index + img_per_page;
            var visible_cards = [];

            if(first_index + img_per_page >= all_cards.length){
                last_index = all_cards.length;
            }
            // Loads new card list
            for(var i = first_index; i < last_index; i++){
                visible_cards.push(all_cards[i]);
            }
            return visible_cards;
        }

        function set_all_cards(cards){
            all_cards = cards;
        }

        function get_selected_card(){
            return selected_card;
        }

        function card_is_editable(current_user, owner){
            //return Math.random() >= 0.75? true: false;
            return current_user == owner? true: false;
        }

        return {
            // Functions
            has_selected_card: has_selected_card,
            is_selected_card: is_selected_card,
            select_card: select_card,
            set_all_cards: set_all_cards,
            get_visible_cards: get_visible_cards,
            get_selected_card: get_selected_card,
            card_is_editable: card_is_editable,
        };
    }
    
})();
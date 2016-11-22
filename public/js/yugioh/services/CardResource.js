(function(){
    // Yu-Gi-Oh-exclusive card resource service
    
    'use strict';
    angular.module('collector_app').factory('CardResource', CardResourceFactory);
    
    function CardResourceFactory($resource){
        var card_cache = [];
        var card_cache = [];
        var cache_size_limit = 20;
        
        function get_form_options(){
            var source = $resource('/data/yugioh/options', {}, {
                query:{method: 'get', isArray: false, 
                cancellable: false}
            });
            return source.query().$promise.then(
                function(response){
                    response.status = 'ok';
                    return response;
                },
                function(response){
                    response.status = 'error';
                    return response;
                }
            );
        }
        
        function is_card_in_cache(id){
            var stored_cards = card_cache.length;
            var res = false;
            for(var i = 0; i < stored_cards; i++){
                if(card_cache[i].id == id){
                    res = true;
                    break;
                }
            }
            return res;
        }

        function store_card_in_cache(card){
            while(card_cache.length >= cache_size_limit){
                card_cache.splice(0, 1);
            }
            card_cache.push(card);
        }

        function get_card_in_cache(id){
            var stored_cards = card_cache.length;
            var res = null;
            for(var i = 0; i < stored_cards; i++){
                if(card_cache[i].id == id){
                    res = card_cache[i];
                    break;
                }
            }
            return res;
        }
        
        function get_card_info(id, image){
            var info = is_card_in_cache(id);
            var res = {};
            if(is_card_in_cache(id)){
                res = get_card_in_cache(id);
                res.active_file = image;
                res.type = 'card_info';
            }
            else{
                var resource = $resource('/games/yugioh/cards/:id', {}, {
                    query:{method: 'get', isArray: false, cancellable: false}
                });
                
                res.type = 'promise';
                res.promise = resource.query({id: id}).$promise.then(
                    function(response){
                        response.status = 'ok';
                        response.id = id;
                        response.active_file = image;
                        store_card_in_cache(response);

                        return response;
                    },
                    function(response){
                        response.status = 'error';
                        response.message = 'Unable to retrieve card information.';

                        return response;
                    }
                );
            }
            return res;
        }

        function get_cards(data){
            var resource = $resource('/games/yugioh/cards/search', {}, {
                query:{method: 'get', isArray: false, cancellable: false}
            });
            
            return resource.query(data).$promise.then(
                function(response){
                    response.status = 'ok';
                    return response;
                },
                function(response){
                    response.status = 'error';
                    return response;
                }
            );
        }
        
        function get_user_cards(id){
            var resource = $resource('/games/yugioh/user_cards/:id', {}, {
                query:{method: 'get', isArray: false, cancellable: false}
            });
            
            return resource.query({id: id}).$promise.then(
                function(response){
                    response.status = 'ok';
                    response.user_id = id;
                    return response;
                },
                function(response){
                    response.status = 'error';
                    return response;
                }
            );
        }
        
        return {
            get_form_options: get_form_options,
            get_cards: get_cards,
            get_card_info: get_card_info,
            get_user_cards: get_user_cards,
        };
    }
    
})();
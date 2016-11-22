(function(){
    'use strict';
    angular.module('collector_app').factory('DeckResource', DeckResourceFactory);
    
    function DeckResourceFactory($resource){
        function upload_deck(data){
            var resource = $resource('/games/yugioh/decks/', {}, {
                query:{method: 'post', isArray: false, cancellable: false}
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
        
        return {
            upload_deck: upload_deck,
        };
    }
    
})();
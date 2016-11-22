(function(){
    'use strict';
    angular.module('collector_app').factory('UsersResource', UsersResourceFactory);
    
    function UsersResourceFactory($resource){
        function is_username_available(user_id, username){
            var source = $resource('/data/users/available_username/:id/:username', {}, {
                query: {method: 'get', isArray: false, cancellable: false}
            });
            return source.query({id: user_id, username: username}).$promise.then(
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
        
        function is_email_available(user_id, email){
            var source = $resource('/data/users/available_email/:id/:email', {}, {
                query: {method: 'get', isArray: false, cancellable: false}
            });
            return source.query({id: user_id, email: email}).$promise.then(
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
            is_username_available: is_username_available,
        };
    }
    
})();
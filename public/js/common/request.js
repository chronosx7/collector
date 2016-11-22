(function(){
    'use strict';
    
    $.extend({
        json_response: function(response){
            console.log('In json_response');
            console.log(response);
            var res = {};
            try{
               res = JSON.parse(response); 
            }
            catch(error){
                res.status = 'error';
                if(response.toLowerCase() == 'unauthorized.'){
                    res.errors = ['Permission denied. Please log in again.'];
                }
                console.log('Response format error: ' + error);
            }
            return res;
        }
    });
    
})();
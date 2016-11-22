(function(){
    'use strict';
    angular.module('collector_app').factory('UserEditForm', UserEditFormFactory);
    
    function UserEditFormFactory(UsersResource){
        function get_form(){
        }

        function build_form(){
            return [
                // 
                {
                    key: 'name',
                    type: 'input',
                    templateOptions: {
                        type: 'text',
                        label: 'Username',
                    },
                },
                // 
                {
                    key: 'email',
                    type: 'input',
                    templateOptions: {
                        type: 'text',
                        label: 'Email',
                    },
                },
                // 
                {
                    key: 'password',
                    type: 'input',
                    templateOptions: {
                        type: 'text',
                        label: 'Password',
                    },
                },
                // 
                {
                    key: 'confirm_password',
                    type: 'input',
                    templateOptions: {
                        type: 'text',
                        label: 'Confirm Password',
                    },
                },
            ];
        }
        
        return {
            get_form: get_form,
        };
    }
    
})();










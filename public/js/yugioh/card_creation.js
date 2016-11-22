(function(){
    'use strict';

    angular.module('collector_app', ['formly',  'formlyBootstrap', 'ngDroplet', 'initialValue', 'ngResource', 'ui.bootstrap'])
    .controller('AppController', AppController);

    function AppController($scope, Form, CardResource, ModalWindow, $element){
        var vm = this;

        var card_id = $('#_card_id').val();
        vm.image_tag = '__svr_img_';
        vm.form = {};
        vm.request_token;
        vm.image_display = {};
        vm.data_errors = [];
        vm.formFields = {};
        vm.existing_images = [];
        vm.deleted_images = [];

        CardResource.get_form_options().then(function(response){
            if(response.status == 'ok'){
                vm.formFields = Form.getForm(response.data);
                vm.load_card_info();
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

        $scope.$on('$dropletReady', function(){
            vm.image_display.allowedExtensions(['png', 'jpg', 'jpeg']);
            vm.image_display.useArray(true);
            vm.image_display.useParser(ImageUploadResponse);
        });

        $scope.$on('$dropletFileAdded', function(){
            var images = vm.image_display.getFiles();
            var invalid_files = vm.image_display.getFiles(vm.image_display.FILE_TYPES.INVALID);
            var invalid_file_message = 'Invalid file.';

            // Removes last file if there are two images or said file is invalid
            if(invalid_files.length > 0 || images.length > 2){
                images.pop();
            }
        });

        vm.submit_data = function(){
            vm.form._token = vm.request_token;
            if(vm.deleted_images.length > 0){
                vm.form['deleted_images[]'] = vm.deleted_images;
            }
            // Stablish target url
            if(card_id != ''){
                vm.image_display.setRequestUrl('/games/yugioh/cards/update');
            }
            else{
                vm.image_display.setRequestUrl('/games/yugioh/cards');
            }
            vm.image_display.setPostData(vm.form);
            vm.image_display.setRequestHeaders({
                Accept: "application/json",
            });
            vm.image_display.uploadFiles();
        }

        vm.remove_image = function(index){
            var images = vm.image_display.getFiles();
            if(vm.existing_images.indexOf(images[index].file.file_name) != -1){
                vm.deleted_images.push(images[index].file.file_name);
            }
            images.splice(index, 1);
        };

        function ImageUploadResponse(response){
            var res = $.json_response(response);
            if(res.status != 'ok'){
                var error = 'Error creating a new card. ';
                var error_list = '';
                error += 'Please verify the provided data or try again later. Data errors will be listed below.';

                if(res.errors != undefined){
                    vm.data_errors = res.errors;
                }
                ModalWindow.open('error', {
                    title: 'Error - Card Creation Process',
                    message: error,
                });
            }
            else{
                var message = 'New card created successfully. ';
                message += 'It should be available soon.';
                ModalWindow.open('alert', {
                    title: 'Success - Card Creation Process',
                    message: message,
                });
                vm.form = {};
            }
        }

        vm.load_card_info = function(){
            if(card_id != ''){
                vm.get_card_info(card_id);
            }
        }

        vm.get_card_info = function(id, image){
            var res = CardResource.get_card_info(id, image);
            if(res.type == 'card_info'){
                vm.form = res;
            }
            else{
                var error_message = 'Unable to retrieve card information.';
                res.promise.then(function(response){
                    if(response.status == 'ok'){
                        vm.form = response;
                        get_card_images(response.images);
                        if(response.class == '1'){
                            set_card_families(response.families);
                        }
                        else{
                            vm.form['spell_type'] = response.spell_type;
                        }
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

        function get_card_images(images){
            for(var i = 0; i < images.length; i++){
                var image = new Image();
                image.addEventListener('load', load_image, false);
                image.src = '/img/yugioh/' + images[i].active_file;
                image.file_name = images[i].active_file;
            }
        }

        function load_image(){
            var canvas = document.createElement("canvas");
            var file_name = vm.image_tag + this.file_name;
            canvas.width = this.width;
            canvas.height = this.height;

            var ctx = canvas.getContext("2d");
            ctx.drawImage(this, 0, 0);

            var data = canvas.toBlob(function(blob){
                blob.file_name = file_name;
                vm.image_display.addFile(blob);
                vm.existing_images.push(file_name);
                $element.find("droplet").triggerHandler("$dropletFileAdded");
                $scope.$apply();
            },
            "image/jpg");
        }

        function set_card_families(families){
            for(var i = 0; i < families.length; i++){
                $('.formly-field-checkbox[id*="checkbox_' + families[i].toLowerCase() + '"]')
                .prop('checked', true);
                vm.form[families[i].toLowerCase()] = true;
            }
        }
    }


})();










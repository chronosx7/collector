(function(){
    'use strict';
    angular.module('collector_app').factory('Form', FormFactory);
    
    function FormFactory(){
        var hide_non_monster = 'model.class == "1"';
        var hide_non_spell = 'model.class == "2" || model.class == "3"';
        var hide_pendulum = '!model.pendulum';
        
        var level_placeholder = '1 to 12';
        var atk_placeholder = 'Number or ?';
        
        function capitalizeWord(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        function getForm(options, action){
            action = action === undefined? 'CREATE': action;
            return build_form(options, action);
        }

        function validate_description($viewValue, $modelValue, scope){
            var value = $viewValue || $modelValue;
            value = value.toString().trim();
            if(value){
                value = value.replace(/\s{2,}/, ' ');
                value = value === '[object Object]'? '': value;
                $modelValue.value(value);
            }
        }
        
        function validate_level($viewValue, $modelValue, scope){
            var value = $viewValue || $modelValue;
            value = value.toString();
            if(value){
                var expr = new RegExp(/[^0-9]/, 'g');
                value = value.replace(expr, '');
                value = parseInt(value);
                if(isNaN(value)){
                    value = '';
                }
                else if(value < 1){
                    value = 1
                }
                else if(value > 12){
                    value = 12;
                }
                $modelValue.value(value);
            }
        }
        
        function validate_atk($viewValue, $modelValue, scope){
            var value = $viewValue || $modelValue;
            value = value.toString();
            if(value){
                var expr = new RegExp(/[^0-9?]/, 'g');
                value = value.replace(expr, '?');
                if(value.indexOf('?') != -1){
                    value = '';
                }
                else{
                    expr = new RegExp(/^0+(?=\d)/, 'g');
                    value = value.replace(expr, '');
                    value = value > 9999? 9999: value;
                }
                $modelValue.value(value);
            }
        }
        
        function build_form(options, action){
            var no_value = {name: '---', value: ''};
            var monster_family_fields = [];
            var monster_type_options = [no_value];
            var monster_attributes = [no_value];
            var class_options = [];
            var spell_options = [no_value];
            var trap_options = [no_value];
            var require_fields = true;
            
            if(action.toString().toUpperCase() == 'SEARCH'){
                require_fields = false;
            }
            for(var i = 0; i < options.monster_families.length; i++){
                monster_family_fields.push({
                    className: 'col-xs-3',
                    key: options.monster_families[i].family.toLowerCase(),
                    type: 'checkbox',
                    templateOptions:{
                        label: options.monster_families[i].family,
                    },
                    hideExpression: hide_non_spell,
                });
            }
            for(var i = 0; i < options.monster_types.length; i++){
                monster_type_options.push({name: options.monster_types[i].type, 
                value: options.monster_types[i].id});
            }
            for(var i = 0; i < options.card_classes.length; i++){
                class_options.push({name: options.card_classes[i]['class'],
                    value: options.card_classes[i].id,
                });
            }
            for(var i = 0; i < options.spell_types.length; i++){
                spell_options.push({name: options.spell_types[i].type,
                    value: options.spell_types[i].id,
                });
            }
            for(var i = 0; i < options.trap_types.length; i++){
                trap_options.push({name: options.trap_types[i].type,
                    value: options.trap_types[i].id,
                });
            }
            for(var i = 0; i < options.monster_attributes.length; i++){
                monster_attributes.push({name: options.monster_attributes[i].attribute,
                    value: options.monster_attributes[i].id,
                });
            }
            
            return [
                // Official / Unofficial
                {
                    key: 'official',
                    type: 'checkbox',
                    defaultValue: true,
                    templateOptions:{type: 'text', label: 'Official Card',},
                },
                // Card class
                {
                    key: 'class',
                    type: 'select',
                    defaultValue: 1,
                    templateOptions:{
                        type: 'text',
                        label: 'Card Class',
                        options: class_options,
                        required: require_fields,
                    },
                },
                {
                    hideExpression: 'model.class == "?"',
                    fieldGroup:[
                        // card_name
                        {
                            key: 'card_name',
                            type: 'input',
                            templateOptions: {
                                type: 'text',
                                label: 'Card Name',
                                placeholder: 'card Name',
                                required: require_fields,
                                onBlur: validate_description,
                            },
                        },
                        // Spell type
                        {
                            key: 'spell_type',
                            type: 'select',
                            templateOptions:{
                                type: 'text',
                                label: 'Spell / Trap Type',
                                options: spell_options,
                                required: require_fields,
                            },
                            hideExpression: 'model.class != "2"',
                        },
                        // Trap type
                        {
                            key: 'trap_type',
                            type: 'select',
                            templateOptions:{
                                type: 'text',
                                label: 'Spell / Trap Type',
                                options: trap_options,
                                required: require_fields,
                            },
                            hideExpression: 'model.class != "3"',
                        },
                        // Monster type
                        {
                            key: 'monster_type',
                            type: 'select',
                            templateOptions:{
                                type: 'text',
                                label: 'Monster Type',
                                options: monster_type_options,
                                required: require_fields,
                            },
                            hideExpression: hide_non_spell,
                        },
                        // Level and Attribute field group
                        {
                            className: 'row',
                            fieldGroup:[
                                // Level / Rank
                                {
                                    className: 'col-xs-6',
                                    key: 'level',
                                    type: 'input',
                                    templateOptions:{
                                        type: 'text',
                                        label: 'Monster Level',
                                        placeholder: level_placeholder,
                                        required: require_fields,
                                        onBlur: validate_level,
                                    },
                                    hideExpression: hide_non_spell,
                                },
                                // Monster Attribute
                                {
                                    className: 'col-xs-6',
                                    key: 'attribute',
                                    type: 'select',
                                    templateOptions:{
                                        type: 'text',
                                        label: 'Attribute',
                                        options: monster_attributes,
                                        required: require_fields,
                                    },
                                    hideExpression: hide_non_spell,
                                },
                            ],
                        },
                        // Monster families field group
                        {
                            className: 'row',
                            fieldGroup: monster_family_fields,
                        },
                        // Left and Right scale field group
                        {
                            className: 'row',
                            fieldGroup:[
                                // Left Scale
                                {
                                    className: 'col-xs-6',
                                    key: 'left_scale',
                                    type: 'input',
                                    templateOptions:{
                                        placeholder: level_placeholder,
                                        type: 'text',
                                        label: 'Left Scale',
                                        required: require_fields,
                                        onBlur: validate_level,
                                    },
                                    hideExpression: hide_pendulum,
                                },
                                // Right Scale
                                {
                                    className: 'col-xs-6',
                                    key: 'right_scale',
                                    type: 'input',
                                    templateOptions:{
                                        placeholder: level_placeholder,
                                        type: 'text',
                                        label: 'Right Scale',
                                        required: require_fields,
                                        onBlur: validate_level,
                                    },
                                    hideExpression: hide_pendulum,
                                },
                            ],
                        },
                        // Pendulum Effect
                        {
                            key: 'pendulum_effect',
                            type: 'textarea',
                            templateOptions:{
                                type: 'text',
                                label: 'Pendulum Effect',
                                required: require_fields,
                                onBlur: validate_description,
                            },
                            hideExpression: hide_pendulum,
                        },
                        // Card description / Effect
                        {
                            key: 'description',
                            type: 'textarea',
                            templateOptions:{
                                type: 'text',
                                label: 'Card Description',
                                required: require_fields,
                                onBlur: validate_description,
                            },
                        },
                        // Atk and Def field group
                        {
                            className: 'row',
                            fieldGroup:[
                                // Attack
                                {
                                    className: 'col-xs-6',
                                    key: 'attack',
                                    type: 'input',
                                    templateOptions:{
                                        type: 'text',
                                        label: 'Atk',
                                        required: require_fields,
                                        onBlur: validate_atk,
                                        placeholder: atk_placeholder,
                                    },
                                    hideExpression: hide_non_spell,
                                },
                                // Defense
                                {
                                    className: 'col-xs-6',
                                    key: 'defense',
                                    type: 'input',
                                    templateOptions:{
                                        placeholder: atk_placeholder,
                                        type: 'text',
                                        label: 'Def',
                                        required: require_fields,
                                        onBlur: validate_atk,
                                    },
                                    hideExpression: hide_non_spell,
                                },
                            ],
                        },
                    ],
                },
            ];
        }
        
        function error_message(){
            return 'Could not retrieve form options.';
        }
        
        return {
            getForm: getForm,
            error_message: error_message,
        };
    }
    
})();










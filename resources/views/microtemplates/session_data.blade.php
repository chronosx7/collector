<input type="hidden" id='_token' name="_token" ng-model='vm.request_token' 
initial-value="<?php echo csrf_token();?>" value="<?php echo csrf_token();?>"/>
@if(!Auth::guest())
    <input type="hidden" id='_user_id' name="_user_id" 
    value="<?php echo Auth::user()->id;?>" 
    ng-model='vm.user_id' 
    initial-value="<?php echo Auth::user()->id;?>"/>
@endif

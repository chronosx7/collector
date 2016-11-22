<form class="yugioh_form">
    <div class="row full-height panel-bottom-container">
        <div class='col-xs-12 full-height padding-xs auto-scroll'>
            <input type="hidden" name="_token" ng-model='vm.request_token' initial-value="<?php echo csrf_token(); ?>"/>
            <formly-form model='vm.form' fields='vm.formFields' form='vm.cardForm'>
            </formly-form>
        </div>
        <div class='panel panel-default panel-bottom'>
            <button type='submit' class='btn btn-primary' ng-disabled='vm.cardForm.$invalid' ng-click='vm.submit_data()' style='width: 100%'>
                Search
            </button>
        </div>
    </div>
</form>

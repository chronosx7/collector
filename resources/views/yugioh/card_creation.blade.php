@extends('layouts.app1')

@section('content')
<div ng-app='collector_app' ng-controller='AppController as vm'>
    @include('microtemplates.session_data')
    <input type="hidden" id="_card_id" value="{{$card_id or ''}}"/>
    <form class='yugioh_form'>
        <div class='row height-90'>
            <!-- Search form-->
            <div class='col-xs-12 col-md-3 full-height padding-xs bordered border-grey' style='overflow: auto'>
                <formly-form model='vm.form' fields='vm.formFields' form='vm.cardForm'>
                </formly-form>
            </div>
            <!--/ Search form-->
            
            <!-- Image display-->
            <div class='col-xs-12 col-md-9 full-height'>
                <div class='row height-5 padding-xs'>
                    Browse or drag and drop up to 2 images (.PNG, .JPG or .JPEG) below.
                </div>
                <div class='row height-5'>
                    <div class='col-xs-12 padding-xs'>
                        <droplet-upload-single ng-model="vm.image_display"></droplet-upload-single>
                    </div>
                </div>
                <div class='row height-60'>
                    <div class='col-xs-12 padding-xs'>
                        <droplet ng-model="vm.image_display" id='image_display'>
                            <div class='images image-preview '>
                                <div class='col-xs-6 full-height panel-bottom-container' ng-repeat='model in vm.image_display.getFiles(vm.image_display.FILE_TYPES.VALID)'>
                                    <droplet-preview ng-model='model' ng-show='model.isImage()'></droplet-preview>
                                    <div class="panel panel-default panel-bottom">
                                        <button class="btn btn-primary" style='width: 100%;' ng-click="vm.remove_image($index)">
                                            Remove image
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </droplet>
                    </div>
                </div>
                <div class='row height-30'>
                    <div class='col-xs-12 padding-xs'>
                        <ul ng-repeat='error in vm.data_errors'>
                            <li>@{{error}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--/ Image display-->
            <!-- Blob creation canvas-->
            <div id="canvas-container" ng-hide="true"></div>
            <!--/ Blob creation canvas-->
            
            @include('microtemplates.modal')
        </div>
        <div class='row height-5' >
            <div class='bordered border-grey padding-xs' style='width: 100%;'>
                <button type='submit' class='btn btn-primary' 
                ng-disabled='vm.cardForm.$invalid || vm.image_display.getFiles(vm.image_display.FILE_TYPES.VALID).length == 0' 
                ng-click='vm.submit_data()' style='width: 100%'>
                    Submit Card
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('custom_js')
    @include('includes_js.angular')
    
    <script src='/js/common/request.js'></script>
    <script src='/js/{{$game}}/card_creation.js'></script>
    <script src='/js/{{$game}}/services/CardResource.js'></script>
    <script src='/js/{{$game}}/services/Form.js'></script>
    
    <script src='/js/common/modal.js'></script>
@endsection
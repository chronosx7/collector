@extends('layouts.app1')

@section('content')
<div ng-app='collector_app' ng-controller='AppController as vm'>
    <div class="row full-height">
        <!-- Search form-->
        <div class='col-xs-12 col-sm-2 full-height panel panel-default'>
        </div>
        <!--/ Search form-->
        
        <!-- Search results-->
        <div class='col-xs-12 col-sm-10 full-height panel panel-default'>
            <div class="row full-height auto-scroll" style="padding: 2px">
                <div class='col-xs-12 col-sm-6 panel panel-default'>
                    <div class="row">
                        <div class="col-xs-12">
                            My Cards
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            Create New Card
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Search results-->
    </div>
    @include('microtemplates.modal')
</div>
@endsection

@section('custom_js')
    @include('includes_js.angular')
    
    <script src='/js/common/modal.js'></script>
@endsection
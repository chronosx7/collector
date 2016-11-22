@extends('layouts.app1')

@section('content')
<div ng-app='collector_app' ng-controller='AppController as vm'>
    @include('microtemplates.session_data')
    <div class="row height-95">
        <!-- Search form-->
        <div class='col-xs-12 col-sm-3 col-sm-3 full-height panel panel-default'>
            @include('yugioh.components.comp_card_search_form')
        </div>
        <!--/ Search form-->
        
        <!-- Search results-->
        <div class='col-xs-12 col-sm-4 col-sm-4 full-height panel panel-default'>
            <div class="row full-height auto-scroll" style="padding: 2px 2px 45px 2px">
                @include('yugioh.components.comp_search_results')
            </div>
        </div>
        <!--/ Search results-->
        
        <!-- Card display -->
        <div class='col-xs-12 col-sm-5 col-sm-5 panel panel-default full-height'>
            @include('yugioh.components.comp_card_details')
        </div>
        <!--/ Card display -->
    </div>
    @include('microtemplates.modal')
</div>
@endsection

@section('custom_js')
    @include('includes_js.angular')
    <script src='/js/{{$game}}/card_search.js'></script>
    <script src='/js/{{$game}}/services/CardResource.js'></script>
    <script src='/js/{{$game}}/services/CardInteraction.js'></script>
    <script src='/js/{{$game}}/services/Form.js'></script>
    
    <script src='/js/common/modal.js'></script>
@endsection
@extends('layouts.app1')

@section('content')
<div ng-app='collector_app' ng-controller='AppController as vm'>
    @include('microtemplates.session_data')
    <input type="hidden" id="_target_user" name="_target_user" value="{{$user}}"/>
    <input type="hidden" id="_target_game" name="_target_game" value="{{$game}}"/>
    <div class="row top-bar-row">
        <div class="col-xs-12 full-height panel panel-default" style='margin: 2px 0px 3px 0px; padding: 3px;'>
        </div>
    </div>
    <div class="row height-95">
        <!-- Search results-->
        <div class='col-xs-12 col-sm-7 full-height panel panel-default'>
            <div class="row full-height auto-scroll" style="padding: 2px 2px 45px 2px">
                @include($game . '.components.comp_search_results')
            </div>
        </div>
        <!--/ Search results-->
        
        <!-- Card details-->
        <div class='col-xs-12 col-sm-5 full-height panel panel-default'>
            <div class="row full-height auto-scroll" style="padding: 2px 2px 45px 2px">
                @include($game . '.components.comp_card_details')
            </div>
        </div>
        <!--/ Card details-->
    </div>
    @include('microtemplates.modal')
</div>
@endsection

@section('custom_js')
    @include('includes_js.angular')
    <script src='/bower_components/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js'></script>
    <script src='/js/users/user_cards.js'></script>
    <script src='/js/{{$game}}/services/CardResource.js'></script>
    <script src='/js/{{$game}}/services/CardInteraction.js'></script>
    
    <script src='/js/common/modal.js'></script>
@endsection
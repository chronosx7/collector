@extends('layouts.app1')

@section('content')
<div ng-app='collector_app' ng-controller='AppController as vm'>
    @include('microtemplates.session_data')
    <div class="row top-bar-row">
        <div class="col-xs-12 full-height panel panel-default" style='margin: 2px 0px 3px 0px; padding: 3px;'>
            <form class="form-inline">
                <div class="form-group">
                    <label for="deck_name">Deck Name</label>
                    <input type="text" class="form-control input-sm" id="deck_name" placeholder="Awesome Deck 1" ng-model='vm.deck_name'>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" ng-click='vm.upload_deck()' ng-disabled="!vm.deck_is_valid()">Save Deck</button>

                <div class="btn-group pull-right" role="group">
                    <button type="submit" class="btn btn-primary btn-sm" ng-disabled='!vm.has_selected_card()' ng-click='vm.btn_move_to_main_deck()'>Move to Main Deck</button>
                    <button type="submit" class="btn btn-primary btn-sm" ng-disabled='!vm.has_selected_card()' ng-click='vm.btn_move_to_side_deck()'>Move to Side Deck</button>
                    <button type="submit" class="btn btn-primary btn-sm" ng-disabled='!vm.has_selected_card()' ng-click='vm.btn_remove_from_deck()'>Remove From Deck</button>
                </div>
            </form>
        </div>
    </div>
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
        
        <!-- Deck Construction -->
        <div class='col-xs-12 col-sm-5 col-sm-5 panel panel-default full-height'>
            @include('yugioh.components.comp_deck_construction')
        </div>
        <!--/ Deck Construction -->
    </div>
    @include('microtemplates.modal')
</div>
@endsection

@section('custom_js')
    @include('includes_js.angular')
    <script src='/bower_components/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js'></script>
    <script src='/js/{{$game}}/deck_creation.js'></script>
    <script src='/js/{{$game}}/services/CardResource.js'></script>
    <script src='/js/{{$game}}/services/CardInteraction.js'></script>
    <script src='/js/{{$game}}/services/DeckResource.js'></script>
    <script src='/js/{{$game}}/services/Form.js'></script>
    
    <script src='/js/common/modal.js'></script>
@endsection
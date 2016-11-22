<div class="col-xs-12 full-height padding-xs auto-scroll" style="padding: 2px 2px 45px 2px">
    @if(Auth::guest())
        <a class="btn btn-primary btn-sm inner-top-button top-right" 
            href="{{url('/games/yugioh/cards/create')}}">Log in or Register to Create New Cards</a>
    @else
        <a class="btn btn-primary btn-sm inner-top-button top-right" 
            href="{{url('/games/yugioh/cards/create')}}">Create New Card</a>
    @endif
    <ul class='itemlist full-height padding-xs margin-xs no-style-list margin-top'
        dnd-list='vm.dumped_cards'
        dnd-allowed-types='["MainCard", "ExtraCard"]'
        dnd-droped='vm.clear_dumped_cards()'
        dnd-external-sources='false'>
        <li ng-repeat='card in vm.visible_cards' 
        class='col-xs-12 col-sm-3 padding-xs margin-sm card-thumbnail' 
        ng-class="{'selected': vm.is_selected_card($index, 'SEARCH')}">
            <img src='/img/yugioh/@{{card.active_file}}' class='thumbnail img-thumbnail' 
            ng-class="{'selected': vm.is_selected_card($index, 'SEARCH')}"
            dnd-draggable="card"
            dnd-type="card.card_type"
            dnd-effect-allowed="copy"
            ng-click='vm.select_card(card, "SEARCH", $index); 
            vm.get_card_info(card.id, card.active_file);vm.show_info_tab();'
            ng-dblclick='vm.show_info_tab()'/>
        </li>
    </ul>
</div>
<div class='panel panel-default panel-bottom' style='text-align: center;'>
    <uib-pagination boundary-links="true" total-items="vm.total_items" 
    ng-model="vm.current_page" ng-change='vm.page_changed()' class="pagination-sm margin-xs" 
    previous-text="&lsaquo;" items-per-page="vm.per_page" next-text="&rsaquo;" 
    first-text="&laquo;" last-text="&raquo;" style='margin: 2px 0'></uib-pagination>
</div>

<!-- Nav tabs -->
<div class="row height-5">
    <ul id='deck_construction_tabs' class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active" ng-click='vm.select_deck("Main")'>
            <a href="#main_deck_construction" aria-controls="main_deck_construction" role="tab" data-toggle="tab">
                Main (@{{vm.main_deck_cards.length}})
            </a>
        </li>
        <li role="presentation"  ng-click='vm.select_deck("Extra")'>
            <a href="#extra_deck_construction" aria-controls="extra_deck_construction" role="tab" data-toggle="tab">
                Extra (@{{vm.extra_deck_cards.length}})
            </a>
        </li>
        <li role="presentation"  ng-click='vm.select_deck("Side")'>
            <a href="#side_deck_construction" aria-controls="side_deck_construction" role="tab" data-toggle="tab">
                Side (@{{vm.side_deck_cards.length}})
            </a>
        </li>
        <li role="presentation">
            <a href="#card_details" aria-controls="card_details" role="tab" data-toggle="tab">Card Details</a>
        </li>
    </ul>
</div>
<div class="row height-95 tab-content">
    <!-- Main Deck -->
    <div id="main_deck_construction" class="col-xs-12 tab-pane active full-height auto-scroll" role="tabpanel">
        <button type="submit" class="btn btn-primary btn-sm inner-top-button top-right" 
            ng-click='vm.empty_deck("MAIN")'>Empty Main Deck</button>
        <ul dnd-list="vm.main_deck_cards"
            dnd-allowed-types="['MainCard']"
            dnd-external-sources='false' 
            dnd-drop='vm.drop_in_deck(event, index, item, external, type, "MAIN")'
            class="itemlist full-height padding-xs margin-xs no-style-list margin-top">
            <li ng-repeat="card in vm.main_deck_cards" class="col-xs-12 col-sm-3 padding-xs margin-sm card-thumbnail" ng-class="{'selected': vm.is_selected_card($index, 'MAIN')}">
                <img class="thumbnail img-thumbnail" 
                ng-class="{'selected': vm.is_selected_card($index, 'MAIN')}"
                dnd-draggable="card"
                dnd-type="card.card_type"
                dnd-effect-allowed='move'
                dnd-moved='vm.remove_from_main_deck($index)' 
                ng-click='vm.select_card(card, "MAIN", $index); vm.get_card_info(card.id, card.active_file); vm.show_info_tab();'
                ng-dblclick='vm.show_info_tab()'
                src='/img/yugioh/@{{card.active_file}}'
                >
            </li>
        </ul>
    </div>
    <!-- Extra Deck -->
    <div id="extra_deck_construction" class="col-xs-12 tab-pane full-height auto-scroll" role="tabpanel">
        <button type="submit" class="btn btn-primary btn-sm inner-top-button top-right" 
            ng-click='vm.empty_deck("EXTRA")'>Empty Extra Deck</button>
        <ul dnd-list="vm.extra_deck_cards"
            dnd-allowed-types="['ExtraCard']"
            dnd-external-sources='false' 
            dnd-drop='vm.drop_in_deck(event, index, item, external, type, "EXTRA")'
            class="itemlist full-height media-grid padding-xs 
            margin-xs no-style-list margin-top">
            <li ng-repeat="card in vm.extra_deck_cards" class="col-xs-12 col-sm-3 padding-xs margin-sm card-thumbnail" ng-class="{'selected': vm.is_selected_card($index, 'EXTRA')}">
                <img class="thumbnail img-thumbnail" 
                ng-class="{'selected': vm.is_selected_card($index, 'EXTRA')}"
                dnd-draggable="card"
                dnd-type="card.card_type"
                dnd-effect-allowed='move'
                dnd-moved='vm.remove_from_extra_deck($index)' 
                ng-click='vm.select_card(card, "EXTRA", $index); vm.get_card_info(card.id, card.active_file); vm.show_info_tab();'
                ng-dblclick='vm.show_info_tab()'
                src='/img/yugioh/@{{card.active_file}}'
                >
            </li>
        </ul>
    </div>
    <!-- Side Deck -->
    <div id="side_deck_construction" class="col-xs-12 tab-pane full-height auto-scroll" role="tabpanel">
        <button type="submit" class="btn btn-primary btn-sm inner-top-button top-right" 
            ng-click='vm.empty_deck("SIDE")'>Empty Side Deck</button>
        <ul dnd-list="vm.side_deck_cards"
            dnd-allowed-types="['MainCard', 'ExtraCard']"
            dnd-external-sources='false' 
            dnd-drop='vm.drop_in_deck(event, index, item, external, type, "SIDE")'
            class="itemlist full-height media-grid padding-xs 
            margin-xs no-style-list margin-top">
            <li ng-repeat="card in vm.side_deck_cards" class="col-xs-12 col-sm-3 padding-xs margin-sm card-thumbnail" ng-class="{'selected': vm.is_selected_card($index, 'SIDE')}">
                <img class="thumbnail img-thumbnail" 
                ng-class="{'selected': vm.is_selected_card($index, 'SIDE')}"
                dnd-draggable="card"
                dnd-type="card.card_type"
                dnd-effect-allowed='move'
                dnd-moved='vm.remove_from_side_deck($index)' 
                ng-click='vm.select_card(card, "SIDE", $index); vm.get_card_info(card.id, card.active_file); vm.show_info_tab();'
                ng-dblclick='vm.show_info_tab()'
                src='/img/yugioh/@{{card.active_file}}'
                >
            </li>
        </ul>
    </div>
    <!-- Card Details -->
    <div id="card_details" class="col-xs-12 tab-pane full-height" role="tabpanel">
        @include('yugioh.components.comp_card_details')
    </div>
</div>

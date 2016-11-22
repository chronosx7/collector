<!-- Card image -->
<div class="row height-45">
    <div class="col-xs-12 full-height">
        @if(!Auth::guest())
            <a class="btn btn-primary btn-sm inner-top-button top-right" 
                href="/games/yugioh/cards/@{{vm.card_info.id}}/edit" ng-hide='!vm.card_is_editable(vm.card_info.user_id, vm.user_id)'>Edit Card</a>
        @endif
        <img class='img-responsive' style='height: 95%; width: auto; margin: 2% auto 2% auto;' src='/img/yugioh/@{{vm.card_info.active_file}}'/>
    </div>
</div>
<!-- Monster Cards -->
<div class="row height-50 panel panel-default panel-bottom auto-scroll" ng-hide='vm.card_info.class != "1"'>
    <div class="row height-10 border-bottom border-grey margin-xs">
        <div class="col-xs-12 full-height" style="text-align: center;">
            @{{vm.card_info.card_name}}
        </div>
    </div>
    <div class="row height-10 border-bottom border-grey margin-xs">
        <div class="col-xs-6 full-height border-right border-grey">Level / Rank: @{{vm.card_info.level}}</div>
        <div class="col-xs-6 full-height border-grey">Attribute: @{{vm.card_info.attribute_name}}</div>
    </div>
    <div class="row height-10 border-bottom border-grey margin-xs">
        <div class="col-xs-12">
            <span >@{{vm.card_info.monster_type_name}} / 
                <span ng-repeat='family in vm.card_info.families'>@{{family}} / </span>
            </span>
        </div>
    </div>
    <div class="row height-30 border-bottom border-grey margin-xs" ng-hide='vm.card_info.pendulum_effect == undefined'>
        <div class="col-xs-1 full-height ">
            <span >@{{vm.card_info.left_scale}}</span>
        </div>
        <div class="col-xs-10 full-height border-right border-left border-grey auto-scroll">
            <span >@{{vm.card_info.pendulum_effect}}</span>
        </div>
        <div class="col-xs-1 full-height">
            <span >@{{vm.card_info.right_scale}}</span>
        </div>
    </div>
    <div class="row height-30 border-bottom border-grey auto-scroll margin-xs">
        <div class="col-xs-12">
            <span >@{{vm.card_info.description}}</span>
        </div>
    </div>
    <div class="row height-10 border-bottom border-grey margin-xs">
        <div class="col-xs-6 border-right border-grey full-height">
            Atk: <span>@{{vm.card_info.attack}}</span>
        </div>
        <div class="col-xs-6 border-grey full-height">
            Def: <span>@{{vm.card_info.defense}}</span>
        </div>
    </div>
</div>
<!-- Spell / Trap Cards -->
<div class="row height-50  panel panel-default panel-bottom auto-scroll" ng-hide='vm.card_info.class == "1"'>
    <div class="row height-10 bordered border-grey">
        <div class="col-xs-12 full-height" style="text-align: center;">
            @{{vm.card_info.card_name}}
        </div>
    </div>
    <div class="row height-10 bordered border-grey">
        <div class="col-xs-12 full-height">
            Spell / Trap Type: @{{vm.card_info.spell_type_name}}
        </div>
    </div>
    <div class="row height-80 bordered border-grey auto-scroll">
        <div class="col-xs-12 full-height">
            <span >@{{vm.card_info.description}}</span>
        </div>
    </div>
</div>
<div class="row panel panel-default panel-bottom">
    <div class="col-xs-12 full-height">
        <span >Added by @{{vm.card_info.user_name}}</span>
    </div>
</div>

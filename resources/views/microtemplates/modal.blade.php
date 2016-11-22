<!-- Alert Modal template -->
<script type="text/ng-template" id="ModalContent.html">
    <div class="modal-header" style='padding: 5px; 
    background-color: @{{elems.bg_color}}'>
        <h3 class="modal-title" style='color: white'>@{{elems.title}}</h3>
    </div>
    <div class="modal-body">
        @{{ elems.message }}
    </div>
    <div class="modal-footer" style='padding: 5px'>
        <button class="btn btn-@{{elems.btn_class}}" type="button" ng-click="ok()">OK</button>
    </div>
</script>
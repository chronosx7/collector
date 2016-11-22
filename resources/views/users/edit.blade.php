@extends('layouts.app1')

@section('content')
<div class="container" ng-app='collector_app' ng-controller='AppController as vm'>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Edit User Profile</div>
                <div class="panel-body">
                    <p>
                        Please update the folowing information to complete your
                        profile.
                    </p>
                    <p>
                        Password fields are optional <strong><em>if</em></strong> you use Facebook or Twitter to log in.
                    </p>
                    <formly-form model='vm.form' fields='vm.form_fields' form='vm.edit_form'>
                    </formly-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('custom_js')
    @include('includes_js.angular')
    <script src='/js/users/edit.js'></script>
    
    <script src='/js/common/modal.js'></script>
@endsection
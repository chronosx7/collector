<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel='stylesheet' href='/bower_components/bootstrap/dist/css/bootstrap.css'/>
        <!--<link rel='stylesheet' href='/themes/bs-sb-admin/css/sb-admin.css'/>-->
        <link href="/themes/bs-sb-admin/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <link rel='stylesheet' href='/css/styles.css'/>
        @yield('custom_css')
        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body>
        <div class='container-fluid full-height' >
            <div class='row'>
                <!-- Navigation -->
                <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                    <div class='container-fluid'>
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class='icon-bar'></span>
                                <span class='icon-bar'></span>
                                <span class='icon-bar'></span>
                            </button>
                            <a class="navbar-brand" href="/">Collector</a>
                        </div>

                        <div class='collapse navbar-collapse' id='bs-navbar-collapse'>
                            <ul class='nav navbar-nav navbar-right'>
                                @if(Auth::guest())
                                    <li><a href="{{ url('/login') }}">Login</a></li>
                                    <li><a href="{{ url('/register') }}">Register</a></li>
                                @else
                                    <li class='visible-xs' ><a href='#'>{{ Auth::user()->name }}</a></li>
                                    <li class='visible-xs' role="separator" class="divider"><hr></li>
                                    
                                    <li class='visible-xs'><a href="#">Profile</a></li>
                                    
                                    <li class='visible-xs' role="separator" class="divider"><hr></li>
                                    <li class='visible-xs'><a href="{{ url('/logout') }}"> <i class='glyphicon glyphicon-log-out'></i> Logout</a></li>
                                    <li class="dropdown hidden-xs">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#"> Profile</a></li>
                                            <li role="separator" class="divider"><hr></li>
                                            <li><a href="{{ url('/logout') }}"> <i class='glyphicon glyphicon-log-out'></i> Logout</a></li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                            <ul class="nav navbar-nav navbar-right">
                                <li class="dropdown">
                                    <a href='#' class="dropdown-toggle" data-toggle="dropdown" role="button"
                                    aria-haspopup="false" aria-expanded="false">Yu-Gi-Oh <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href='{{url("/games/yugioh/cards")}}'>Search Cards</a></li>
                                        <li><a href='{{url("/games/yugioh/decks/create")}}'>Search Decks</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
            <div class='row app-container'>
                <div class='col-xs-12 col-md-10 full-height'>
                    @yield('content')
                </div>
                <div class='col-xs-12 col-md-2 hidden-xs visible-md visible-lg'>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p id='target'>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                    <p>some ads here</p>
                </div>
            </div>
        </div>
        <script src='/bower_components/jquery/dist/jquery.js'></script>
        <script src='/bower_components/bootstrap/dist/js/bootstrap.js'></script>
    
        @yield('custom_js')
    </body>
</html>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src="{{ url('js/app.js') }}" defer></script>
    </head>
    <body>
        <main>
            <!-- Updated Header Section -->
            <header>
                <div class="logo">
                    <h1><a href="{{ url('/posts') }}">NewsNet</a></h1>
                </div>
                <nav>
                    <div class='about'>
                        <a href="/about">About</a>
                    </div>
                    <div class='contacts'>
                        <a href="/contacts">Contacts</a>
                    </div>
                    @if (Auth::check())
                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <a class="dropbtn">{{ Auth::user()->username }}</a>
                            <div class="dropdown-content">
                                <a href="/users/{{ Auth::user()->id }}">Profile</a>
                                @if (Auth::user()->system_managers)
                                    <a href="/sys/reports">Reports</a>
                                @endif
                                <a href="{{ url('/logout') }}">Logout</a>
                            </div>
                        </div>
                    @else
                        <div class='login'>
                            <a href="/login">Login</a>
                        </div>
                    @endif
                </nav>
            </header>

            <!-- Search Bar Section -->
            <section id="search-bar">
                <div class="navigation-search">
                    @if (isset($breadcrumbs))
                        <div class="breadcrumb">
                            @foreach ($breadcrumbs as $breadcrumb)
                                @if ($breadcrumb['url'])
                                    <span class="breadcrumb-item">
                                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                    </span>
                                @else
                                    <span class="breadcrumb-item active">{{ $breadcrumb['title'] }}</span>
                                @endif
                                @if (!$loop->last)
                                    <span class="breadcrumb-separator">▶</span>
                                @endif
                            @endforeach
                        </div>
                    @endif    

                    <form action="{{ route('search.posts') }}" method="GET" >
                        <input type="text" name="query" placeholder="Search posts..." required>
                        <button type="submit">Search</button>
                    </form>
                </div>
            </section>
            <!-- Content Section -->
            <section id="content">
                @yield('content')
            </section>
        </main>
    </body>
</html>
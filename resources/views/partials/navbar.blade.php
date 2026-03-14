<style>
    .btn-primary {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-primary:hover {
        color: #fff;
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .btn-primary:focus {
        color: #fff;
        background-color: #0b5ed7;
        border-color: #0a58ca;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.5);
    }

    .btn-primary:active {
        color: #fff;
        background-color: #0a58ca;
        border-color: #0a53be;
    }

    .btn-primary:disabled {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
        opacity: 0.65;
    }

    .btn-blue-outline {
        border: 1px solid #0d6efd;
        color: #0d6efd;
        background-color: transparent;
    }

    .btn-blue-outline:hover {
        background-color: #0d6efd;
        color: #ffffff;
        border-color: #0d6efd;
    }
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            {{ config('app.name', 'Starz') }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse mt-3 mt-lg-0" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link btn-primary" href="{{ route('explore.index') }}">
                        Explore
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item">
                        <a class="btn btn-primary nav-link" href="{{ route('dashboard') }}">
                            Fan Dashboard
                        </a>
                    </li>

                    @if(auth()->user()->isApprovedCreator())
                        <li class="nav-item">
                            <a class="nav-link btn-primary" href="{{ route('creator.dashboard') }}">
                                Creator Dashboard
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-light btn-sm ms-lg-2 mt-2 mt-lg-0 btn-blue-outline" type="submit">
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm me-2 mb-2 mb-lg-0" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Join</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<header class="header">
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i id="menu-icon" class="fas fa-bars"></i>
        </button>
        <div class="logo-container">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-img">

        </div>
    </div>
    <div class="header-right">
        <div class="user-info">
            @auth
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="user-name">{{ auth()->user()->name }}</div>
            @endauth
            @guest
                <div class="avatar">GU</div>
                <div class="user-name">Guest</div>
            @endguest
        </div>

        <div class="nav-links">
            <span class="separator">|</span>

            @auth
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="nav-button nav-button--logout" title="Logout" aria-label="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            @endauth
        </div>
    </div>
</header>

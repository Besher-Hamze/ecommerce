<div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-grow text-primary" role="status"></div>
</div>

<div class="container-fluid fixed-top">
    <div class="container topbar bg-primary d-none d-lg-block">
        <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
                <small class="me-3"><i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                    <span class="text-white">{{ $storeSettings['address'] ?? '' }}</span></small>
                <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i>
                    <span class="text-white">{{ $storeSettings['email'] ?? '' }}</span></small>
            </div>
            <div class="top-link pe-2">
                <span class="text-white small mx-2">{{ $storeSettings['const_phone'] ?? '' }}</span>
                <span class="text-white small mx-2">{{ $storeSettings['wats_phone'] ?? '' }}</span>
            </div>
        </div>
    </div>
    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl">
            <a href="{{ route('welcome.index') }}" class="navbar-brand">
                <h1 class="text-primary display-6 mb-0">{{ $storeSettings['name'] ?? 'Noor-Store' }}</h1>
            </a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="{{ route('welcome.index') }}" class="nav-item nav-link {{ request()->routeIs('welcome.index') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('products.all') }}" class="nav-item nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">Shop</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Categories</a>
                        <div class="dropdown-menu m-0 bg-white mega-menu shadow border-0">
                            @foreach($navCategories ?? [] as $category)
                                <div class="parent">{{ $category->eng_name }}</div>
                                <a href="{{ route('products.all', ['category_id' => $category->id]) }}" class="dropdown-item">All {{ $category->eng_name }}</a>
                                @foreach($category->children as $child)
                                    <a href="{{ route('products.all', ['category_id' => $child->id]) }}" class="dropdown-item ps-4">{{ $child->eng_name }}</a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('recommendations.index') }}" class="nav-item nav-link">For you</a>
                    <a href="{{ route('contact.index') }}" class="nav-item nav-link">Contact</a>
                </div>
                <div class="d-flex m-3 me-0 align-items-center">
                    <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-3"
                            data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                        <i class="fas fa-search text-primary"></i>
                    </button>
                    <button type="button" class="btn position-relative me-3 my-auto p-0 border-0 bg-transparent" id="cartIconLink"
                       data-bs-toggle="offcanvas" data-bs-target="#miniCartOffcanvas" aria-label="Open cart">
                        <i class="fa fa-shopping-bag fa-2x"></i>
                        <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                              style="top: -5px; left: 15px; height: 20px; min-width: 20px;" id="cart_counter">{{ $cartCount ?? 0 }}</span>
                    </button>
                    @guest
                        <a href="{{ route('login') }}" class="my-auto" aria-label="Login"><i class="fas fa-user fa-2x"></i></a>
                    @else
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle my-auto" data-bs-toggle="dropdown">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
                            <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                <a href="{{ route('user.order') }}" class="dropdown-item">Order History</a>
                                <a href="{{ route('profile.show') }}" class="dropdown-item">Profile</a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.recommendations.index') }}" class="dropdown-item">Reco Engine</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>
    </div>
</div>

<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title">Search Noor-Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex align-items-center">
                <form action="{{ route('products.all') }}" method="GET" class="input-group w-75 mx-auto d-flex">
                    <input type="search" name="search" class="form-control p-3" placeholder="Lipstick, mascara, brand…" required>
                    <button type="submit" class="input-group-text p-3"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

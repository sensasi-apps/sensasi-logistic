<ul class="sidebar-menu">
    <li>
        <a class="nav-link" href="dashboard">
            <i class="fas fa-chart-line"></i>
            <span>Dasbor</span>
        </a>
    </li>

    <li class="menu-header">Data</li>

    <li>
        <a class="nav-link" href="products">
            <i class="fas fa-box"></i>
            <span>Produk</span>
        </a>
    </li>

    @if (Auth::user()->hasRole('Manufacturer|Super Admin'))
        <li>
            <a class="nav-link" href="batches">
                <i class="fas fa-boxes"></i>
                <span>Batch</span>
            </a>
        </li>
    @endif

    <li>
        <a class="nav-link" href="materials">
            <i class="fas fa-pallet"></i>
            <span>Bahan</span>
        </a>
    </li>

    @if (Auth::user()->hasRole('Super Admin'))
        <li class="menu-header">Sistem</li>

        <li>
            <a class="nav-link" href="system/users">
                <i class="fas fa-user-cog"></i>
                <span>Pengguna</span>
            </a>
        </li>

        <li>
            <a class="nav-link" href="{{ route('system.ip-addr') }}">
                <i class="fas fa-network-wired"></i>
                <span>{{ __('IP Addresses') }}</span>
            </a>
        </li>
    @endif
</ul>

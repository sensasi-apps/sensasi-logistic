<ul class="sidebar-menu">
    <li>
        <a class="nav-link" href="blank.html">
            <i class="fas fa-chart-line"></i>
            <span>Dasbor</span>
        </a>
    </li>

    <li class="menu-header">Data</li>

    <li>
        <a class="nav-link" href="blank.html">
            <i class="fas fa-box"></i>
            <span>Produk</span>
        </a>
    </li>

    @if (Auth::user()->hasRole('Manufacturer|Super Admin'))
    <li>
        <a class="nav-link" href="blank.html">
            <i class="fas fa-boxes"></i>
            <span>Batch</span>
        </a>
    </li>
    @endif

    <li>
        <a class="nav-link" href="blank.html">
            <i class="fas fa-pallet"></i>
            <span>Bahan</span>
        </a>
    </li>

    @if (Auth::user()->hasRole('Super Admin'))
        <li class="menu-header">Sistem</li>

        <li>
            <a class="nav-link" href="blank.html">
                <i class="fas fa-user-cog"></i>
                <span>Pengguna</span>
            </a>
        </li>
    @endif

</ul>

<ul class="sidebar-menu">
    <li>
        <a class="nav-link" href="{{ url('dashboard') }}">
            <i class="fas fa-chart-line"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>
    </li>

    <li class="menu-header">Data</li>

    <li>
        <a class="nav-link" href="{{ url('materials') }}">
            <i class="fas fa-pallet"></i>
            <span>{{ __('Materials') }}</span>
        </a>
    </li>

    @if (Auth::user()->hasRole('Manufacturer|Super Admin'))
        <li>
            <a class="nav-link" href="{{ url('manufactures') }}">
                <i class="fas fa-boxes"></i>
                <span>{{ __('Manufactures') }}</span>
            </a>
        </li>
    @endif

    <li>
        <a class="nav-link" href="{{ url('products') }}">
            <i class="fas fa-box"></i>
            <span>{{ __('Products') }}</span>
        </a>
    </li>



    <li class="menu-header">{{ __('Report') }}</li>

    <li>
        <a class="nav-link" href="{{ url('report/materials') }}">
            <i class="fas fa-file-alt"></i>
            <span>{{ __('Materials') }}</span>
        </a>
    </li>

    <li>
        <a class="nav-link" href="{{ url('report/manufactures') }}">
            <i class="fas fa-file-alt"></i>
            <span>{{ __('Manufactures') }}</span>
        </a>
    </li>

    <li>
        <a class="nav-link" href="{{ url('report/products') }}">
            <i class="fas fa-file-alt"></i>
            <span>{{ __('Products') }}</span>
        </a>
    </li>


    @if (Auth::user()->hasRole('Super Admin'))
        <li class="menu-header">{{ __('System') }}</li>

        <li>
            <a class="nav-link" href="{{ url('system/users') }}">
                <i class="fas fa-user-cog"></i>
                <span>{{ __('Users') }}</span>
            </a>
        </li>

        <li>
            <a class="nav-link" href="{{ url('system/ip-addr') }}">
                <i class="fas fa-network-wired"></i>
                <span>{{ __('IP Addresses') }}</span>
            </a>
        </li>
    @endif
</ul>

<ul class="sidebar-menu text-capitalize pb-4">

    @hasanyrole(['Super Admin', 'Stackholder'])
        <li @class(['active' => request()->is('dashboard*')])>
            <a class="nav-link" href="{{ url('dashboard') }}">
                <i class="fas fa-chart-line"></i>
                <span>{{ __('dashboard') }}</span>
            </a>
        </li>
    @endhasanyrole

    @hasanyrole(['Super Admin', 'Warehouse', 'Manufacture', 'Sales', 'Purchase'])
        <li class="menu-header">Data</li>
    @endhasanyrole

    @hasanyrole(['Warehouse', 'Super Admin', 'Purchase'])
        <li @class(['active' => request()->is('materials*')])>
            <a class="nav-link" href="{{ url('materials') }}">
                <i class="fas fa-pallet"></i>
                <span>{{ __('materials') }}</span>
            </a>
        </li>
    @endhasanyrole

    @hasanyrole(['Manufacture', 'Super Admin'])
        <li @class(['active' => request()->is('manufactures*')])>
            <a class="nav-link" href="{{ url('manufactures') }}">
                <i class="fas fa-boxes"></i>
                <span>{{ __('manufactures') }}</span>
            </a>
        </li>
    @endhasanyrole

    @hasanyrole(['Warehouse', 'Sales', 'Super Admin', 'Purchase'])
        <li @class(['active' => request()->is('products*')])>
            <a class="nav-link" href="{{ url('products') }}">
                <i class="fas fa-box"></i>
                <span>{{ __('products') }}</span>
            </a>
        </li>
    @endhasanyrole


    @hasanyrole(['Stackholder', 'Super Admin'])
        <li class="menu-header">{{ __('report') }}</li>

        <li @class(['active' => request()->is('report/materials*')])>
            <a class="nav-link" href="{{ url('report/materials') }}">
                <i class="fas fa-file-alt"></i>
                <span>{{ __('materials') }}</span>
            </a>
        </li>

        <li @class(['active' => request()->is('report/manufactures*')])>
            <a class="nav-link" href="{{ url('report/manufactures') }}">
                <i class="fas fa-file-alt"></i>
                <span>{{ __('manufactures') }}</span>
            </a>
        </li>

        <li @class(['active' => request()->is('report/products*')])>
            <a class="nav-link" href="{{ url('report/products') }}">
                <i class="fas fa-file-alt"></i>
                <span>{{ __('products') }}</span>
            </a>
        </li>
    @endhasanyrole

    @hasanyrole(['Super Admin', 'Admin'])
        <li class="menu-header">{{ __('system') }}</li>
    @endhasanyrole

    @hasanyrole(['Super Admin', 'Admin'])
        <li @class(['active' => request()->is('report/users*')])>
            <a class="nav-link" href="{{ url('system/users') }}">
                <i class="fas fa-user-cog"></i>
                <span>{{ __('users') }}</span>
            </a>
        </li>

        <li @class(['active' => request()->is('system/user-activities*')])>
            <a class="nav-link text-capitalized" href="{{ url('system/user-activities') }}">
                <i class="fas fa-clipboard-list"></i>
                <span>{{ __('user activities') }}</span>
            </a>
        </li>
    @endhasanyrole

    @hasanyrole(['Super Admin'])
        <li class="menu-header">Developer Only</li>

        <li>
            <a class="nav-link" href="{{ url('_/laravel-shell') }}" target="_BLANK">
                <i class="fas fa-terminal"></i>
                <span>Laravel Shell</span>
                <i class="fas fa-external-link-alt"></i>
            </a>
        </li>

        <li>
            <a class="nav-link" href="{{ url('_/terminal') }}" target="_BLANK">
                <i class="fas fa-terminal"></i>
                <span>Terminal</span>
                <i class="fas fa-external-link-alt"></i>
            </a>
        </li>

        <li>
            <a class="nav-link" href="{{ url('_/basic-page-format') }}" target="_BLANK">
                <i class="fas fa-border-all"></i>
                <span>Basic page format</span>
            </a>
        </li>

        <li>
            <a class="nav-link" href="{{ url('_/phpinfo') }}" target="_BLANK">
                <i class="fab fa-php"></i>
                <span>phpinfo</span>
                <i class="fas fa-external-link-alt"></i>
            </a>
        </li>

        <li class="{{ request()->is('_/ip-addr*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('_/ip-addr') }}">
                <i class="fas fa-network-wired"></i>
                <span>{{ __('IP Addresses') }}</span>
            </a>
        </li>
    @endhasanyrole
</ul>

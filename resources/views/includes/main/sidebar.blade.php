<div class="main-sidebar">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="index.html">ROOMING</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="index.html">RM</a>
    </div>
    <ul class="sidebar-menu">
      @if (Auth::user()->role == 'USER')

        <li class="menu-header">Dashboard</li>
        <li><a class="nav-link" href="{{ route('user.dashboard') }}"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>

        <li class="menu-header">RUANGAN</li>
        <li class="{{ request()->is('room*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('room-list.index') }}">
            <i class="fas fa-door-open"></i> <span>List Ruangan</span>
          </a>
        </li>
        <li class="menu-header">ALAT TEST</li>
        <li class="{{ request()->is('alat-test*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('alat-test.index') }}">
            <i class="fa solid fa-swatchbook"></i> <span>List Alat Test</span>
          </a>
        </li>

        <li class="menu-header">TRANSAKSI</li>
        <li class="{{ request()->is('my-booking-list*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('my-booking-list.index') }}">
            <i class="fas fa-list"></i> <span>My Booking Room List</span>
          </a>
        </li>

        <li class="{{ request()->is('my-booking-alat-test-list*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('my-booking-alat-test-list.index') }}">
            <i class="fas fa-vials"></i> <span>My Booking Alat Test List</span>
          </a>
        </li>

        <li class="menu-header">SETTING</li>
        <li class="{{ request()->is('change-pass*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('user.change-pass.index') }}">
            <i class="fas fa-key"></i> <span>Ganti Password</span>
          </a>
        </li>

      @endif

      @if (Auth::user()->role == 'ADMIN')

        <li class="menu-header">Dashboard</li>
        <li><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>

        <li class="menu-header">DATA MASTER</li>
        <li class="{{ request()->is('admin/room*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('room.index') }}">
            <i class="fas fa-door-open"></i> <span>Ruangan</span>
          </a>
        </li>
        <li class="{{ request()->is('admin/alat-test*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('alat-test-list.index') }}">
            <i class="fas fa-toolbox"></i><span>Alat Test Psikologi</span>
          </a>
        </li>
        <li class="{{ request()->is('admin/user*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('user.index') }}">
            <i class="fas fa-user"></i> <span>User</span>
          </a>
        </li>

        <li class="menu-header">TRANSAKSI</li>
        <li class="{{ request()->is('admin/booking-list*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('booking-list.index') }}">
            @inject('booking_list', 'App\Models\BookingList')
            <i class="fas fa-list"></i> <span>{{ $booking_list->where("status", "PENDING")->count() > 0 ? '('.$booking_list->where("status", "PENDING")->count().')' : '' }} Booking List</span>
          </a>
        </li>

        <li class="menu-header">SETTING</li>
        <li class="{{ request()->is('admin/change-pass*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.change-pass.index') }}">
            <i class="fas fa-key"></i> <span>Ganti Password</span>
          </a>
        </li>

      @endif

      </ul>

  </aside>
</div>
<div class="dl-admin-nav">
    <a href="/admin" class="{{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">Dashboard</a>
    <a href="/admin/users" class="{{ request()->is('admin/users') ? 'active' : '' }}">Users</a>
    <a href="/admin/gigs" class="{{ request()->is('admin/gigs') ? 'active' : '' }}">Listings</a>
    <a href="/admin/disputes" class="{{ request()->is('admin/disputes') ? 'active' : '' }}">Disputes</a>
    <a href="/admin/reviews" class="{{ request()->is('admin/reviews*') ? 'active' : '' }}">Reviews</a>
    <a href="/admin/support" class="{{ request()->is('admin/support') ? 'active' : '' }}">Support</a>
    <a href="/admin/settings" class="{{ request()->is('admin/settings') ? 'active' : '' }}">Settings</a>
</div>

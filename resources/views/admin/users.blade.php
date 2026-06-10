@extends('layout')

@section('title')
    | Admin Users
@endsection

@section('content')
    <div class="container py-4">
        @include('admin.partials.nav')
        <h4 class="fw-semibold mb-4">Users</h4>
        <table class="table table-striped">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Admin</th><th>Rating</th></tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><a href="/users/{{ $user->id }}">{{ $user->name }}</a></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->acc_type == 1 ? 'Worker' : 'Client' }}</td>
                        <td>{{ $user->is_admin ? 'Yes' : 'No' }}</td>
                        <td>{{ $user->averageRating() ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
@endsection

@extends('layout')

@section('title')
    | Worker Contracts
@endsection

@section('content')
    <br>
    <h4 class="text-center"><b>Contracts assigned to you</b></h4>
    <br>
    @unless (count($tasks) == 0)
        <div class="container">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Listing</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Delivery</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td>
                                <a href="/users/{{ $task->owner }}" style="text-decoration: none;">
                                    {{ $task->ownerUser->name ?? 'Client' }}
                                </a>
                            </td>
                            <td>
                                @if ($task->gig)
                                    <a href="/gigs/{{ $task->gig->id }}">{{ $task->gig->title }}</a>
                                @endif
                            </td>
                            <td>${{ $task->price }}</td>
                            <td>
                                @include('contracts.partials.status-badge', ['status' => $task->status])
                            </td>
                            <td>
                                @if ($task->latestDeliverable)
                                    {{ ucfirst(str_replace('_', ' ', $task->latestDeliverable->status)) }}
                                @elseif ($task->status === 'draft')
                                    Awaiting contract acceptance
                                @elseif ($task->status === 'active')
                                    Not submitted
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <a href="/tasks/{{ $task->id }}" class="btn btn-sm btn-primary">View Contract</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <h5 class="bg-light text-danger p-1 text-center">You have no contracts yet!</h5>
    @endunless
@endsection

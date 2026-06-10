@extends('layout')

@section('title')
    | Client Contracts
@endsection
@section('content')
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show container" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <br>
    <h4 class="text-center"><b>Contracts you've hired</b></h4>
    <br>
    @unless (count($tasks) == 0)
        <div class="container">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Worker</th>
                        <th>Listing</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td>
                                <a href="/users/{{ $task->user_id }}" style="text-decoration: none;">
                                    {{ $task->user->name }}
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
                                @if ($task->isFullyPaid() || $task->payment_flag)
                                    <span class="text-success">Paid</span>
                                @elseif ($task->canPay() || $task->milestones->where('status', 'approved')->isNotEmpty())
                                    <span class="text-warning">Ready to pay</span>
                                @else
                                    <span class="text-muted">Pending</span>
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

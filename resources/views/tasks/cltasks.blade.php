@extends('layout')

@section('title')
    | Client tasks
@endsection
@section('content')
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show container" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <?php
    if (Auth::user()->acc_type == 2) {
        $tasks = App\Models\Tasks::where('owner', '=', Auth::user()->id)->get();
    } else {
        $tasks = App\Models\Tasks::where('user_id', '=', Auth::user()->id)->get();
    }
    
    ?>
    <br>
    <h4 class="text-center"><b>This is the clients you've hired for tasks.</b></h4>
    <br>
    @unless (count($tasks) == 0)
        <div class="container">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>worker post</th>
                        <th>Job</th>
                        <th>Time</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Checkout</th>
                    </tr>
                </thead>
                @foreach ($tasks as $task)
                    <tbody>
                        <?php
                        $messageTime = $task->created_at->setTimezone('Asia/Gaza');
                        $gig = App\Models\Gigs::find($task->gig_id);
                        $sender = App\Models\User::find($task->user_id);
                        ?>
                        <td>
                            <h5>
                                <a class="" href="/users/{{ $task->user_id }}" id="{{ $task->user_id }}"
                                    style="text-decoration: none;">{{ $sender->name }}
                                </a>
                            </h5>
                        </td>
                        <td>
                            {{ $task->content }}
                        </td>
                        <td>
                            @if ($task->gig_id)
                                <a href="/gigs/{{ $gig->id }}">
                                    <p>{{ $gig->title }}</p>
                                </a>
                            @else
                            @endif
                        </td>
                        <td>
                            {{ $messageTime->diffForHumans() }}
                        </td>
                        <td>
                            {{ $task->price }}
                        </td>
                        <td>
                            @if ($task->status == 'Pending')
                                <div class="row">
                                    <div class="col">
                                        <p>
                                            <i class="fa-solid fa-gear fa-spin text-danger"></i>
                                            {{ $task->status }}
                                        </p>
                                    </div>
                                    <div class="col">
                                        <form method="post" action="/task/edit/{{ $task->id }}">
                                            @csrf
                                            {{-- @method('PUT') --}}
                                            <input hidden name="user_id" value="{{ $task->user_id }}">
                                            @if ($task->gig_id)
                                                <input hidden name="gig_id" value="{{ $gig->id }}">
                                            @endif
                                            <input hidden name="owner" value="{{ Auth::user()->id }}">

                                            <input hidden name="status" value="Done">
                                            <input hidden name="price" value="{{ $task->price }}">

                                            <input hidden name="content" value="{{ $task->content }}">

                                            @if ($task->payment_flag)
                                                <input hidden name="payment_flag" value="{{ $task->payment_flag }}">
                                            @else
                                                <input hidden name="payment_flag" value="0">
                                            @endif

                                            <button class="btn btn-sm btn-success">
                                                <i class="fa-solid fa-check"></i>
                                                Close</button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($task->status == 'Done'&& $task->payment_flag==0)
                                <P>
                                    <i class="fa-solid fa-xmark text-danger"></i>
                                    Cancelled.
                                </P>
                            @else
                                <P>
                                    <i class="fa-solid fa-check text-success"></i>
                                    {{ $task->status }}
                                </P>
                            @endif
                        </td>
                        <td>
                            @if ($task->status == 'Pending' && $task->payment_flag == 0)
                                <form action="/session" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type='hidden' name="total" value="{{ $gig->salary }}">
                                    <input type='hidden' name="productname" value="{{ $gig->title }}">
                                    <input type='hidden' name="sender" value="{{ Auth::user()->name }}">
                                    <input type='hidden' name="reciever" value="{{ $sender->name }}">
                                    <input type='hidden' name="task" value="{{ $task->id }}">
                                    <button class="btn btn-success btn-sm" type="submit" id="checkout-live-button"><i
                                            class="fa fa-money"></i>
                                        Checkout</button>
                                </form>
                            @elseif ($task->status == 'Done' && $task->payment_flag == 0)
                                Task cancelled.
                            @else
                                Payment reached out.
                            @endif
                        </td>

                    </tbody>
                @endforeach
            </table>
        @else
            <h5 class="bg-light text-danger p-1">You have nothing in your tasks list!</h5>
        </div>
    @endunless
    </div>
@endsection

@extends('layout')

@section('title')
    | Worker tasks
@endsection

@section('content')
    <?php
    $tasks = App\Models\Tasks::where('user_id', '=', Auth::user()->id)->get();
    ?>
    <br>
    <h4 class="text-center"><b>This is the tasks you have to do</b></h4>
    <br>
    @unless (count($tasks) == 0)
        <div class="container">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Job holder</th>
                        <th>Worker post</th>
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
                        $sender = App\Models\User::find($task->owner);
                        ?>
                        <td>
                            <h5>
                                <a class="" href="/users/{{ $task->owner }}" id="{{ $task->owner }}"
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
                                </div>
                            @elseif($task->status == 'Done' && $task->payment_flag==0)
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
                            @if ($task->status == 'Done' && $task->payment_flag == 0)
                                {{-- <i class="fa-solid fa-xmark text-danger"></i> --}}
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

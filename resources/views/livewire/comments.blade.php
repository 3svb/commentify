<div>
    {{-- <section class=""> --}}
        <div class="mx-auto">
            {{-- <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg lg:text-2xl font-bold text-gray-900 dark:text-white">Discussion
                    ({{$comments->count()}})</h2>
            </div> --}}
            @auth
            @include('commentify::livewire.partials.comment-form',[
            'method'=>'postComment',
            'state'=>'newCommentState',
            'inputId'=> 'comment',
            'inputLabel'=> 'Your comment',
            'button'=>'Post',
            'quote'=> $quote
            ])
            @else
            {{-- <a class="mt-2 text-sm text-white" href="/login">Log in to comment!</a> --}}
            {{-- <p class="text-gray-400">Nog geen data</p> --}}
            @endauth
            @if($comments->count())
            @foreach($comments as $comment)
            <livewire:comment :$comment :key="$comment->id" />
            @endforeach
            {{$comments->links(data: ['scrollTo' => false])}}

            @else
            {{-- <p class="text-white">Nog geen gezever ...!</p> --}}
            @endif
        </div>

        {{--
    </section> --}}
</div>

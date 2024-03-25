<div>
    @if ($isEditing)
        @include('commentify::livewire.partials.comment-form', [
            'method' => 'editComment',
            'state' => 'editState',
            'inputId' => 'reply-comment',
            'inputLabel' => 'Your Reply',
            'button' => 'Wijzig comment',
        ])
    @else
        <article
            class="p-4 mb-1 text-base bg-white rounded-lg dark:bg-gray-900
    @auth @if ($comment->user->id === auth()->user()->id)
            border border-blue-900
        @endif @endauth
    ">
            <footer class="flex justify-between items-center mb-1">

                <div class="flex items-center">
                    <p class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white">
                        {{-- <img class="mr-2 w-6 h-6 rounded-full" src="{{ $comment->user->avatar() }}"
                            alt="{{ $comment->user->username }}"> --}}
                        <x-avatar class="mr-2" :url="$comment->user->getAvatarThumb2()" />
                        {{ $comment->user->username }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <time pubdate datetime="{{ $comment->presenter()->relativeCreatedAt() }}"
                            title="{{ $comment->presenter()->relativeCreatedAt() }}">
                            {{ $comment->presenter()->relativeCreatedAt() }}
                        </time>
                    </p>
                </div>

                <div
                    class="relative @auth @if (auth()->user()->hasRole('admin')) @elseif($comment->user->id !== auth()->user()->id) hidden @endif @endauth">
                    <button wire:click="$toggle('showOptions')"
                        class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-400 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-50 dark:bg-gray-900 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        type="button">
                        <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z">
                            </path>
                        </svg>
                    </button>
                    <!-- Dropdown menu -->
                    @if ($showOptions)
                        <div
                            class="absolute z-10 top-full right-0 mt-1 w-36 bg-white rounded-lg divide-y divide-gray-100
                    shadow dark:bg-gray-700 dark:divide-gray-600">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-300">
                                @can('update_comment', $comment)
                                    <li>
                                        <button wire:click="$toggle('isEditing')" type="button"
                                            class="block w-full text-left py-2 px-4
                                           dark:hover:text-gray-100">
                                            Wijzig
                                        </button>
                                    </li>
                                @endcan
                                @can('destroy_comment', $comment)
                                    <li>
                                        <button x-on:click="confirmCommentDeletion" x-data="{
                                            confirmCommentDeletion() {
                                                if (window.confirm('You sure to delete this comment?')) {
                                                    @this.call('deleteComment')
                                                }
                                            }
                                        }"
                                            class="block w-full text-left py-2 px-4 dark:hover:text-gray-100">
                                            Verwijder
                                        </button>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    @endif
                </div>
            </footer>
            <p class="ms-4 mt-2 text-gray-500 dark:text-white">
                {{-- {{ $comment->body }} --}}
                {{-- {!! $comment->presenter()->replaceUserMentions($comment->presenter()->markdownBody()) !!} --}}
                {!! $comment->presenter()->replaceUserMentions($comment->body) !!}
            </p>

            @auth
                <div class="flex items-center mt-4 space-x-4">
                    <livewire:like :$comment :key="$comment->id" />
                    @include('commentify::livewire.partials.comment-reply')
                </div>
            @endauth
        </article>
    @endif
    @if ($isReplying)
        @include('commentify::livewire.partials.comment-form', [
            'method' => 'postReply',
            'state' => 'replyState',
            'inputId' => 'reply-comment',
            'inputLabel' => 'Your Reply',
            'button' => 'Reply',
        ])
    @endif

    @if ($hasReplies)
        @if (count($comment->children) > 0)
            <article class="mb-1 ml-1 lg:ml-12
      dark:bg-gray-900 rounded-lg">
                @foreach ($comment->children as $child)
                    <livewire:comment :comment="$child" :key="$child->id" />
                @endforeach
            </article>
        @endif
    @endif

    @script
        <script>
            function detectAtSymbol() {
                const textarea = document.getElementById('reply-comment');
                if (!textarea) {
                    return;
                }

                const cursorPosition = textarea.selectionStart;
                const textBeforeCursor = textarea.value.substring(0, cursorPosition);
                const atSymbolPosition = textBeforeCursor.lastIndexOf('@');

                if (atSymbolPosition !== -1) {
                    const searchTerm = textBeforeCursor.substring(atSymbolPosition + 1);
                    if (searchTerm.trim().length > 0) {
                        @this.dispatch('getUsers', {
                            searchTerm: searchTerm
                        });
                    }
                }
            }
        </script>
    @endscript
</div>

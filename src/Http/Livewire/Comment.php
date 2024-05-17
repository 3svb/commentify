<?php

namespace Usamamuneerchaudhary\Commentify\Http\Livewire;

use App\Traits\General;
use App\Traits\RandomQuotes;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Usamamuneerchaudhary\Commentify\Models\User;

class Comment extends Component
{
    use AuthorizesRequests, General, RandomQuotes;

    public $comment;

    public $users = [];

    public $isReplying = false;
    public $hasReplies = true;

    public string $quote = '';

    public $showOptions = false;

    public $isEditing = false;

    public $replyState = [
        'body' => ''
    ];

    public $editState = [
        'body' => ''
    ];

    protected $validationAttributes = [
        'replyState.body' => 'Reply',
        'editState.body' => 'Reply'
    ];



    /**
     * @param $isEditing
     * @return void
     */
    public function updatedIsEditing($isEditing): void
    {
        if (!$isEditing) {
            return;
        }
        $this->editState = [
            'body' => $this->comment->body
        ];
    }

    /**
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editComment(): void
    {
        $this->authorize('update_comment', $this->comment);
        $this->validate([
            'editState.body' => 'required|min:2'
        ]);
        $this->comment->update($this->editState);
        $this->isEditing = false;
        $this->showOptions = false;
    }

    /**
     * @return void
     * @throws AuthorizationException
     */
    #[On('refresh')]
    public function deleteComment(): void
    {
        $this->authorize('destroy_comment', $this->comment);
        $this->comment->delete();
        $this->showOptions = false;
        $this->dispatch('refresh');
    }

    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
    {
        $this->quote = $this->getRandomQuote(false);
        return view('commentify::livewire.comment');
    }

    /**
     * @return void
     */
    #[On('refresh')]
    public function postReply(): void
    {
        if (!$this->comment->isParent()) {
            return;
        }
        $this->validate([
            'replyState.body' => 'required'
        ]);

        $reply = $this->comment->children()->make($this->replyState);
        $reply->user()->associate(auth()->user());
        $reply->commentable()->associate($this->comment->commentable);
        $reply->team_id = $this->getTeam()->id; // per team
        $reply->save();

        $this->replyState = [
            'body' => ''
        ];
        $this->isReplying = false;
        $this->showOptions = false;
        $this->dispatch('refresh')->self();
    }

    /**
     * @param $userName
     * @return void
     */
    public function selectUser($userName): void
    {
        if ($this->replyState['body']) {
            $this->replyState['body'] = preg_replace(
                '/@(\w+)$/',
                '@' . str_replace(' ', '_', Str::lower($userName)) . ' ',
                $this->replyState['body']
            );
            //            $this->replyState['body'] =$userName;
            $this->users = [];
        } elseif ($this->editState['body']) {
            $this->editState['body'] = preg_replace(
                '/@(\w+)$/',
                '@' . str_replace(' ', '_', Str::lower($userName)) . ' ',
                $this->editState['body']
            );
            $this->users = [];
        }
    }


    /**
     * @param $searchTerm
     * @return void
     */
    #[On('getUsers')]
    public function getUsers($searchTerm): void
    {
        if (!empty($searchTerm)) {
            $this->users = User::where('username', 'like', '%' . $searchTerm . '%')->take(5)->get();
        } else {
            $this->users = [];
        }
    }
}

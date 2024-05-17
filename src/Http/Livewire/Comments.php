<?php

namespace Usamamuneerchaudhary\Commentify\Http\Livewire;

use App\Models\Team;
use App\Traits\General;
use App\Traits\RandomQuotes;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Masmerise\Toaster\Toastable;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class Comments extends Component
{
    use WithPagination,
        Toastable,
        General,
        WithoutUrlPagination,
        RandomQuotes;

    public $model;

    public $users = [];

    public string $quote = '';

    public $showDropdown = false;

    protected $numberOfPaginatorsRendered = [];

    public $newCommentState = [
        'body' => ''
    ];

    protected $listeners = [
        'refresh' => '$refresh'
    ];

    protected $validationAttributes = [
        'newCommentState.body' => 'comment'
    ];

    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    #[On('refreshTeamData')]
    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
    {
        $this->quote = $this->getRandomQuote();
        $comments = $this->model
            ->comments()
            ->with('user', 'children.user', 'children.children')
            ->where('team_id', $this->getTeam()->id)
            ->parent()
            ->latest()
            ->paginate(config('commentify.pagination_count', 10))
            ->setPath('/');
        return view('commentify::livewire.comments', [
            'comments' => $comments
        ]);
    }


    /**
     * @return void
     */
    #[On('refresh')]
    public function postComment(): void
    {
        $this->validate([
            'newCommentState.body' => 'required'
        ]);

        $comment = $this->model->comments()->make($this->newCommentState);
        $comment->team_id = $this->getTeam()->id; // per team
        $comment->user()->associate(auth()->user());
        $comment->save();

        $this->newCommentState = [
            'body' => ''
        ];
        $this->users = [];
        $this->showDropdown = false;

        $this->resetPage();
        // session()->flash('message', 'Comment Posted Successfully!');
        $this->success('Uwe zeg is geplaatst');
    }
}

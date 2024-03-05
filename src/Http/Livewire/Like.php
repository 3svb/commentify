<?php

namespace Usamamuneerchaudhary\Commentify\Http\Livewire;


use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class Like extends Component
{

    public $comment;
    public $count;


    public function mount(\Usamamuneerchaudhary\Commentify\Models\Comment $comment): void
    {

        $this->comment = $comment;
        $this->count = $comment->likes_count;
    }

    public function like(): void
    {
        if ($this->comment->user->id === auth()->user()->id) {
            return;
        }

        // Get user/IP
        $user = auth()->user() ? auth()->id() : null;
        // $ip = request()->ip();

        // Check if user already liked
        $alreadyLiked = $this->comment->likes()
            ->where('user_id', $user)
            // ->orWhere('ip', $ip)
            ->exists();

        if ($alreadyLiked) {
            // User already liked, remove like
            $this->comment->likes()
                ->where('user_id', $user)
                // ->orWhere('ip', $ip)
                ->delete();

            $this->count--;
        } else {

            // User has not liked yet, add like
            $like = $this->comment->likes()->create([
                'user_id' => $user,
                // 'ip' => $ip
            ]);

            $this->count++;
        }
    }



    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
    {
        return view('commentify::livewire.like');
    }
}

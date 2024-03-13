<?php

namespace Usamamuneerchaudhary\Commentify\Traits;

trait HasUserAvatar
{

    /**
     * @return string
     */
    public function avatar(): string
    {
        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . $this->username;
        // return 'https://gravatar.com/avatar/' . md5($this->email) . '?s=80&d=mp';
    }
}

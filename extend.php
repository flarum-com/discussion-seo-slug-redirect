<?php

namespace Flarum\DiscussionSeoSlugRedirect;

use Flarum\Extend;

return [
    (new Extend\Middleware('forum'))
        ->add(Middleware\Redirect::class)
];

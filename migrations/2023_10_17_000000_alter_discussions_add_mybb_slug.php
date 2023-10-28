<?php

use Flarum\Database\Migration;

return Migration::addColumns('discussions', [
    'seo_slug' => ['text', 'nullable' => true]
]);

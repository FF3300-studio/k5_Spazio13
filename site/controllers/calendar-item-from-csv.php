<?php

use NonDeterministic\Helpers\CollectionHelper;

return function ($page, $site, $kirby) {
    return [
        // Forniamo formData (fallback vuoto) per evitare errori nel snippet layouts
    ] + CollectionHelper::formDataFor($page);
};

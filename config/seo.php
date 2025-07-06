<?php

return [
    'default_image' => '/images/logo/v1/icon.png',
    // 从环境变量获取不进行索引的路由，并分割成数组
    'disabled_index_routes' => array_filter(array_map('trim', explode(',', env('SEO_DISABLED_INDEX_ROUTES', '')))),
];
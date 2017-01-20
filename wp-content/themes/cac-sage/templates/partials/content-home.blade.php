@include('partials.post-list', [
    'title'     => 'Learn More About',
    'partial'   => 'partials.content-page-callout',
    'args'  => [
        'posts_per_page'    => 3,
        'post_type'         => 'page',
        'category_name'     => 'homepage',
        'orderby'           => 'rand',
    ]
])

@include('partials.post-list', [
    'title'     => 'Recent Posts',
    'partial'   => 'partials.content-post-callout',
    'args'  => [
        'posts_per_page' => 6
    ]
])

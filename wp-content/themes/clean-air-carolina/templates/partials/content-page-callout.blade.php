<article class="page page-callout">
    <a href="@php(the_permalink())">
        <figure>
            @php(the_post_thumbnail())
        </figure>
        <footer>
            @php
                /** Special Title post meta overrides the title **/
                $postmeta   = get_post_meta( get_the_ID() );
                $title      = get_the_title();

                if( ! empty( $postmeta[ 'cac_special_title' ] )
                    && ! empty( $postmeta[ 'cac_special_title' ][0] ) ) {
                    $title = $postmeta[ 'cac_special_title' ][0];
                }

            @endphp
            <h3 class="title">{{ $title }}</h3>
            <div class="excerpt">
                @php(the_excerpt())
            </div>
        </footer>
    </a>
</article>
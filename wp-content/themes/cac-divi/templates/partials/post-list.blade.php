@if( $args && $partial )
    @if( $title  )
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="title-featured">
                <span>{{ $title  }}</span>
            </h1>
        </div>
    </div>
    @endif
    @php( $the_query = new WP_Query( $args ) )
    <div class="row">
        @if($the_query->have_posts())
            @while ( $the_query->have_posts() )
                @php($the_query->the_post())
                <div class="col-md-4">
                    @include( $partial )
                </div>
            @endwhile
            @php(wp_reset_postdata())
        @endif
    </div>
@endif
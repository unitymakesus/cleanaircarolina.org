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
            @endphp
            <h3 class="title">{{ $title }}</h3>
            <div class="excerpt">
                @php(the_excerpt())
            </div>
        </footer>
    </a>
</article>

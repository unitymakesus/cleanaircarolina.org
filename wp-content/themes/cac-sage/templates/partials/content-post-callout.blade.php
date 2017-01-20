<article class="post post-callout">
    <figure>
        @php(the_post_thumbnail())
    </figure>
    <h3 class="title">@php(the_title())</h3>
    @php(the_excerpt())
    <a href="@php(the_permalink())" class="btn btn-primary permalink">Read More</a>
</article>

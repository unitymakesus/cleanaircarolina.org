<div class="hero-wrap">
    <div class="content">
        <h2 class="title">
            <span class="super">
                Everyone has a
            </span>
            <span class="curved">
                right to breath
            </span>
            <span class="sub">
                clean, healthy air.
            </span>
        </h2>
        <p>In the two seconds it takes you to read this sentence you have take about two breaths...of air.
            That adds up to about 23,000 breaths a day. We want to make sure every breath you take is good for you.</p>
    </div>
    <div class="type type-{{$type}}">
        @php
            $theme_url      = get_template_directory_uri();
            $video_asset    = $theme_url . '/assets/videos/';
        @endphp
        <div class="video-wrap">
            <video src="{{$video_asset}}woman-running-through-park.mp4" id="hero-video" autoplay
                   poster="{{$video_asset}}woman-running-through-park.png"></video>
        </div>
    </div>
</div>
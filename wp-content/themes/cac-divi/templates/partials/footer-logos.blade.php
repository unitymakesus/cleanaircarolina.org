<figure class="footer-logos">
  @php
    use App as A;
    $logos = [
        'green-hosting' => [
                              'img' => 'hosting-badge-2.png',
                              'link' => ''
                            ],
        'bbb'           => [
                              'img' => 'accredited-charity-logo-blue.jpg',
                              'link' => 'http://www.bbb.org/charlotte/business-reviews/charity-environment/clean-air-carolina-in-charlotte-nc-235424',
                              'target' => '_blank'
                            ],
        'esnc'          => [
                              'img' => 'ESNC.jpg',
                              'link' => 'http://www.earthsharenc.org/',
                              'target' => '_blank'
                            ],
        'aqi'           => [
                              'img' => 'aqi_ex1.jpg',
                              'link' => '/north-carolinas-air-quality/'
                            ],
    ];
  @endphp
  @foreach($logos as $key => $logo)
    @if(!empty($logo['link']))
      <a href="{{$logo['link']}}" target="{{ $logo['target'] }}">
        <img class="{{$key}}" src="{{ A\sage('assets')->getUri('images/' . $logo['img']) }}" />
      </a>
    @else
      <img class="{{$key}}" src="{{ A\sage('assets')->getUri('images/' . $logo['img']) }}" />
    @endif
  @endforeach
</figure>

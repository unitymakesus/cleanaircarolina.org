<figure class="footer-logos">
  @php
    use App as A;
    $logos = [
        'green-hosting' => [
                              'img' => 'hosting-badge-2.png',
                              'alt' => 'This site is eco-friendly',
                              'link' => ''
                            ],
        'bbb'           => [
                              'img' => 'accredited-charity-logo-blue.jpg',
                              'alt' => 'BBB Accredited Charity',
                              'link' => 'http://www.bbb.org/charlotte/business-reviews/charity-environment/clean-air-carolina-in-charlotte-nc-235424',
                              'target' => '_blank'
                            ],
        'esnc'          => [
                              'img' => 'ESNC.jpg',
                              'alt' => 'EarthShare North Carolina',
                              'link' => 'http://www.earthsharenc.org/',
                              'target' => '_blank'
                            ],
        'aqi'           => [
                              'img' => 'aqi_ex1.jpg',
                              'alt' => 'AQI Air Quality Index',
                              'link' => '/north-carolinas-air-quality/'
                            ],
    ];
  @endphp
  @foreach($logos as $key => $logo)
    @if(!empty($logo['link']))
      <a href="{{$logo['link']}}" target="{{ $logo['target'] }}">
        <img class="{{$key}}" alt="{{ $logo['alt'] }}" src="{{ A\sage('assets')->getUri('images/' . $logo['img']) }}" />
      </a>
    @else
      <img class="{{$key}}" alt="{{ $logo['alt'] }}" src="{{ A\sage('assets')->getUri('images/' . $logo['img']) }}" />
    @endif
  @endforeach
</figure>

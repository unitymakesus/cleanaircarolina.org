<figure class="footer-logos">
  @php
    use App as A;
    $logos = [
        'green-hosting' => 'hosting-badge-2.png',
        'bbb'           => 'accredited-charity-logo-blue.jpg',
        'esnc'          => 'ESNC.jpg',
        'aqi'           => 'aqi_ex1.jpg'
    ];
  @endphp
  @foreach($logos as $key => $logo)
    <img class="{{$key}}" src="{{ A\sage('assets')->getUri('images/' . $logo) }}" />
  @endforeach
</figure>

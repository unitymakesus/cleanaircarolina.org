<figure class="footer-logos">
  @php
    $logos = [
        'green-hosting' => 'hosting-badge-2.png',
        'bbb'           => 'accredited-charity-logo-blue.jpg',
        'esnc'          => 'ESNC.jpg',
        'aqi'           => 'aqi_ex1.jpg'
    ];
  @endphp
  @foreach($logos as $key => $logo)
    <img class="{{$key}}" src="@asset(images)/{{$logo}}" />
  @endforeach
</figure>

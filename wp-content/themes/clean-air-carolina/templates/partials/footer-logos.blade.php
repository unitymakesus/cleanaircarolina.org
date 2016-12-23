@php
$logo_location = get_template_directory_uri();
$logos = [
    'green-hosting' => 'hosting-badge-2.png',
    'bbb'           => 'accredited-charity-logo-blue.jpg',
    'esnc'          => 'ESNC.jpg'
];
echo '<figure class="footer-logos">';
foreach($logos as $key => $logo){
    echo '<img class="'.$key.'" src="'.$logo_location.'/assets/images/'.$logo.'" />';
}
echo '</figure>';
@endphp
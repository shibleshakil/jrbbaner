<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1130">
    <title>Jiwer Rawda — preview</title>
</head>

<body>
    @include('availability_banners._banner_body', [
    'banner' => $banner,
    'sidebarLogo' => asset('promotion-assets/banner-1ns.png'),
    'stack1' => asset('promotion-assets/hotel-1.webp'),
    'stack2' => asset('promotion-assets/room-2.jpg'),
    'stack3' => asset('promotion-assets/room-3.jpg'),
    'iconPhone' => asset('promotion-assets/icons/phone.png'),
    'iconWhatsapp' => asset('promotion-assets/icons/whatsapp.png'),
    ])
</body>

</html>

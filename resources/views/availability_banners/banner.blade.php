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
    'sidebarLogo' => asset('public/promotion-assets/banner-1ns.png'),
    'stack1' => asset('public/promotion-assets/hotel-1.webp'),
    'stack2' => asset('public/promotion-assets/room-2.jpg'),
    'stack3' => asset('public/promotion-assets/room-3.jpg'),
    'iconPhone' => asset('public/promotion-assets/icons/phone.png'),
    'iconWhatsapp' => asset('public/promotion-assets/icons/whatsapp.png'),
    ])
</body>

</html>

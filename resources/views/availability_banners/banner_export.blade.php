<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1200">
    <title>Availability banner</title>
    <link href="https://fonts.googleapis.com/css2?family=Paprika&display=swap" rel="stylesheet">
    <style>
        :root {
            --arabic-font: 'ArbFONTS-ae-almohanad';
        }

        @font-face {
            font-family: 'ArbFONTS-ae-almohanad';
            src: url("{{ $arabicFontUri ?? '' }}") format('truetype'),
                 url("{{ asset('fonts/ArbFONTS-ae-almohanad.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 1200px;
            height: 1183px;
            overflow: hidden;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            overflow-y: auto;
        }

        .watermark {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            pointer-events: none;

            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%2395c7d6' stroke-width='0.8' opacity='0.35'%3E%3Ccircle cx='50' cy='0' r='50'/%3E%3Ccircle cx='50' cy='100' r='50'/%3E%3Ccircle cx='0' cy='50' r='50'/%3E%3Ccircle cx='100' cy='50' r='50'/%3E%3Ccircle cx='50' cy='50' r='35'/%3E%3C/g%3E%3C/svg%3E");
            /* background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%23064e75' stroke-width='0.8' opacity='0.35'%3E%3Ccircle cx='50' cy='0' r='50'/%3E%3Ccircle cx='50' cy='100' r='50'/%3E%3Ccircle cx='0' cy='50' r='50'/%3E%3Ccircle cx='100' cy='50' r='50'/%3E%3Ccircle cx='50' cy='50' r='35'/%3E%3C/g%3E%3C/svg%3E"); */
            background-repeat: repeat;
            background-size: 100px 100px;
        }

        .banner {
            width: 1200px;
            height: 1183px;
            position: relative;
            background: #fff;
        }

        .hero {
            width: 1200px;
            height: 520px;
            object-fit: cover;
            display: block;
        }

        .logo-panel {
            position: absolute;
            left: 50px;
            top: 0;
            width: 400px;
            height: 1033px;
            background: rgb(11 80 121 / 67%);
        }

        .logo-panel .logo {
            position: absolute;
            left: 110px;
            top: 0;
            width: 180px;
            height: 180px;
            object-fit: contain;
        }

        .img-div {
            position: absolute;
            top: 190px;
            width: 100%;
            height: auto;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .img-div img {
            width: 100%;
            height: 270px;
            object-fit: fill;
        }

        .side-footer {
            position: absolute;
            bottom: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            font-size: x-large;
            font-weight: 700;
            color: #fff;
        }

        .ar {
            direction: rtl;
            font-family: var(--arabic-font), serif;
        }

        .content {
            margin-left: 450px;
        }


        .brand-block {
            text-align: center;
            color: #2d96a6;
            position: relative;
            top: 20px;
        }

        .brand-block .ar-brand {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 2px;
            font-family: var(--arabic-font), serif;
        }

        .brand-block .en-brand {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }


        .stars {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin: 10px 0 8px;
            color: #c9a227;
            font-size: 28px;
            line-height: 1;
            position: relative;
            top: 20px;
        }

        .hotel-title {
            text-align: center;
            font-size: 42px;
            font-weight: 800;
            font-style: italic;
            color: #0a3d6e;
            margin: 0 0 28px;
            top: 20px;
            position: relative;
            letter-spacing: -0.02em;
        }

        .rate {
            margin: 10px 0 30px;
        }

        .room-rate {
            text-align: center;
            font-size: 58px;
            font-weight: 800;
            color: #0a3d6e;
            position: relative;
            margin: 0;
        }

        .fb {
            text-align: center;
            font-size: 58px;
            font-weight: 800;
            color: #0a3d6e;
            position: relative;
            margin: 0;
        }

        .from-to {
            text-align: center;
            font-size: 56px;
            font-weight: 700;
            display: flex;
            flex-direction: column;
            gap: 16px;

        }

        .date {
            background: #e0ab04;
            border-radius: 32px;
            padding: 0px 12px;
            color: #0a3d6e;
        }

        .avilability {
            display: flex;
            justify-content: center;
            position: relative;
        }

        .avilability img {
            width: 500px;
        }

        .footer {
            position: absolute;
            left: 0;
            bottom: 2px;
            width: 1200px;
            height: auto;
            color: #064e75;
            padding: 5px 50px 0 50px;
            font-size: 13px;
            border-top: 4px solid #064e75;
        }

        .footer>p {
            margin: 0 0 8px;
            font-weight: 600;
            font-size: 24px;
        }

        .footer-cols {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .footer-line1 {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #064e75;
            font-weight: 700;
            font-size: 24px;
        }

        .footer-line1 img {
            width: 24px;
            height: 24px;
        }

        .footer-name {
            color: #064e75;
            font-size: 24px;
            font-weight: 700;
        }

        .footer-loc {
            color: #064e75;
            font-size: 24px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    @php
    $terms = [
    'The above rates are net & non commissionable and quoted in Saudi Riyals.',
    'VAT & Municipality fees are included in the rate.',
    'Check in 16:00 hrs and check out 12:00 hrs. One night charged if check out after 16:00 hrs.',
    'Booking confirmed only upon receipt of 50% advance and full payment before guest arrival.',
    'Cancellation and amendment are according to confirmation letter terms.',
    'Triple and quad occupancy will be through extra bed if standard room is not available.',
    'Rates are subject to change without prior notice.',
    ];
    $fallbackContacts = [
    ['number' => '+966597709206', 'name' => 'Sahadath Khan', 'location' => 'Madinah'],
    ['number' => '+966540802329', 'name' => 'Abdur Rahman (Dhomi)', 'location' => 'Makkah'],
    ];
    $contacts = $fallbackContacts;
    $stored = $banner->contact_info;
    if (is_array($stored) && count($stored) >= 2) {
    $parsed = array_map(function ($row) {
    if (is_array($row) && array_key_exists('number', $row) && array_key_exists('name', $row)) {
    return [
    'number' => (string) $row['number'],
    'name' => (string) $row['name'],
    'location' => isset($row['location']) ? (string) $row['location'] : '',
    ];
    }
    if (is_array($row) && isset($row[0], $row[1])) {
    return [
    'number' => (string) $row[0],
    'name' => (string) $row[1],
    'location' => isset($row[2]) ? (string) $row[2] : '',
    ];
    }

    return ['number' => '', 'name' => '', 'location' => ''];
    }, $stored);
    $parsed = array_values(array_filter($parsed, fn ($c) => $c['number'] !== '' && $c['name'] !== ''));
    if (count($parsed) >= 2) {
    $contacts = $parsed;
    }
    }
    foreach (array_keys($contacts) as $i) {
    if (($contacts[$i]['location'] ?? '') !== '') {
    continue;
    }
    $contacts[$i]['location'] = $fallbackContacts[$i]['location'] ?? 'Indonesia';
    }
    @endphp
    <div class="banner">
        <div class="watermark" aria-hidden="true"></div>
        <div class="logo-panel">
            <img class="logo" src="{{ $imageUris['sidebar_logo'] }}" alt="logo">
            <div class="img-div">
                <img src="{{ $imageUris['stack_1'] }}" alt="stack1">
                <img src="{{ $imageUris['stack_2'] }}" alt="stack2">
                <img src="{{ $imageUris['stack_3'] }}" alt="stack3">

            </div>
        </div>
        <div class="content">
            <div class="brand-block">
                <div class="ar-brand">فندق جيور روضة</div>
                <div class="en-brand">Jiwer Rawda For Hotels</div>
            </div>
            <div class="stars">
                <img src="{{asset('public/promotion-assets/icons/star.png')}}" alt="">
                <img src="{{asset('public/promotion-assets/icons/star.png')}}" alt="">
                <img src="{{asset('public/promotion-assets/icons/star.png')}}" alt="">
                <img src="{{asset('public/promotion-assets/icons/star.png')}}" alt="">
            </div>
            <h1 class="hotel-title">{{ $banner->hotel_name }}</h1>

            <!-- last availability last-availability -->

            <div class="avilability">
                <img src="{{asset('public/promotion-assets/availability.png')}}" alt="Last availability">
            </div>

            <div class="rate">
                <p class="room-rate">Room Rate: {{ $banner->room_rate ?? 00 }} SAR</p>
                <p class="fb">F.B: {{ $banner->fb ?? 00 }} SAR</p>
            </div>

            <div class="from-to">
                <div class="from">From <span class="date">{{$banner->from_date->format('d')}}</span> {{$banner->from_date->format('F')}}</div>
                <div class="to">To <span class="date">{{$banner->to_date->format('d')}}</span> {{$banner->to_date->format('F')}}</div>
            </div>
        </div>

        <div class="footer">
            <p>For booking &amp; inquiries please contact us using the following:</p>
            <div class="footer-cols">
                @foreach ($contacts as $c)
                <div class="footer-col">
                    <div class="footer-line1">
                        @if (! empty($footerIcons['phone']))
                        <img src="{{ $footerIcons['phone'] }}" alt="" width="18" height="18">
                        @endif
                        @if (! empty($footerIcons['whatsapp']))
                        <img src="{{ $footerIcons['whatsapp'] }}" alt="" width="18" height="18">
                        @endif
                        <span>{{ $c['number'] }}</span>
                    </div>
                    <div class="footer-name">{{ $c['name'] }}</div>
                    <div class="footer-loc">{{ $c['location'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>

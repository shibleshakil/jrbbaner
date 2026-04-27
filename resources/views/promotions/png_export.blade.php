<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1130">
    <title>Promotion {{ $promotion->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Paprika&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 1130px;
            height: 1600px;
            overflow: hidden;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            overflow-y: auto;
        }

        .banner {
            width: 1130px;
            height: 1600px;
            position: relative;
            background: #fff;
        }

        .hero {
            width: 1130px;
            height: 520px;
            object-fit: cover;
            display: block;
        }

        .logo-panel {
            position: absolute;
            left: 18px;
            top: 0;
            width: 250px;
            height: 250px;
            background: rgba(10, 64, 97, 0.88);
        }

        .logo-panel img {
            position: absolute;
            left: 32px;
            top: 35px;
            width: 180px;
            height: 180px;
            object-fit: contain;
        }

        .watermark {
            position: absolute;
            left: 0;
            right: 0;
            top: 464px;
            bottom: 0;
            pointer-events: none;

            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%2395c7d6' stroke-width='0.8' opacity='0.35'%3E%3Ccircle cx='50' cy='0' r='50'/%3E%3Ccircle cx='50' cy='100' r='50'/%3E%3Ccircle cx='0' cy='50' r='50'/%3E%3Ccircle cx='100' cy='50' r='50'/%3E%3Ccircle cx='50' cy='50' r='35'/%3E%3C/g%3E%3C/svg%3E");
            /* background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%23064e75' stroke-width='0.8' opacity='0.35'%3E%3Ccircle cx='50' cy='0' r='50'/%3E%3Ccircle cx='50' cy='100' r='50'/%3E%3Ccircle cx='0' cy='50' r='50'/%3E%3Ccircle cx='100' cy='50' r='50'/%3E%3Ccircle cx='50' cy='50' r='35'/%3E%3C/g%3E%3C/svg%3E"); */
            background-repeat: repeat;
            background-size: 100px 100px;
        }

        .badges {
            position: absolute;
            left: 0;
            top: 448px;
            width: 100%;
            height: 100px;
            display: flex;
            flex-direction: row;
            align-items: stretch;
            padding-left: 35px;
            gap: 60px;
        }

        .badge-main {
            display: flex;
            height: 100%;
            filter: drop-shadow(0 1px 0 rgba(0, 0, 0, 0.08));
        }

        .badge-main__skew {
            background: #064e75;
            transform: skewX(-14deg);
            display: flex;
            align-items: stretch;
            padding: 0 22px;
        }

        .badge-main__inner {
            transform: skewX(14deg);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .badge-icon {
            width: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #08476e;
            margin-right: 0;
        }

        .badge-icon svg {
            display: block;
            transform: rotate(13deg);
        }

        .badge-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .badge-text .l1 {
            color: #fff;
            font-size: 36px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-text .l2 {
            color: #e6d32c;
            font-size: 32px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-year {
            background: #1272a8;
            transform: skewX(-14deg);
            padding: 0 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-year span {
            transform: skewX(14deg);
            color: #fff;
            font-size: 36px;
            font-weight: 700;
            font-family: 'Paprika', cursive;
        }

        .rates-table-wrap {
            position: absolute;
            left: 28px;
            top: 585px;
            width: 1074px;
        }

        .rates-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 28px;
            color: #152733;
            border: 2px solid #11ccf7;
        }

        .rates-table th,
        .rates-table td {
            border: 1px solid #152733;
            text-align: center;
            vertical-align: middle;
            font-weight: 700;
            padding: 4px 2px;
            word-break: break-word;
        }

        .rates-table tbody tr.empty td {
            border-color: #126d91;
            color: transparent;
        }

        .meals-head {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 18px;
            font-weight: 700;
            padding: 8px 0;
        }

        .line-clamp-1 {
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            display: -webkit-box;
            overflow: hidden;
        }

        .terms-block {
            position: absolute;
            left: 0;
            top: 925px;
            width: 100%;
            padding-left: 0;
        }

        .terms-title {
            background: #064e75;
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            padding: 12px 44px;
            width: fit-content;
            max-width: 100%;
            border-radius: 0 28px 28px 0;
            text-shadow: 0 0 1px rgba(0, 0, 0, 0.2);
        }

        .terms-list {
            margin: 24px 32px 0;
            padding: 0;
            list-style: none;
            font-size: 24px;
            line-height: 1.35;
            color: #1c313f;
            font-weight: 600;
        }

        .terms-list li {
            margin-bottom: 0;
            padding-left: 0;
        }

        .rooms {
            position: absolute;
            top: 1270px;
            display: flex;
            justify-content: space-evenly;
            width: 100%;
        }

        .room-card {
            height: 165px;
            position: relative;
            border: 3px dashed #08bfdf;
            overflow: hidden;
        }

        .room-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .room-card--0 {
            border-radius: 50px 0px;
        }

        .room-card--1 {
            border-radius: 0px 50px;
        }

        .room-card--2 {
            border-radius: 50px 0px;
        }

        .room-card--3 {
            border-radius: 0px 50px;
        }

        .footer {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 1130px;
            height: 150px;
            background: #064e75;
            color: #fff;
            padding: 8px 30px 0;
            font-size: 13px;
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
            color: #ffe35c;
            font-weight: 700;
            font-size: 24px;
        }

        .footer-line1 img {
            width: 24px;
            height: 24px;
        }

        .footer-name {
            color: #e4eff7;
            font-size: 24px;
            font-weight: 700;
        }

        .footer-loc {
            color: #e4eff7;
            font-size: 24px;
            font-weight: 600;
        }

        tbody tr:nth-child(odd) td {
            background: #f6e23257;
        }
    </style>
</head>

<body>
    @php
    $offers = $promotion->offerDetails;
    $rowCount = max(3, $offers->count());
    $colWidths = [200, 200, 170, 170, 170, 140];
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
    $stored = $promotion->contact_info;
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
        <img class="hero" src="{{ $bannerAssets['hero'] }}" alt="" width="1130" height="560">
        <div class="logo-panel">
            <img src="{{ $bannerAssets['logo'] }}" alt="">
        </div>
        <div class="watermark" aria-hidden="true"></div>

        <div class="badges">
            <div class="badge-main">
                <div class="badge-main__skew">
                    <div class="badge-main__inner">
                        <div class="badge-icon">
                            <svg width="24" height="44" viewBox="0 0 24 44" aria-hidden="true">
                                <rect x="2" y="4" width="20" height="36" rx="1" fill="#fff" />
                                <rect x="5" y="8" width="6" height="5" fill="#08476e" />
                                <rect x="13" y="8" width="6" height="5" fill="#08476e" />
                                <rect x="5" y="16" width="6" height="5" fill="#08476e" />
                                <rect x="13" y="16" width="6" height="5" fill="#08476e" />
                                <rect x="5" y="24" width="6" height="5" fill="#08476e" />
                                <rect x="13" y="24" width="6" height="5" fill="#08476e" />
                                <rect x="9" y="32" width="6" height="8" fill="#08476e" />
                            </svg>
                        </div>
                        <div class="badge-text">
                            <span class="l1">( Fareast Rate )</span>
                            <span class="l2">{{$promotion->hotel_name ?? 'Jiwer Rawda For Hotel'}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="badge-year">
                <span>{{$promotion->hotel_year ?? '1448'}}</span>
            </div>
        </div>

        <div class="rates-table-wrap">
            <table class="rates-table">
                <colgroup>
                    @foreach ($colWidths as $w)
                    <col style="width: {{ $w }}px">
                    @endforeach
                </colgroup>
                <thead>
                    <tr class="h1">
                        <th colspan="2">Period</th>
                        <th colspan="3">Room Type</th>
                        <th rowspan="2" style="transform: rotate(-45deg);">Meals</th>
                    </tr>
                    <tr class="h1">
                        <th>From</th>
                        <th>To</th>
                        <th>Double</th>
                        <th>Triple</th>
                        <th>Quad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($offers as $offer)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($offer->from_date)->format('d-M-y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($offer->to_date)->format('d-M-y') }}</td>
                        <td>{{ $offer->double_rate }}</td>
                        <td>{{ $offer->triple_rate }}</td>
                        <td>{{ $offer->quad_rate }}</td>
                        <td class="line-clamp-1">{{ $offer->meals }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="terms-block">
            <div class="terms-title">Terms &amp; Conditions</div>
            <ul class="terms-list">
                @foreach ($terms as $term)
                <li>* {{ $term }}</li>
                @endforeach
            </ul>
        </div>

        <div class="rooms">
            @foreach ($bannerAssets['rooms'] as $idx => $roomSrc)
            <div class="room-card room-card--{{ $idx }}">
                <img src="{{ $roomSrc }}" alt="">
            </div>
            @endforeach
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
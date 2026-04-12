@php
$from = $banner->from_date;
$to = $banner->to_date;
$fromDay = $from->format('j');
$toDay = $to->format('j');
$fromMonth = $from->format('F');
$toMonth = $to->format('F');
@endphp
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,700;0,800;1,500;1,700;1,800&display=swap" rel="stylesheet">
<style>
    * {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        width: 1200px;
        height: 1183px;
        /* overflow: hidden; */
        font-family: 'Montserrat', 'Segoe UI', Roboto, sans-serif;
        background: #fff;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        overflow-y: auto;
    }

    .banner-root {
        width: 1200px;
        height: 1183px;
        display: flex;
        flex-direction: row;
        pointer-events: none;

        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%2395c7d6' stroke-width='0.8' opacity='0.35'%3E%3Ccircle cx='50' cy='0' r='50'/%3E%3Ccircle cx='50' cy='100' r='50'/%3E%3Ccircle cx='0' cy='50' r='50'/%3E%3Ccircle cx='100' cy='50' r='50'/%3E%3Ccircle cx='50' cy='50' r='35'/%3E%3C/g%3E%3C/svg%3E");
        /* background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%23064e75' stroke-width='0.8' opacity='0.35'%3E%3Ccircle cx='50' cy='0' r='50'/%3E%3Ccircle cx='50' cy='100' r='50'/%3E%3Ccircle cx='0' cy='50' r='50'/%3E%3Ccircle cx='100' cy='50' r='50'/%3E%3Ccircle cx='50' cy='50' r='35'/%3E%3C/g%3E%3C/svg%3E"); */
        background-repeat: repeat;
        background-size: 100px 100px;
    }

    .sidebar {
        width: 339px;
        min-width: 339px;
        background: #3aa8b8;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 24px 16px 0;
    }

    .sidebar-logo {
        width: 132px;
        height: 132px;
        object-fit: contain;
        margin-bottom: 18px;
    }

    .sidebar-stack {
        flex: 1;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 12px;
        align-items: center;
    }

    .sidebar-stack img {
        width: 288px;
        height: 168px;
        object-fit: cover;
        border: 2px solid #111;
        display: block;
    }

    .sidebar-foot {
        width: 100%;
        margin-top: auto;
        background: #2d96a6;
        padding: 18px 14px 22px;
        text-align: center;
        color: #fff;
        font-size: 13px;
        line-height: 1.45;
    }

    .sidebar-foot .ar {
        font-size: 15px;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .sidebar-foot .en-mid {
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .sidebar-foot .nusuk {
        margin-top: 8px;
        font-size: 12px;
        opacity: 0.95;
    }

    .main {
        flex: 1;
        position: relative;
        padding: 36px 40px 32px;
        background: #fff;
    }

    .main-pattern {
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.4;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cg fill='none' stroke='%2395c7d6' stroke-width='0.75'%3E%3Cpath d='M50 0 L100 50 L50 100 L0 50 Z'/%3E%3Cpath d='M50 15 L85 50 L50 85 L15 50 Z'/%3E%3C/g%3E%3C/svg%3E");
        background-size: 56px 56px;
    }

    .main-inner {
        position: relative;
        z-index: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .brand-block {
        text-align: center;
        color: #2d96a6;
    }

    .brand-block .ar-brand {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .brand-block .en-brand {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0.06em;
    }

    .stars {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin: 10px 0 8px;
        color: #c9a227;
        font-size: 28px;
        line-height: 1;
    }

    .hotel-title {
        text-align: center;
        font-size: 42px;
        font-weight: 800;
        font-style: italic;
        color: #0a3d6e;
        margin: 0 0 28px;
        letter-spacing: -0.02em;
    }

    .ribbon-zone {
        display: flex;
        justify-content: center;
        margin-bottom: 36px;
    }

    .ribbon {
        position: relative;
        width: 520px;
        text-align: center;
    }

    .ribbon-fold {
        background: linear-gradient(165deg, #2b7fc9 0%, #0d4a8c 45%, #0a3d6e 100%);
        color: #fff;
        font-size: 58px;
        font-weight: 800;
        padding: 28px 20px 52px;
        clip-path: polygon(4% 0, 96% 0, 100% 88%, 50% 100%, 0 88%);
        text-shadow: 0 2px 0 rgba(0, 0, 0, 0.15);
        letter-spacing: 0.04em;
    }

    .ribbon-bar {
        position: absolute;
        left: 50%;
        bottom: 18px;
        transform: translateX(-50%) rotate(-4deg);
        background: #111;
        color: #fff;
        font-size: 28px;
        font-weight: 800;
        padding: 10px 36px;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .dates-row {
        text-align: center;
        font-size: 26px;
        color: #111;
        margin-bottom: auto;
        padding-bottom: 28px;
    }

    .dates-row .lbl {
        font-weight: 800;
        font-style: italic;
    }

    .day-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #ffe600;
        border: 3px solid #1e6bb8;
        font-weight: 800;
        font-style: normal;
        font-size: 26px;
        margin: 0 6px;
        vertical-align: middle;
    }

    .dates-row .month {
        font-weight: 800;
        font-style: italic;
    }

    .divider {
        height: 3px;
        background: #3aa8b8;
        margin: 0 0 22px;
        border: none;
    }

    .contacts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 28px;
        color: #2d96a6;
        font-size: 15px;
        font-weight: 600;
    }

    .contact-line {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .contact-line .loc {
        width: 100%;
        font-size: 12px;
        font-weight: 500;
        opacity: 0.9;
        margin-top: -4px;
        padding-left: 2px;
    }

    .contact-line img {
        width: 18px;
        height: 18px;
        object-fit: contain;
        vertical-align: middle;
    }
</style>
<div class="banner-root">
    <aside class="sidebar">
        <img class="sidebar-logo" src="{{ $sidebarLogo }}" alt="">
        <div class="sidebar-stack">
            <img src="{{ $stack1 }}" alt="">
            <img src="{{ $stack2 }}" alt="">
            <img src="{{ $stack3 }}" alt="">
        </div>
        <div class="sidebar-foot">
            <div class="ar">مجموعة أمجاد المنورة للفنادق</div>
            <div class="en-mid">Women's Gate Northern Region</div>
            <div class="nusuk">Nusuk number : {{ $banner->nusuk_number }}</div>
        </div>
    </aside>
    <div class="main">
        <!-- <div class="main-pattern"></div> -->
        <div class="main-inner">
            <div class="brand-block">
                <div class="ar-brand">فنادق أمجاد المنورة</div>
                <div class="en-brand">AMJAD AL MONAWARA HOTELS</div>
            </div>
            <div class="stars">★★★★</div>
            <h1 class="hotel-title">{{ $banner->hotel_name }}</h1>
            <div class="ribbon-zone">
                <div class="ribbon">
                    <div class="ribbon-fold">LAST</div>
                    <div class="ribbon-bar">Availability</div>
                </div>
            </div>
            <div class="dates-row">
                <span class="lbl">From</span>
                <span class="day-circle">{{ $fromDay }}</span>
                <span class="month">{{ $fromMonth }}</span>
                <span class="lbl"> TO </span>
                <span class="day-circle">{{ $toDay }}</span>
                <span class="month">{{ $toMonth }}</span>
            </div>
        </div>
    </div>
</div>

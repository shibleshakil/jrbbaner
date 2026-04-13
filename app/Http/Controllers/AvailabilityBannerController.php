<?php

namespace App\Http\Controllers;

use App\Models\AvailabilityBanner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Throwable;

class AvailabilityBannerController extends Controller
{
    public function index()
    {
        $banners = AvailabilityBanner::latest()->get();

        return view('availability_banners.index', compact('banners'));
    }

    public function create()
    {
        return view('availability_banners.create', [
            'defaultContacts' => $this->defaultFooterContacts(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
            'hotel_name' => ['required', 'string', 'max:120'],
            'room_rate' => ['required', 'string', 'max:120'],
            'fb' => ['required', 'string', 'max:120'],
            'image_1' => ['nullable', 'image'],
            'image_2' => ['nullable', 'image'],
            'image_3' => ['nullable', 'image'],
            'contacts' => ['required', 'array'],
            'contacts.0.number' => ['required', 'string', 'max:80'],
            'contacts.0.name' => ['required', 'string', 'max:120'],
            'contacts.0.location' => ['required', 'string', 'max:120'],
            'contacts.1.number' => ['required', 'string', 'max:80'],
            'contacts.1.name' => ['required', 'string', 'max:120'],
            'contacts.1.location' => ['required', 'string', 'max:120'],
            'contacts.2.number' => ['nullable', 'string', 'max:80', 'required_with:contacts.2.name'],
            'contacts.2.name' => ['nullable', 'string', 'max:120', 'required_with:contacts.2.number'],
            'contacts.2.location' => ['nullable', 'string', 'max:120'],
        ]);

        $contactInfo = [
            [
                'number' => trim($validated['contacts'][0]['number']),
                'name' => trim($validated['contacts'][0]['name']),
                'location' => trim($validated['contacts'][0]['location']),
            ],
            [
                'number' => trim($validated['contacts'][1]['number']),
                'name' => trim($validated['contacts'][1]['name']),
                'location' => trim($validated['contacts'][1]['location']),
            ],
        ];
        $thirdNumber = isset($validated['contacts'][2]['number']) ? trim((string) $validated['contacts'][2]['number']) : '';
        $thirdName = isset($validated['contacts'][2]['name']) ? trim((string) $validated['contacts'][2]['name']) : '';
        if ($thirdNumber !== '' && $thirdName !== '') {
            $thirdLoc = isset($validated['contacts'][2]['location']) ? trim((string) $validated['contacts'][2]['location']) : '';
            $contactInfo[] = [
                'number' => $thirdNumber,
                'name' => $thirdName,
                'location' => $thirdLoc !== '' ? $thirdLoc : 'Indonesia',
            ];
        }

        if (Carbon::parse($validated['to_date'])->lt(Carbon::parse($validated['from_date']))) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['to_date' => 'The end date must be on or after the start date.']);
        }

        $banner = DB::transaction(function () use ($request, $validated, $contactInfo) {
            $image1Path = $request->file('image_1') ? $request->file('image_1')->store('secondary-banner-assets', 'public') : null;
            $image2Path = $request->file('image_2') ? $request->file('image_2')->store('secondary-banner-assets', 'public') : null;
            $image3Path = $request->file('image_3') ? $request->file('image_3')->store('secondary-banner-assets', 'public') : null;

            return AvailabilityBanner::create([
                'from_date' => $validated['from_date'],
                'to_date' => $validated['to_date'],
                'hotel_name' => $validated['hotel_name'] ?? 'Jiwer Rawda Hotel',
                'image_1_path' => $image1Path,
                'image_2_path' => $image2Path,
                'image_3_path' => $image3Path,
                'contact_info' => $contactInfo,
            ]);
        });

        try {
            $path = $this->saveBannerPng($banner);
            $banner->update(['generated_banner_path' => $path]);
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('availability_banners.index')
                ->with(
                    'error',
                    'Banner was saved but PNG generation failed. Install Node.js, Puppeteer, and Chrome, or set BROWSERSHOT_CHROME_PATH. ' . $e->getMessage()
                );
        }

        return redirect()
            ->route('availability_banners.index')
            ->with('success', 'Secondary banner created successfully.');
    }

    public function show(AvailabilityBanner $availabilityBanner)
    {
        $imageUris = $this->bannerImageDataUris();
        foreach ($imageUris as $uri) {
            if ($uri === null) {
                throw new \RuntimeException('Could not read promotion-assets image files for PNG export.');
            }
        }

        $footerIcons = $this->footerIconDataUris();
        foreach ($footerIcons as $uri) {
            if ($uri === null) {
                throw new \RuntimeException('Could not read footer icon images.');
            }
        }

        return view('availability_banners.banner_export', [
            'banner' => $availabilityBanner,
            'imageUris' => $imageUris,
            'footerIcons' => $footerIcons,
        ]);
    }

    public function preview(AvailabilityBanner $availabilityBanner)
    {
        if (! $availabilityBanner->generated_banner_path || ! Storage::disk('public')->exists($availabilityBanner->generated_banner_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/' . $availabilityBanner->generated_banner_path));
    }

    public function download(AvailabilityBanner $availabilityBanner)
    {
        $relative = $availabilityBanner->generated_banner_path;

        if ($relative && Storage::disk('public')->exists($relative)) {
            return response()->download(
                storage_path('app/public/' . $relative),
                'last-availability-' . $availabilityBanner->id . '.png'
            );
        }

        try {
            $relative = $this->saveBannerPng($availabilityBanner);
            $availabilityBanner->update(['generated_banner_path' => $relative]);
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('availability_banners.index')
                ->with('error', 'Could not generate banner: ' . $e->getMessage());
        }

        return response()->download(
            storage_path('app/public/' . $relative),
            'last-availability-' . $availabilityBanner->id . '.png'
        );
    }

    /**
     * @throws \RuntimeException
     */
    private function saveBannerPng(AvailabilityBanner $banner): string
    {
        $imageUris = $this->bannerImageDataUris();
        foreach ($imageUris as $uri) {
            if ($uri === null) {
                throw new \RuntimeException('Could not read promotion-assets image files for PNG export.');
            }
        }

        $footerIcons = $this->footerIconDataUris();
        foreach ($footerIcons as $uri) {
            if ($uri === null) {
                throw new \RuntimeException('Could not read footer icon images.');
            }
        }

        $html = view('availability_banners.banner_export', [
            'banner' => $banner,
            'imageUris' => $imageUris,
            'footerIcons' => $footerIcons,
        ])->render();

        $relativePath = 'generated-banners/last-availability-' . $banner->id . '.png';
        $absolutePath = storage_path('app/public/' . $relativePath);
        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $browsershot = Browsershot::html($html)
            ->windowSize(1200, 1183)
            ->timeout(120)
            ->waitUntilNetworkIdle();

        $chromePath = env('BROWSERSHOT_CHROME_PATH');
        if (is_string($chromePath) && $chromePath !== '') {
            $browsershot->setChromePath($chromePath);
        }

        $nodeBinary = env('BROWSERSHOT_NODE_PATH');
        if (is_string($nodeBinary) && $nodeBinary !== '') {
            $browsershot->setNodeBinary($nodeBinary);
        }

        $npmBinary = env('BROWSERSHOT_NPM_PATH');
        if (is_string($npmBinary) && $npmBinary !== '') {
            $browsershot->setNpmBinary($npmBinary);
        }

        $browsershot->save($absolutePath);

        return $relativePath;
    }

    /**
     * @return array{sidebar_logo: ?string, stack_1: ?string, stack_2: ?string, stack_3: ?string}
     */
    private function bannerImageDataUris(): array
    {
        return [
            'sidebar_logo' => $this->imageDataUri(public_path('app-assets/images/logo/logo.png')),
            'stack_1' => $this->imageDataUri(public_path('promotion-assets/hotel-1.webp')),
            'stack_2' => $this->imageDataUri(public_path('promotion-assets/room-2.jpg')),
            'stack_3' => $this->imageDataUri(public_path('promotion-assets/room-3.jpg')),
        ];
    }

    /**
     * @return array{whatsapp: ?string, phone: ?string}
     */
    private function footerIconDataUris(): array
    {
        return [
            'whatsapp' => $this->imageDataUri(public_path('promotion-assets/icons/whatsapp.png')),
            'phone' => $this->imageDataUri(public_path('promotion-assets/icons/phone.png')),
        ];
    }

    private function imageDataUri(string $absolutePath): ?string
    {
        if (! is_file($absolutePath)) {
            return null;
        }

        $contents = @file_get_contents($absolutePath);
        if ($contents === false) {
            return null;
        }

        $mime = @mime_content_type($absolutePath) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    /**
     * @return list<array{number: string, name: string, location: string}>
     */
    private function defaultFooterContacts(): array
    {
        return [
            [
                'number' => '+966597709206',
                'name' => 'Sahadath Khan',
                'location' => 'Madinah',
            ],
            [
                'number' => '+966540802329',
                'name' => 'Abdur Rahman (Dhomi)',
                'location' => 'Makkah',
            ],
            [
                'number' => '',
                'name' => '',
                'location' => 'Indonesia',
            ],
        ];
    }
}

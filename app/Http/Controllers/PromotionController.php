<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Throwable;

class PromotionController extends Controller
{
    public function index()
    {
        // TODO: Pagination
        $promotions = Promotion::with('offerDetails')->latest()->paginate(10);

        return view('promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('promotions.create', [
            'defaultContacts' => $this->defaultFooterContacts(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hero_banner' => ['nullable', 'image'],
            'logo' => ['nullable', 'image'],
            'hotel_name' => ['required', 'string', 'max:120'],
            'hotel_year' => ['nullable', 'string', 'max:60'],
            'room_image_1' => ['nullable', 'image'],
            'room_image_2' => ['nullable', 'image'],
            'room_image_3' => ['nullable', 'image'],
            'room_image_4' => ['nullable', 'image'],
            'offers' => ['required', 'array', 'min:1', 'max:5'],
            'offers.*.from_date' => ['required', 'date', 'before:offers.*.to_date'],
            'offers.*.to_date' => ['required', 'date', 'after:offers.*.from_date'],
            'offers.*.double_rate' => ['required', 'integer', 'min:1'],
            'offers.*.triple_rate' => ['required', 'integer', 'min:1'],
            'offers.*.quad_rate' => ['required', 'integer', 'min:1'],
            'offers.*.meals' => ['nullable', 'string', 'max:20'],
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

        $promotion = DB::transaction(function () use ($request, $validated, $contactInfo) {
            $heroPath = $request->file('hero_banner') ? $request->file('hero_banner')->store('promotion-assets', 'public') : null;
            $logoPath = $request->file('logo') ? $request->file('logo')->store('promotion-assets', 'public') : null;
            $roomImage1Path = $request->file('room_image_1') ? $request->file('room_image_1')->store('promotion-assets', 'public') : null;
            $roomImage2Path = $request->file('room_image_2') ? $request->file('room_image_2')->store('promotion-assets', 'public') : null;
            $roomImage3Path = $request->file('room_image_3') ? $request->file('room_image_3')->store('promotion-assets', 'public') : null;
            $roomImage4Path = $request->file('room_image_4') ? $request->file('room_image_4')->store('promotion-assets', 'public') : null;

            $promotion = Promotion::create([
                'hero_banner_path' => $heroPath,
                'logo_path' => $logoPath,
                'hotel_name' => $validated['hotel_name'],
                'hotel_year' => $validated['hotel_year'],
                'room_image_1_path' => $roomImage1Path,
                'room_image_2_path' => $roomImage2Path,
                'room_image_3_path' => $roomImage3Path,
                'room_image_4_path' => $roomImage4Path,
                'contact_info' => $contactInfo,
            ]);

            foreach ($validated['offers'] as $index => $offer) {
                $promotion->offerDetails()->create([
                    'from_date' => $offer['from_date'],
                    'to_date' => $offer['to_date'],
                    'double_rate' => $offer['double_rate'],
                    'triple_rate' => $offer['triple_rate'],
                    'quad_rate' => $offer['quad_rate'],
                    'meals' => $offer['meals'],
                ]);
            }

            return $promotion->load('offerDetails');
        });

        try {
            $generatedPath = $this->savePromotionPngThroughHtmlExport($promotion);
            $promotion->update(['generated_banner_path' => $generatedPath]);
        } catch (Throwable $e) {
            report($e);

            $message = $e->getMessage();
            if (str_contains($message, 'missing') || str_contains($message, 'Could not read')) {
                return redirect()
                    ->route('promotions.index')
                    ->with('error', 'Promotion was saved but PNG generation failed: '.$message);
            }

            return redirect()
                ->route('promotions.index')
                ->with(
                    'error',
                    'Promotion was saved but PNG generation failed. Install Node.js, run npm install (puppeteer), and ensure Chrome is available. Optional: set BROWSERSHOT_CHROME_PATH in .env.'
                );
        }

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion banner created successfully.');
    }

    public function preview(Promotion $promotion)
    {
        if (! $promotion->generated_banner_path || ! Storage::disk('public')->exists($promotion->generated_banner_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/' . $promotion->generated_banner_path));
    }

    public function download(Promotion $promotion)
    {
        $relativePath = $promotion->generated_banner_path;

        if ($relativePath && Storage::disk('public')->exists($relativePath)) {
            return response()->download(
                storage_path('app/public/'.$relativePath),
                'promotion-banner-'.$promotion->id.'.png'
            );
        }

        try {
            $relativePath = $this->savePromotionPngThroughHtmlExport($promotion);
            $promotion->update(['generated_banner_path' => $relativePath]);
        } catch (Throwable $e) {
            report($e);

            $message = $e->getMessage();
            if (str_contains($message, 'missing') || str_contains($message, 'Could not read')) {
                return redirect()
                    ->route('promotions.index')
                    ->with('error', $message);
            }

            return redirect()
                ->route('promotions.index')
                ->with(
                    'error',
                    'Could not generate banner for download. Install Node.js, run npm install (puppeteer), and ensure Chrome is available. Optional: set BROWSERSHOT_CHROME_PATH in .env.'
                );
        }

        return response()->download(
            storage_path('app/public/'.$relativePath),
            'promotion-banner-'.$promotion->id.'.png'
        );
    }

    public function exportPng(Promotion $promotion)
    {
        $promotion->load('offerDetails');

        try {
            $absolutePath = storage_path('app/public/'.$this->savePromotionPngThroughHtmlExport($promotion));
        } catch (Throwable $e) {
            report($e);

            $message = $e->getMessage();
            if (str_contains($message, 'missing') || str_contains($message, 'Could not read')) {
                return redirect()
                    ->route('promotions.index')
                    ->with('error', $message);
            }

            return redirect()
                ->route('promotions.index')
                ->with(
                    'error',
                    'PNG export failed. Install Node.js, run npm install (puppeteer), and ensure Chrome is available. Optional: set BROWSERSHOT_CHROME_PATH in .env.'
                );
        }

        return response()->download(
            $absolutePath,
            'promotion-banner-'.$promotion->id.'.png'
        );
    }

    /**
     * Renders promotions.png_export via Browsershot (same pipeline as export PNG download).
     *
     * @throws \RuntimeException When required images are missing or unreadable
     */
    private function savePromotionPngThroughHtmlExport(Promotion $promotion): string
    {
        $promotion->load('offerDetails');

        $heroPath = $promotion->hero_banner_path && Storage::disk('public')->exists($promotion->hero_banner_path)
            ? storage_path('app/public/'.$promotion->hero_banner_path)
            : public_path('promotion-assets/banner-1s.png');
        $logoPath = $promotion->logo_path && Storage::disk('public')->exists($promotion->logo_path)
            ? storage_path('app/public/'.$promotion->logo_path)
            : public_path('app-assets/images/logo/logo.png');

        $roomImage1Path = $promotion->room_image_1_path && Storage::disk('public')->exists($promotion->room_image_1_path)
            ? storage_path('app/public/'.$promotion->room_image_1_path)
            : public_path('promotion-assets/room-1.jpg');
        $roomImage2Path = $promotion->room_image_2_path && Storage::disk('public')->exists($promotion->room_image_2_path)
            ? storage_path('app/public/'.$promotion->room_image_2_path)
            : public_path('promotion-assets/room-2.jpg');
        $roomImage3Path = $promotion->room_image_3_path && Storage::disk('public')->exists($promotion->room_image_3_path)
            ? storage_path('app/public/'.$promotion->room_image_3_path)
            : public_path('promotion-assets/room-3.jpg');
        $roomImage4Path = $promotion->room_image_4_path && Storage::disk('public')->exists($promotion->room_image_4_path)
            ? storage_path('app/public/'.$promotion->room_image_4_path)
            : public_path('promotion-assets/room-4.jpg');

        $bannerAssets = [
            'hero' => $this->imageDataUri($heroPath),
            'logo' => $this->imageDataUri($logoPath),
            'rooms' => [
                $this->imageDataUri($roomImage1Path),
                $this->imageDataUri($roomImage2Path),
                $this->imageDataUri($roomImage3Path),
                $this->imageDataUri($roomImage4Path),
            ],
        ];

        foreach (array_merge([$bannerAssets['hero'], $bannerAssets['logo']], $bannerAssets['rooms']) as $uri) {
            if ($uri === null) {
                throw new \RuntimeException('Could not read promotion image files for PNG export.');
            }
        }

        $footerIcons = [
            'whatsapp' => $this->imageDataUri(public_path('promotion-assets/icons/whatsapp.png')),
            'phone' => $this->imageDataUri(public_path('promotion-assets/icons/phone.png')),
        ];

        $html = view('promotions.png_export', compact('promotion', 'bannerAssets', 'footerIcons'))->render();

        $relativePath = 'generated-banners/promotion-'.$promotion->id.'-html.png';
        $absolutePath = storage_path('app/public/'.$relativePath);
        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $browsershot = Browsershot::html($html)
            ->windowSize(1130, 1600)
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

        return 'data:'.$mime.';base64,'.base64_encode($contents);
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

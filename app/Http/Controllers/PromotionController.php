<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Services\PromotionBannerRenderer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('offerDetails')->latest()->get();

        return view('promotions.index', compact('promotions'));
    }

    public function store(Request $request, PromotionBannerRenderer $renderer)
    {
        $validated = $request->validate([
            'hero_banner' => ['required', 'image'],
            'logo' => ['required', 'image'],
            'room_image_1' => ['required', 'image'],
            'room_image_2' => ['required', 'image'],
            'room_image_3' => ['required', 'image'],
            'room_image_4' => ['required', 'image'],
            'offers' => ['required', 'array', 'min:1', 'max:5'],
            'offers.*.from_date' => ['required', 'date'],
            'offers.*.to_date' => ['required', 'date'],
            'offers.*.double_rate' => ['required', 'integer', 'min:1'],
            'offers.*.triple_rate' => ['required', 'integer', 'min:1'],
            'offers.*.quad_rate' => ['required', 'integer', 'min:1'],
            'offers.*.meals' => ['required', 'string', 'max:20'],
        ]);

        foreach ($validated['offers'] as $offer) {
            if (Carbon::parse($offer['to_date'])->lt(Carbon::parse($offer['from_date']))) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['The "To" date must be greater than or equal to the "From" date.']);
            }
        }

        $promotion = DB::transaction(function () use ($request, $validated) {
            $heroPath = $request->file('hero_banner')->store('promotion-assets', 'public');
            $logoPath = $request->file('logo')->store('promotion-assets', 'public');
            $roomImage1Path = $request->file('room_image_1')->store('promotion-assets', 'public');
            $roomImage2Path = $request->file('room_image_2')->store('promotion-assets', 'public');
            $roomImage3Path = $request->file('room_image_3')->store('promotion-assets', 'public');
            $roomImage4Path = $request->file('room_image_4')->store('promotion-assets', 'public');

            $promotion = Promotion::create([
                'hero_banner_path' => $heroPath,
                'logo_path' => $logoPath,
                'room_image_1_path' => $roomImage1Path,
                'room_image_2_path' => $roomImage2Path,
                'room_image_3_path' => $roomImage3Path,
                'room_image_4_path' => $roomImage4Path,
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

        $generatedPath = $renderer->render($promotion);
        $promotion->update(['generated_banner_path' => $generatedPath]);

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion banner created successfully.');
    }

    public function download(Promotion $promotion)
    {
        if (! $promotion->generated_banner_path || ! Storage::disk('public')->exists($promotion->generated_banner_path)) {
            return redirect()
                ->route('promotions.index')
                ->with('error', 'Generated banner is missing.');
        }

        return response()->download(
            storage_path('app/public/'.$promotion->generated_banner_path),
            'promotion-banner-'.$promotion->id.'.png'
        );
    }

    public function preview(Promotion $promotion)
    {
        if (! $promotion->generated_banner_path || ! Storage::disk('public')->exists($promotion->generated_banner_path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/'.$promotion->generated_banner_path));
    }

    public function regenerate(Promotion $promotion, PromotionBannerRenderer $renderer)
    {
        $generatedPath = $renderer->render($promotion);
        $promotion->update(['generated_banner_path' => $generatedPath]);

        return response()->download(
            storage_path('app/public/' . $promotion->generated_banner_path),
            'promotion-banner-' . $promotion->id . '.png'
        );

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion banner regenerated successfully.');
    }
}


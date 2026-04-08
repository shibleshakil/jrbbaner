<?php

namespace App\Services;

use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PromotionBannerRenderer
{
    public function render(Promotion $promotion): string
    {
        $width = 1130;
        $height = 1600;

        $canvas = imagecreatetruecolor($width, $height);
        if (! $canvas) {
            throw new RuntimeException('Could not initialize canvas.');
        }

        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        $heroPath = storage_path('app/public/'.$promotion->hero_banner_path);
        $logoPath = storage_path('app/public/'.$promotion->logo_path);
        $roomPaths = [
            storage_path('app/public/'.$promotion->room_image_1_path),
            storage_path('app/public/'.$promotion->room_image_2_path),
            storage_path('app/public/'.$promotion->room_image_3_path),
            storage_path('app/public/'.$promotion->room_image_4_path),
        ];

        $heroImage = $this->imageFromPath($heroPath);
        $logoImage = $this->imageFromPath($logoPath);
        $roomImages = array_map(fn (string $path) => $this->imageFromPath($path), $roomPaths);

        if (! $heroImage || ! $logoImage || in_array(null, $roomImages, true)) {
            throw new RuntimeException('Uploaded images are invalid.');
        }

        imagecopyresampled($canvas, $heroImage, 0, 0, 0, 0, $width, 560, imagesx($heroImage), imagesy($heroImage));

        $panelColor = imagecolorallocatealpha($canvas, 10, 64, 97, 20);
        imagefilledrectangle($canvas, 18, 18, 308, 248, $panelColor);
        imagerectangle($canvas, 18, 18, 308, 248, imagecolorallocate($canvas, 255, 255, 255));

        imagecopyresampled(
            $canvas,
            $logoImage,
            28,
            36,
            0,
            0,
            270,
            195,
            imagesx($logoImage),
            imagesy($logoImage)
        );

        $blue = imagecolorallocate($canvas, 6, 78, 117);
        $dark = imagecolorallocate($canvas, 21, 39, 51);
        $this->drawWatermarkPattern($canvas);
        $this->drawRateBadges($canvas, $blue, $white);
        $this->drawTable($canvas, $promotion, $blue, $dark, $white);
        $this->drawTermsAndConditions($canvas, $blue, $dark, $white);
        $this->drawRoomImages($canvas, $roomImages);
        $this->drawFooter($canvas, $blue, $white);

        $relativePath = 'generated-banners/promotion-'.$promotion->id.'.png';
        $absolutePath = storage_path('app/public/'.$relativePath);
        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        imagepng($canvas, $absolutePath);

        imagedestroy($heroImage);
        imagedestroy($logoImage);
        foreach ($roomImages as $roomImage) {
            imagedestroy($roomImage);
        }
        imagedestroy($canvas);

        return $relativePath;
    }

    private function drawTable($canvas, Promotion $promotion, int $blue, int $dark, int $white): void
    {
        $x = 28;
        $y = 715;
        $headerRow1Height = 44;
        $headerRow2Height = 46;
        $rowHeight = 52;
        $columnWidths = [180, 180, 170, 170, 170, 200]; // From, To, Double, Triple, Quad, Meals
        $tableWidth = array_sum($columnWidths);
        $headerHeight = $headerRow1Height + $headerRow2Height;
        $tableHeight = $headerHeight + ($rowHeight * max(3, $promotion->offerDetails->count()));

        $lineColor = imagecolorallocate($canvas, 18, 109, 145);
        $headerBlue = imagecolorallocate($canvas, 9, 88, 131);

        // Header row 1 blocks: Period(colspan 2), Room Type(colspan 3), Meals(rowspan 2)
        $periodWidth = $columnWidths[0] + $columnWidths[1];
        $roomTypeWidth = $columnWidths[2] + $columnWidths[3] + $columnWidths[4];
        $mealsWidth = $columnWidths[5];

        imagefilledrectangle($canvas, $x, $y, $x + $periodWidth, $y + $headerRow1Height, $headerBlue);
        imagefilledrectangle($canvas, $x + $periodWidth, $y, $x + $periodWidth + $roomTypeWidth, $y + $headerRow1Height, $headerBlue);
        imagefilledrectangle($canvas, $x + $periodWidth + $roomTypeWidth, $y, $x + $tableWidth, $y + $headerHeight, $headerBlue);

        imagerectangle($canvas, $x, $y, $x + $periodWidth, $y + $headerRow1Height, $white);
        imagerectangle($canvas, $x + $periodWidth, $y, $x + $periodWidth + $roomTypeWidth, $y + $headerRow1Height, $white);
        imagerectangle($canvas, $x + $periodWidth + $roomTypeWidth, $y, $x + $tableWidth, $y + $headerHeight, $white);

        $this->drawText($canvas, $x + 128, $y + 14, 'Period', 5, $white);
        $this->drawText($canvas, $x + $periodWidth + 168, $y + 14, 'Room Type', 5, $white);
        // Simulate rotated header text using vertical text in rowspan area
        imagestringup($canvas, 4, $x + $tableWidth - 72, $y + 78, 'Meals', $white);

        // Header row 2 cells
        $headers = ['From', 'To', 'Double', 'Triple', 'Quad'];
        $cursorX = $x;
        for ($index = 0; $index < 5; $index++) {
            $columnWidth = $columnWidths[$index];
            imagefilledrectangle($canvas, $cursorX, $y + $headerRow1Height, $cursorX + $columnWidth, $y + $headerHeight, $headerBlue);
            imagerectangle($canvas, $cursorX, $y + $headerRow1Height, $cursorX + $columnWidth, $y + $headerHeight, $white);
            $this->drawText($canvas, $cursorX + 18, $y + $headerRow1Height + 14, $headers[$index], 5, $white);
            $this->drawText($canvas, $cursorX + 19, $y + $headerRow1Height + 14, $headers[$index], 5, $white);
            $cursorX += $columnWidth;
        }

        $offers = $promotion->offerDetails;
        foreach ($offers as $index => $offer) {
            $rowTop = $y + $headerHeight + ($index * $rowHeight);
            $cursorX = $x;
            $values = [
                Carbon::parse($offer->from_date)->format('d-M-y'),
                Carbon::parse($offer->to_date)->format('d-M-y'),
                (string) $offer->double_rate,
                (string) $offer->triple_rate,
                (string) $offer->quad_rate,
                $offer->meals,
            ];

            foreach ($columnWidths as $valueIndex => $columnWidth) {
                imagerectangle($canvas, $cursorX, $rowTop, $cursorX + $columnWidth, $rowTop + $rowHeight, $lineColor);
                $this->drawText($canvas, $cursorX + 16, $rowTop + 18, $values[$valueIndex], 5, $dark);
                $this->drawText($canvas, $cursorX + 17, $rowTop + 18, $values[$valueIndex], 5, $dark);
                $cursorX += $columnWidth;
            }
        }

        // Empty rows up to 3 for stable layout
        $existingRows = $offers->count();
        for ($i = $existingRows; $i < 3; $i++) {
            $rowTop = $y + $headerHeight + ($i * $rowHeight);
            $cursorX = $x;
            foreach ($columnWidths as $columnWidth) {
                imagerectangle($canvas, $cursorX, $rowTop, $cursorX + $columnWidth, $rowTop + $rowHeight, $lineColor);
                $cursorX += $columnWidth;
            }
        }
    }

    private function drawTermsAndConditions($canvas, int $blue, int $dark, int $white): void
    {
        imagefilledrectangle($canvas, 28, 984, 420, 1038, $blue);
        $this->drawText($canvas, 44, 1002, 'Terms & Conditions', 5, $white);

        $terms = [
            'The above rates are net & non commissionable and quoted in Saudi Riyals.',
            'VAT & Municipality fees are included in the rate.',
            'Check in 16:00 hrs and check out 12:00 hrs. One night charged if check out after 16:00 hrs.',
            'Booking confirmed only upon receipt of 50% advance and full payment before guest arrival.',
            'Cancellation and amendment are according to confirmation letter terms.',
            'Triple and quad occupancy will be through extra bed if standard room is not available.',
            'Rates are subject to change without prior notice.',
        ];

        $termColor = imagecolorallocate($canvas, 28, 49, 63);
        $startY = 1062;
        foreach ($terms as $term) {
            $this->drawText($canvas, 32, $startY, '* '.$term, 4, $termColor);
            $this->drawText($canvas, 33, $startY, '* '.$term, 4, $termColor);
            $startY += 28;
        }
    }

    private function drawFooter($canvas, int $blue, int $white): void
    {
        $yellow = imagecolorallocate($canvas, 255, 227, 92);
        $green = imagecolorallocate($canvas, 52, 199, 89);
        $dark = imagecolorallocate($canvas, 37, 48, 59);
        imagefilledrectangle($canvas, 0, 1490, 1130, 1600, $blue);
        $this->drawText($canvas, 30, 1504, 'For booking & inquiries please contact us using the following:', 4, $white);

        $whatsappIcon = $this->imageFromPath(public_path('promotion-assets/icons/whatsapp.png'));
        $phoneIcon = $this->imageFromPath(public_path('promotion-assets/icons/phone.png'));

        $sections = [
            ['+966597709206', 'Sahadath Khan', 'Madinah'],
            ['+966540802329', 'Abdur Rahman (Dhomi)', 'Makkah'],
            ['+966597709206', 'Sahadath Khan', 'Indonesia Office'],
        ];

        $startX = 34;
        foreach ($sections as $section) {
            if ($whatsappIcon) {
                imagecopyresampled($canvas, $whatsappIcon, $startX - 8, 1519, 0, 0, 18, 18, imagesx($whatsappIcon), imagesy($whatsappIcon));
            } else {
                imagefilledellipse($canvas, $startX, 1530, 18, 18, $green);
                $this->drawText($canvas, $startX - 4, 1523, 'W', 2, $white);
            }

            if ($phoneIcon) {
                imagecopyresampled($canvas, $phoneIcon, $startX + 16, 1519, 0, 0, 18, 18, imagesx($phoneIcon), imagesy($phoneIcon));
            } else {
                imagefilledellipse($canvas, $startX + 24, 1530, 18, 18, $yellow);
                $this->drawText($canvas, $startX + 20, 1523, 'P', 2, $dark);
            }

            $this->drawText($canvas, $startX + 40, 1518, $section[0], 5, $yellow);
            $nameColor = imagecolorallocate($canvas, 228, 239, 247);
            $this->drawText($canvas, $startX + 40, 1548, $section[1], 5, $nameColor);
            $this->drawText($canvas, $startX + 40, 1574, $section[2], 5, $nameColor);
            $startX += 360;
        }

        if ($whatsappIcon) {
            imagedestroy($whatsappIcon);
        }
        if ($phoneIcon) {
            imagedestroy($phoneIcon);
        }
    }

    private function drawRoomImages($canvas, array $roomImages): void
    {
        $x = 26;
        $y = 1330;
        $imageWidth = 258;
        $imageHeight = 126;
        $gap = 16;
        $borderColor = imagecolorallocate($canvas, 8, 191, 223);

        foreach ($roomImages as $index => $roomImage) {
            $currentX = $x + (($imageWidth + $gap) * $index);

            $this->copyCoverRoundedImage($canvas, $roomImage, $currentX, $y, $imageWidth, $imageHeight, 26);

            imagesetthickness($canvas, 3);
            // Softer dashed cyan frame around rounded corners
            $style = [$borderColor, $borderColor, $borderColor, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT];
            imagesetstyle($canvas, $style);
            imageline($canvas, $currentX, $y, $currentX + $imageWidth, $y, IMG_COLOR_STYLED);
            imageline($canvas, $currentX, $y + $imageHeight, $currentX + $imageWidth, $y + $imageHeight, IMG_COLOR_STYLED);
            imageline($canvas, $currentX, $y, $currentX, $y + $imageHeight, IMG_COLOR_STYLED);
            imageline($canvas, $currentX + $imageWidth, $y, $currentX + $imageWidth, $y + $imageHeight, IMG_COLOR_STYLED);
            imagesetthickness($canvas, 1);
        }
    }

    private function drawWatermarkPattern($canvas): void
    {
        $lineColor = imagecolorallocatealpha($canvas, 74, 182, 210, 110);
        $width = imagesx($canvas);
        $radius = 20;
        $space = 20;

        for ($y = 600; $y <= 1600; $y += $space) {
            for ($x = 0; $x <= $width; $x += $space) {
                imagearc($canvas, $x, $y, $radius * 2, $radius * 2, 0, 360, $lineColor);
            }
        }
    }

    private function drawRateBadges($canvas, int $blue, int $white): void
    {
        $yellow = imagecolorallocate($canvas, 230, 211, 44);
        $yearBlue = imagecolorallocate($canvas, 19, 108, 160);

        imagefilledrectangle($canvas, 40, 586, 650, 648, $blue);
        $this->drawText($canvas, 72, 603, '( Fareast Rate )', 5, $white);
        $this->drawText($canvas, 72, 626, 'Jiwar Al SaAha Hotel', 5, $yellow);

        imagefilledrectangle($canvas, 700, 586, 850, 648, $yearBlue);
        $this->drawText($canvas, 756, 609, Carbon::now()->format('Y'), 5, $white);
    }

    private function copyCoverRoundedImage($canvas, $source, int $dstX, int $dstY, int $dstW, int $dstH, int $radius): void
    {
        $temp = imagecreatetruecolor($dstW, $dstH);
        imagealphablending($temp, false);
        imagesavealpha($temp, true);
        $transparent = imagecolorallocatealpha($temp, 0, 0, 0, 127);
        imagefill($temp, 0, 0, $transparent);

        $srcW = imagesx($source);
        $srcH = imagesy($source);
        $srcRatio = $srcW / $srcH;
        $dstRatio = $dstW / $dstH;

        if ($srcRatio > $dstRatio) {
            $cropH = $srcH;
            $cropW = (int) round($srcH * $dstRatio);
            $cropX = (int) round(($srcW - $cropW) / 2);
            $cropY = 0;
        } else {
            $cropW = $srcW;
            $cropH = (int) round($srcW / $dstRatio);
            $cropX = 0;
            $cropY = (int) round(($srcH - $cropH) / 2);
        }

        imagecopyresampled($temp, $source, 0, 0, $cropX, $cropY, $dstW, $dstH, $cropW, $cropH);

        for ($y = 0; $y < $dstH; $y++) {
            for ($x = 0; $x < $dstW; $x++) {
                $inCorner = false;

                if ($x < $radius && $y < $radius) {
                    $dx = $radius - $x;
                    $dy = $radius - $y;
                    $inCorner = (($dx * $dx) + ($dy * $dy)) > ($radius * $radius);
                } elseif ($x >= ($dstW - $radius) && $y < $radius) {
                    $dx = $x - ($dstW - $radius - 1);
                    $dy = $radius - $y;
                    $inCorner = (($dx * $dx) + ($dy * $dy)) > ($radius * $radius);
                } elseif ($x < $radius && $y >= ($dstH - $radius)) {
                    $dx = $radius - $x;
                    $dy = $y - ($dstH - $radius - 1);
                    $inCorner = (($dx * $dx) + ($dy * $dy)) > ($radius * $radius);
                } elseif ($x >= ($dstW - $radius) && $y >= ($dstH - $radius)) {
                    $dx = $x - ($dstW - $radius - 1);
                    $dy = $y - ($dstH - $radius - 1);
                    $inCorner = (($dx * $dx) + ($dy * $dy)) > ($radius * $radius);
                }

                if ($inCorner) {
                    imagesetpixel($temp, $x, $y, $transparent);
                }
            }
        }

        imagealphablending($canvas, true);
        imagecopy($canvas, $temp, $dstX, $dstY, 0, 0, $dstW, $dstH);
        imagedestroy($temp);
    }

    private function drawText($image, int $x, int $y, string $text, int $font, int $color): void
    {
        imagestring($image, $font, $x, $y, $text, $color);
    }

    private function imageFromPath(string $path)
    {
        if (! file_exists($path)) {
            return null;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            return null;
        }

        return imagecreatefromstring($content);
    }
}


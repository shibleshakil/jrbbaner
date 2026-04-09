<?php

namespace App\Services;

use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PromotionBannerRenderer
{
    private ?string $fontPathCache = null;

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
        imagefilledrectangle($canvas, 18, 0, 308, 280, $panelColor);
        imagerectangle($canvas, 18, 0, 308, 280, 0);

        imagecopyresampled(
            $canvas,
            $logoImage,
            28,
            56,
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
        $headerBlue = imagecolorallocate($canvas, 255, 255, 255);

        // Header row 1 blocks: Period(colspan 2), Room Type(colspan 3), Meals(rowspan 2)
        $periodWidth = $columnWidths[0] + $columnWidths[1];
        $roomTypeWidth = $columnWidths[2] + $columnWidths[3] + $columnWidths[4];
        $mealsWidth = $columnWidths[5];

        imagerectangle($canvas, $x, $y, $x + $periodWidth, $y + $headerRow1Height, $dark);
        imagerectangle($canvas, $x + $periodWidth, $y, $x + $periodWidth + $roomTypeWidth, $y + $headerRow1Height, $dark);
        imagerectangle($canvas, $x + $periodWidth + $roomTypeWidth, $y, $x + $tableWidth, $y + $headerHeight, $dark);

        $this->drawCenteredTextInRect($canvas, 'Period', $x, $y, $periodWidth, $headerRow1Height, 5, $dark, true);
        $this->drawCenteredTextInRect($canvas, 'Room Type', $x + $periodWidth, $y, $roomTypeWidth, $headerRow1Height, 5, $dark, true);
        // Simulate rotated header text using vertical text in rowspan area
        $fontPath = $this->resolveFontPath();
        if ($fontPath) {
            imagettftext($canvas, 18, 45, $x + $tableWidth - 110, $y + 70, $dark, $fontPath, 'Meals');
            imagettftext($canvas, 18, 45, $x + $tableWidth - 109, $y + 70, $dark, $fontPath, 'Meals');
            imagettftext($canvas, 18, 45, $x + $tableWidth - 108, $y + 70, $dark, $fontPath, 'Meals');
        } else {
            $this->drawText($canvas, $x + $tableWidth - 86, $y + 20, 'Meals', 5, $dark);
            $this->drawText($canvas, $x + $tableWidth - 85, $y + 20, 'Meals', 5, $dark);
            $this->drawText($canvas, $x + $tableWidth - 84, $y + 20, 'Meals', 5, $dark);
        }

        // Header row 2 cells
        $headers = ['From', 'To', 'Double', 'Triple', 'Quad'];
        $cursorX = $x;
        for ($index = 0; $index < 5; $index++) {
            $columnWidth = $columnWidths[$index];
            imagerectangle($canvas, $cursorX, $y + $headerRow1Height, $cursorX + $columnWidth, $y + $headerHeight, $dark);
            $this->drawCenteredTextInRect(
                $canvas,
                $headers[$index],
                $cursorX,
                $y + $headerRow1Height,
                $columnWidth,
                $headerRow2Height,
                5,
                $dark,
                true
            );
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
                imagerectangle($canvas, $cursorX, $rowTop, $cursorX + $columnWidth, $rowTop + $rowHeight, $dark);
                $this->drawCenteredTextInRect(
                    $canvas,
                    $values[$valueIndex],
                    $cursorX,
                    $rowTop,
                    $columnWidth,
                    $rowHeight,
                    5,
                    $dark,
                    true
                );
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
        $this->drawRightRoundedFilledRect($canvas, 0, 984, 280, 1038, 18, $blue);
        $this->drawText($canvas, 44, 1002, 'Terms & Conditions', 5, $white);
        $this->drawText($canvas, 44, 1001, 'Terms & Conditions', 5, $white);

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
            $this->drawText($canvas, 32, $startY, '* '.$term, 5, $termColor);
            $this->drawText($canvas, 32, $startY + 1, '* '.$term, 5, $termColor);
            $this->drawText($canvas, 33, $startY, '* '.$term, 5, $termColor);
            $startY += 30;
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

        $lineNumberY = 1526;
        $lineNameY = 1551;
        $lineLocationY = 1573;
        $startX = 40;
        foreach ($sections as $section) {
            if ($whatsappIcon) {
                imagecopyresampled($canvas, $whatsappIcon, $startX - 8, $lineNumberY - 1, 0, 0, 18, 18, imagesx($whatsappIcon), imagesy($whatsappIcon));
            } else {
                imagefilledellipse($canvas, $startX, $lineNumberY + 8, 18, 18, $green);
                $this->drawText($canvas, $startX - 4, $lineNumberY + 1, 'W', 2, $white);
            }

            if ($phoneIcon) {
                imagecopyresampled($canvas, $phoneIcon, $startX + 16, $lineNumberY - 1, 0, 0, 18, 18, imagesx($phoneIcon), imagesy($phoneIcon));
            } else {
                imagefilledellipse($canvas, $startX + 24, $lineNumberY + 8, 18, 18, $yellow);
                $this->drawText($canvas, $startX + 20, $lineNumberY + 1, 'P', 2, $dark);
            }

            $this->drawText($canvas, $startX + 40, $lineNumberY, $section[0], 5, $yellow);
            $nameColor = imagecolorallocate($canvas, 228, 239, 247);
            $this->drawText($canvas, $startX - 4, $lineNameY, $section[1], 5, $nameColor);
            $this->drawText($canvas, $startX - 4, $lineLocationY, $section[2], 5, $nameColor);
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
        $y = 1300;
        $imageWidth = 258;
        $imageHeight = 160;
        $gap = 16;
        $borderColor = imagecolorallocate($canvas, 8, 191, 223);
        $cardRadius = 26;
        $padding = 0;

        foreach ($roomImages as $index => $roomImage) {
            $currentX = $x + (($imageWidth + $gap) * $index);
            $alternate = ($index % 2) === 0;
            $roundTopLeft = $alternate;
            $roundTopRight = ! $alternate;
            $roundBottomRight = $alternate;
            $roundBottomLeft = ! $alternate;

            // Round the whole container card and then place the image inside it.
            $this->copyCoverRoundedImage(
                $canvas,
                $roomImage,
                $currentX + $padding,
                $y + $padding,
                $imageWidth - ($padding * 2),
                $imageHeight - ($padding * 2),
                max(4, $cardRadius - $padding),
                $roundTopLeft,
                $roundTopRight,
                $roundBottomRight,
                $roundBottomLeft
            );

            $this->drawRoundedRectBorder(
                $canvas,
                $currentX,
                $y,
                $imageWidth,
                $imageHeight,
                $cardRadius,
                $borderColor,
                3,
                $roundTopLeft,
                $roundTopRight,
                $roundBottomRight,
                $roundBottomLeft
            );
        }
    }

    private function drawRoundedRectBorder(
        $canvas,
        int $x,
        int $y,
        int $w,
        int $h,
        int $r,
        int $color,
        int $thickness = 1,
        bool $roundTopLeft = true,
        bool $roundTopRight = true,
        bool $roundBottomRight = true,
        bool $roundBottomLeft = true
    ): void
    {
        // Use inclusive max bounds to avoid 1px overflow/missing seams.
        $x2 = $x + $w - 1;
        $y2 = $y + $h - 1;

        imagesetthickness($canvas, $thickness);
        $style = [$color, $color, $color, IMG_COLOR_TRANSPARENT, IMG_COLOR_TRANSPARENT];
        imagesetstyle($canvas, $style);

        $topLeftOffset = $roundTopLeft ? $r : 0;
        $topRightOffset = $roundTopRight ? $r : 0;
        $bottomRightOffset = $roundBottomRight ? $r : 0;
        $bottomLeftOffset = $roundBottomLeft ? $r : 0;

        // Straight edges
        imageline($canvas, $x + $topLeftOffset, $y, $x2 - $topRightOffset, $y, IMG_COLOR_STYLED);
        imageline($canvas, $x + $bottomLeftOffset, $y2, $x2 - $bottomRightOffset, $y2, IMG_COLOR_STYLED);
        imageline($canvas, $x, $y + $topLeftOffset, $x, $y2 - $bottomLeftOffset, IMG_COLOR_STYLED);
        imageline($canvas, $x2, $y + $topRightOffset, $x2, $y2 - $bottomRightOffset, IMG_COLOR_STYLED);

        // Rounded corner arcs (dashed)
        if ($roundTopLeft) {
            $this->drawDashedArc($canvas, $x + $r, $y + $r, $r, 180, 270, $color);
        }
        if ($roundTopRight) {
            $this->drawDashedArc($canvas, $x2 - $r, $y + $r, $r, 270, 360, $color);
        }
        if ($roundBottomLeft) {
            $this->drawDashedArc($canvas, $x + $r, $y2 - $r, $r, 90, 180, $color);
        }
        if ($roundBottomRight) {
            $this->drawDashedArc($canvas, $x2 - $r, $y2 - $r, $r, 0, 90, $color);
        }

        imagesetthickness($canvas, 1);
    }

    private function drawDashedArc($canvas, int $cx, int $cy, int $radius, int $startDeg, int $endDeg, int $color): void
    {
        $dashDegrees = 8;
        $gapDegrees = 6;

        for ($deg = $startDeg; $deg < $endDeg; $deg += ($dashDegrees + $gapDegrees)) {
            $segStart = $deg;
            $segEnd = min($deg + $dashDegrees, $endDeg);

            $x1 = (int) round($cx + ($radius * cos(deg2rad($segStart))));
            $y1 = (int) round($cy + ($radius * sin(deg2rad($segStart))));
            $x2 = (int) round($cx + ($radius * cos(deg2rad($segEnd))));
            $y2 = (int) round($cy + ($radius * sin(deg2rad($segEnd))));

            imageline($canvas, $x1, $y1, $x2, $y2, $color);
        }
    }

    private function drawRightRoundedFilledRect($canvas, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        $radius = max(1, min($radius, (int) floor(($y2 - $y1) / 2), (int) floor(($x2 - $x1) / 2)));

        imagefilledrectangle($canvas, $x1, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($canvas, $x2 - $radius, $y1 + $radius, $x2, $y2 - $radius, $color);
        imagefilledellipse($canvas, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($canvas, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    private function drawWatermarkPattern($canvas): void
    {
        $lineColor = imagecolorallocatealpha($canvas, 74, 182, 210, 120);
        $width = imagesx($canvas);
        $radius = 20;
        $space = 25;

        for ($y = 600; $y <= 1600; $y += $space) {
            for ($x = 0; $x <= $width; $x += $space) {
                imagearc($canvas, $x, $y, $radius * 2, $radius * 2, 0, 360, $lineColor);
            }
        }
    }

    private function drawRateBadges($canvas, int $blue, int $white): void
    {
        $yellow = imagecolorallocate($canvas, 230, 211, 44);
        $yearBlue = imagecolorallocate($canvas, 18, 114, 168);
        $darkBlue = imagecolorallocate($canvas, 8, 71, 110);

        $yTop = 582;
        $yBot = 652;
        $skew = 26;
        $marginLeft = 20;
        $padTextX = 22;
        $padTextY = 10;
        $lineGap = 5;
        $iconW = 64;
        $badgeGap = 32;
        $badgeInnerPadX = 22;
        $yearBadgeInnerPadX = 22;

        $line1 = '( Fareast Rate )';
        $line2 = 'Jiwar Al SaAha Hotel';
        // +1 accounts for bold second pass at x+1 when centering
        $vw1 = $this->measureTextWidth($line1, 5) + 1;
        $vw2 = $this->measureTextWidth($line2, 5) + 1;
        $textBlockW = max($vw1, $vw2) + ($padTextX * 2);
        $lineH = $this->measureTextHeight(5);
        $textStackH = ($lineH * 2) + $lineGap;
        $badgeH = $yBot - $yTop;
        $textOffsetY = (int) max(0, floor(($badgeH - $textStackH) / 2)) + (int) floor($padTextY / 2) - 4;

        $x0 = $marginLeft;
        $contentWidth = $iconW + $textBlockW;
        $topRight = $x0 + ($badgeInnerPadX * 2) + $contentWidth;
        $contentLeft = $x0 + $badgeInnerPadX;

        imagefilledpolygon($canvas, [
            $x0, $yTop,
            $topRight, $yTop,
            $topRight - $skew, $yBot,
            $x0 - $skew, $yBot,
        ], 4, $blue);

        $iconRightTop = $contentLeft + $iconW;
        imagefilledpolygon($canvas, [
            $contentLeft, $yTop,
            $iconRightTop, $yTop,
            $iconRightTop - $skew, $yBot,
            $contentLeft - $skew, $yBot,
        ], 4, $darkBlue);

        $iconGraphicW = 20;
        $ix = $contentLeft + (int) floor(($iconW - $iconGraphicW) / 2);
        $iyMid = (int) round($yTop + ($badgeH / 2));
        imagefilledrectangle($canvas, $ix, $iyMid - 22, $ix + 20, $iyMid + 18, $white);
        $window = imagecolorallocate($canvas, 8, 71, 110);
        for ($r = 0; $r < 3; $r++) {
            for ($c = 0; $c < 2; $c++) {
                $wx = $ix + 4 + ($c * 8);
                $wy = $iyMid - 17 + ($r * 11);
                imagefilledrectangle($canvas, $wx, $wy, $wx + 3, $wy + 4, $window);
            }
        }
        imagefilledrectangle($canvas, $ix + 8, $iyMid + 11, $ix + 12, $iyMid + 18, $window);

        $textColLeft = $iconRightTop;
        $textX1 = $textColLeft + (int) max(0, floor(($textBlockW - $vw1) / 2));
        $textX2 = $textColLeft + (int) max(0, floor(($textBlockW - $vw2) / 2));
        $yLine1 = $yTop + $textOffsetY;
        $yLine2 = $yLine1 + $lineH + $lineGap;
        $this->drawText($canvas, $textX1, $yLine1, $line1, 5, $white);
        $this->drawText($canvas, $textX1 + 1, $yLine1, $line1, 5, $white);
        $this->drawText($canvas, $textX2, $yLine2, $line2, 5, $yellow);
        $this->drawText($canvas, $textX2 + 1, $yLine2, $line2, 5, $yellow);

        $yearLabel = Carbon::now()->format('Y');
        $yearPadX = 28;
        $vwYear = $this->measureTextWidth($yearLabel, 5) + 1;
        $yearContentInnerW = $vwYear + ($yearPadX * 2);
        $yearW = $yearContentInnerW + ($yearBadgeInnerPadX * 2);
        $yearX0 = $topRight + $badgeGap;

        imagefilledpolygon($canvas, [
            $yearX0, $yTop,
            $yearX0 + $yearW, $yTop,
            $yearX0 + $yearW - $skew, $yBot,
            $yearX0 - $skew, $yBot,
        ], 4, $yearBlue);

        $yearTextX = $yearX0 + $yearBadgeInnerPadX + (int) max(0, floor(($yearContentInnerW - $vwYear) / 2));
        $yearTextY = $yTop + (int) max(0, floor(($badgeH - $lineH) / 2)) + 1;
        $this->drawText($canvas, $yearTextX - 10, $yearTextY, $yearLabel, 5, $white);
        $this->drawText($canvas, $yearTextX - 9, $yearTextY, $yearLabel, 5, $white);
    }

    private function copyCoverRoundedImage(
        $canvas,
        $source,
        int $dstX,
        int $dstY,
        int $dstW,
        int $dstH,
        int $radius,
        bool $roundTopLeft = true,
        bool $roundTopRight = true,
        bool $roundBottomRight = true,
        bool $roundBottomLeft = true
    ): void
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

                if ($roundTopLeft && $x < $radius && $y < $radius) {
                    $dx = $radius - $x;
                    $dy = $radius - $y;
                    $inCorner = (($dx * $dx) + ($dy * $dy)) > ($radius * $radius);
                } elseif ($roundTopRight && $x >= ($dstW - $radius) && $y < $radius) {
                    $dx = $x - ($dstW - $radius - 1);
                    $dy = $radius - $y;
                    $inCorner = (($dx * $dx) + ($dy * $dy)) > ($radius * $radius);
                } elseif ($roundBottomLeft && $x < $radius && $y >= ($dstH - $radius)) {
                    $dx = $radius - $x;
                    $dy = $y - ($dstH - $radius - 1);
                    $inCorner = (($dx * $dx) + ($dy * $dy)) > ($radius * $radius);
                } elseif ($roundBottomRight && $x >= ($dstW - $radius) && $y >= ($dstH - $radius)) {
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
        $fontPath = $this->resolveFontPath();
        if (! $fontPath) {
            imagestring($image, $font, $x, $y, $text, $color);
            return;
        }

        $sizeMap = [
            1 => 9.0,
            2 => 10.0,
            3 => 11.5,
            4 => 13.0,
            5 => 18.0,
        ];
        $size = $sizeMap[$font] ?? 12.0;
        $baselineY = (int) round($y + $size);
        imagettftext($image, $size, 0, $x, $baselineY, $color, $fontPath, $text);
    }

    private function drawCenteredTextInRect(
        $image,
        string $text,
        int $x,
        int $y,
        int $width,
        int $height,
        int $font,
        int $color,
        bool $bold = false
    ): void {
        $textWidth = $this->measureTextWidth($text, $font);
        $textHeight = $this->measureTextHeight($font);
        $drawX = $x + (int) max(0, floor(($width - $textWidth) / 2));
        $drawY = $y + (int) max(0, floor(($height - $textHeight) / 2));
        if ($bold) {
            $this->drawText($image, $drawX, $drawY, $text, $font, $color);
            $this->drawText($image, $drawX + 1, $drawY, $text, $font, $color);
            $this->drawText($image, $drawX, $drawY + 1, $text, $font, $color);
            return;
        }

        $this->drawText($image, $drawX, $drawY, $text, $font, $color);
    }

    private function measureTextWidth(string $text, int $font): int
    {
        $fontPath = $this->resolveFontPath();
        if (! $fontPath) {
            return imagefontwidth($font) * strlen($text);
        }

        $sizeMap = [1 => 9.0, 2 => 10.0, 3 => 11.5, 4 => 13.0, 5 => 18.0];
        $size = $sizeMap[$font] ?? 12.0;
        $bbox = imagettfbbox($size, 0, $fontPath, $text);
        if (! is_array($bbox)) {
            return imagefontwidth($font) * strlen($text);
        }

        return (int) abs($bbox[2] - $bbox[0]);
    }

    private function measureTextHeight(int $font): int
    {
        $fontPath = $this->resolveFontPath();
        if (! $fontPath) {
            return imagefontheight($font);
        }

        $sizeMap = [1 => 9.0, 2 => 10.0, 3 => 11.5, 4 => 13.0, 5 => 18.0];
        $size = $sizeMap[$font] ?? 12.0;
        $bbox = imagettfbbox($size, 0, $fontPath, 'Ag');
        if (! is_array($bbox)) {
            return imagefontheight($font);
        }

        return (int) abs($bbox[1] - $bbox[7]);
    }

    private function resolveFontPath(): ?string
    {
        if ($this->fontPathCache !== null) {
            return $this->fontPathCache;
        }

        $candidates = [
            public_path('fonts/Roboto-Regular.ttf'),
            public_path('fonts/OpenSans-Regular.ttf'),
            public_path('fonts/DejaVuSans.ttf'),
            'C:\\Windows\\Fonts\\arial.ttf',
            'C:\\Windows\\Fonts\\calibri.ttf',
            'C:\\Windows\\Fonts\\segoeui.ttf',
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && file_exists($candidate)) {
                $this->fontPathCache = $candidate;
                return $this->fontPathCache;
            }
        }

        $this->fontPathCache = '';
        return null;
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


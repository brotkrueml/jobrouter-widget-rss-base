<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Widget RSS Base library.
 *
 * Copyright (c) 2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 * @see https://github.com/brotkrueml/jobrouter-widget-rss-base
 */

namespace Brotkrueml\JobRouterWidgetRssBase\Asset;

class ImageCache
{
    public function getUrl(string $remoteImageUrl, string $cacheDir): ?string
    {
        $imageParts = \explode('/', $remoteImageUrl);
        $imageName = \end($imageParts);
        $localImagePath = $cacheDir . DIRECTORY_SEPARATOR . $imageName;

        if (!\is_file($localImagePath)) {
            if (!\is_dir($cacheDir)) {
                \mkdir($cacheDir, 0777, true);
            }

            $image = \file_get_contents($remoteImageUrl);
            if ($image !== false) {
                if (\file_put_contents($localImagePath, $image) === false) {
                    $localImagePath = null;
                }
            }
        }

        return $localImagePath ? $imageName : null;
    }
}

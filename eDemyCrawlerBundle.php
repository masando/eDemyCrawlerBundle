<?php

namespace eDemy\CrawlerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class eDemyCrawlerBundle extends Bundle
{
    public static function getBundleName($type = null)
    {
        if ($type == null) {

            return 'eDemyCrawlerBundle';
        } else {
            if ($type == 'Simple') {

                return 'Crawler';
            } else {
                if ($type == 'simple') {

                    return 'crawler';
                }
            }
        }
    }

    public static function eDemyBundle() {

        return true;
    }
}

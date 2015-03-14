<?php
namespace Kata;

/**
 * Class GildedRose
 * @package Kata
 */
class GildedRose {

    const BRIE = "Aged Brie";
    const PASS = "Backstage passes to a TAFKAL80ETC concert";
    const SULFURAS = "Sulfuras, Hand of Ragnaros";
    const DOUBLE_INCREMENT_THRESHOLD = 10;
    const TRIPLE_INCREMENT_THRESHOLD = 5;
    const SELLIN_GRANULARITY = 1;
    const QUALITY_GRANULARITY = 1;
    const MINIMUM_QUALITY = 0;
    const MAXIMUM_QUALITY = 50;
    const MINIMUN_SELLIN = 0;

    public static function updateQuality($items) {
        for ($i = 0; $i < count($items); $i++) {
            $currentItem = $items[$i];
            $name = $currentItem->getName();
            $notBrie = self::BRIE != $name;
            $notPass = self::PASS != $name;
            $isSulfuras = self::SULFURAS == $name;
            $norBrieNeitherPass = ($notBrie) && ($notPass);
            $isPass = self::PASS == $name;
            $isBrie = self::BRIE == $name;

            if ($norBrieNeitherPass) {
                self::decreaseQuality($isSulfuras, $currentItem);
            } else {
                self::increaseQuality($currentItem);
                if ($isPass) {
                    if (self::isInDoubleIncrement($currentItem)) {
                        self::increaseQuality($currentItem);
                    }
                    if (self::isTripleIncrement($currentItem)) {
                        self::increaseQuality($currentItem);
                    }
                }
            }
            self::updateSellIn($isSulfuras, $currentItem);
            if (self::isExpired($currentItem)) {
                if ($norBrieNeitherPass) {
                    self::decreaseQuality($isSulfuras, $currentItem);
                } else {
                    self::resetQuality($currentItem);
                }
                if ($isBrie) {
                    self::increaseQuality($currentItem);
                }
            }
        }
    }

    /**
     * @param $item
     * @return bool
     */
    private function hasNotQuality($item){
        return ($item->getQuality() <= self::MINIMUM_QUALITY);
    }

    /**
     * @param $item
     * @return bool
     */
    private function hasMaximumQuality($item){
        return ($item->getQuality() >= self::MAXIMUM_QUALITY);
    }

    /**
     * @param $isSulfuras
     * @param $item
     */
    private function updateSellIn($isSulfuras, $item)
    {
        if ($isSulfuras) return;
        $item->setSellIn($item->getSellIn() - self::SELLIN_GRANULARITY);
    }

    /**
     * @param $isSulfuras
     * @param $currentItem
     */
    private static function decreaseQuality($isSulfuras, $currentItem)
    {
        if ($isSulfuras && self::hasNotQuality($currentItem)) return;
        $currentItem->setQuality($currentItem->getQuality() - self::QUALITY_GRANULARITY);
    }

    /**
     * @param $currentItem
     */
    private static function increaseQuality($currentItem)
    {
        if (self::hasMaximumQuality($currentItem)) return;
        $currentItem->setQuality($currentItem->getQuality() + self::QUALITY_GRANULARITY);
    }

    /**
     * @param $currentItem
     * @return bool
     */
    private static function isExpired($currentItem)
    {
        return $currentItem->getSellIn() < self::MINIMUN_SELLIN;
    }

    /**
     * @param $currentItem
     */
    private static function resetQuality($currentItem)
    {
        $currentItem->setQuality(self::MINIMUM_QUALITY);
    }

    /**
     * @param $currentItem
     * @return bool
     */
    private static function isInDoubleIncrement($currentItem)
    {
        return $currentItem->getSellIn() <= self::DOUBLE_INCREMENT_THRESHOLD;
    }

    /**
     * @param $currentItem
     * @return bool
     */
    private static function isTripleIncrement($currentItem)
    {
        return $currentItem->getSellIn() <= self::TRIPLE_INCREMENT_THRESHOLD;
    }
}
?>
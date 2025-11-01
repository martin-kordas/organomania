<?php

namespace App;

use App\Enums\OrganCategory;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderAdditionalImage;

/**
 *  - sdružuje data o skříni pro stranu cases.blade.php
 *  - data se načítají ze dvou zdrojů: Organ, OrganBuilderAdditionalImage
 */
final readonly class OrganCaseImage {

    public OrganCategory $periodCategory;
    
    public function __construct(
        public string $id,

        public string $name,
        public string $imageUrl,
        public ?string $imageCredits,

        public int $yearBuilt,
        public ?OrganCategory $caseCategory,
        
        public ?OrganBuilder $organBuilder,
        public ?string $organBuilderName,
        public ?int $stopsCount,
        public ?int $organBuilderActiveFromYear,
        
        public ?string $details,
        
        public ?string $organBuilderExactName = null,
        public ?Organ $organ = null,
    )
    {
        $categories = OrganCategory::getPeriodCategories($yearBuilt);
        $this->periodCategory = $categories->first();
    }

    public static function fromOrgan(Organ $organ)
    {
        $details = [];

        if ($organ->hasCaseOrganBuilder()) {
            $yearBuilt = $organ->case_year_built;
            
            $organBuilder = $organ->caseOrganBuilder;
            $organBuilderName = $organ->case_organ_builder_name ?? $organ->caseOrganBuilder?->initialName;
            $stopsCount = null;
            $organBuilderActiveFromYear = $organ->caseOrganBuilder?->active_from_year;

            $details[] = __('dochována skříň');
        }
        else {
            $yearBuilt = $organ->year_built;
            
            $organBuilder = $organ->organBuilder;
            $organBuilderName = $organ->organ_builder_name ?? $organ->organBuilder?->initialName ?? __('neznámý');
            $stopsCount = static::getOrganStopsCount($organ);
            $organBuilderActiveFromYear = $organ->organBuilder?->active_from_year;

            $sizeInfo = static::getOrganSizeInfo($organ);
            if (isset($sizeInfo)) $details[] = $sizeInfo;
        }   
        
        $detailsStr = empty($details) ? null : implode(', ', $details);

        $caseCategory = $organ->organCategories->map(
            fn ($category) => $category->getEnum()
        )->filter(
            fn ($category) => $category->isCaseCategory()
        )->first();

        return new static(
            id: "organ{$organ->id}",

            name: "{$organ->municipality}, {$organ->shortPlace}",
            imageUrl: $organ->image_url,
            imageCredits: $organ->image_credits,

            organ: $organ,
            yearBuilt: $yearBuilt,
            caseCategory: $caseCategory,

            organBuilder: $organBuilder,
            organBuilderName: $organBuilderName,
            stopsCount: $stopsCount,
            organBuilderActiveFromYear: $organBuilderActiveFromYear,

            details: $detailsStr,
        );
    }

    public static function fromAdditionalImage(OrganBuilderAdditionalImage $additionalImage)
    {
        $details = [];
        if (isset($additionalImage->details)) $details[] = $additionalImage->details;
        $detailsStr = empty($details) ? null : implode(', ', $details);

        return new static(
            id: "additionalImage{$additionalImage->id}",

            name: $additionalImage->name,
            imageUrl: $additionalImage->image_url,
            imageCredits: $additionalImage->image_credits,

            yearBuilt: $additionalImage->year_built,
            caseCategory: $additionalImage->caseOrganCategory,

            organBuilder: $additionalImage->organBuilder,
            organBuilderName: $additionalImage->organ_builder_name ?? $additionalImage->organBuilder?->initialName ?? __('neznámý'),
            stopsCount: $additionalImage->stops_count,
            organBuilderActiveFromYear: $additionalImage->organBuilder?->active_from_year,
            organBuilderExactName: $additionalImage->organ_builder_name,

            details: $detailsStr,
        );
    }

    private static function getOrganSizeInfo(Organ $organ)
    {
        if ($organ->organ_rebuilds_count <= 0) $sizeInfo = $organ->getSizeInfo();
        elseif ($organ->hasOriginalSizeInfo()) {
            $sizeInfo = $organ->getSizeInfo(original: true);
            $sizeInfo .= ', ' . __('přestavěno');
        }
        else $sizeInfo = null;

        return $sizeInfo;
    }

    private static function getOrganStopsCount(Organ $organ)
    {
        if ($organ->organ_rebuilds_count <= 0) return $organ->stops_count;
        else return $organ->original_stops_count;
    }

}
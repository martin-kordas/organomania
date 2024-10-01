<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\OrganCollection;
use App\Http\Resources\OrganBuilderCollection;
use App\Repositories\OrganRepository;

use App\Models\OrganBuilder;

class ExportController extends Controller
{
    
    public function exportOrgans(OrganRepository $repository)
    {
        $organs = $repository->getOrgans(
            with: [...OrganRepository::ORGANS_WITH, 'organRebuilds'],
            withCount: []
        );
        $data = new OrganCollection($organs);
        return $this->getResponse($data, 'organs.json');
    }
    
    public function exportOrganBuilders()
    {
        // TODO: eager loading
        $organBuilders = OrganBuilder::all();
        $data =  new OrganBuilderCollection($organBuilders);
        return $this->getResponse($data, 'organ-builders.json');
    }
    
    private function getResponse($data, $filename)
    {
        return response()
            ->json($data)
            ->header('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
    }
    
}

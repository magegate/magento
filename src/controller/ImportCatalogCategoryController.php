<?php
namespace Magegate;

use Magegate\ImportCatalogCategory;

class ImportCatalogCategoryController extends BaseJsonController {

    public function show($host,$id)
    {
        return ImportCatalogCategory::withTrashed()
            ->where('host','=',$host)
            ->where('id','=',$id)
            ->firstOrFail()->toArray();
    }


    public function index($host)
    {
        if(\Input::get('withTrashed',false)==='true')
        {
            return ImportCatalogCategory::withTrashed()
                ->where('host','=',$host)
                ->get()->toArray();
        }
        if(\Input::get('onlyTrashed',false)==='true')
        {
            return ImportCatalogCategory::onlyTrashed()
                ->where('host','=',$host)
                ->get()->toArray();

        }
        return ImportCatalogCategory
                ::where('host','=',$host)
                ->get()->toArray();
    }

    public function create($host)
    {
        $list = json_decode(\Input::get('list','[]'));

        if(!is_array($list))
            throw(new \Exception("list parameter must be an array",400));

        if(count($list)==0)
            throw(new \Exception("list parameter is an empty list",400));

        $import = ImportCatalogCategory
            ::create(array(
                'host' => $host,
                'list' => $list,
            ));

        $this->jsonResponseFlush($import->toArray());

        while($import->queueNext())
        {
            if(!$import->queueWork()) break;
        }
    }

}
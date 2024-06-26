<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchFileRequest;
use App\Http\Requests\PaginateRequest;
use App\Services\BatchFileService;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponseAlias;

class BatchFIleController extends Controller
{
    private BatchFileService $service;

    public function __construct()
    {
        $this->service = new BatchFileService();
    }

    public function index(PaginateRequest $request)
    {
        return Response::api($this->service->index($request->all()));
    }

    public function show(string $id)
    {
        return Response::api($this->service->show($id));
    }

    public function store(BatchFileRequest $request)
    {
        return Response::api(
            $this->service->uploadFile($request->file('file'), $request->all()),
            HttpResponseAlias::HTTP_CREATED
        );
    }

    public function errors(string $id, PaginateRequest $request)
    {
        return Response::api($this->service->errors($id, $request->all()));
    }
}

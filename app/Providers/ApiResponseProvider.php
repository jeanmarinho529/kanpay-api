<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as HttpResponseAlias;

class ApiResponseProvider extends ServiceProvider
{
    public function boot(): void
    {
        Response::macro('api', function (mixed $data, $httpCode = HttpResponseAlias::HTTP_OK) {

            $status = ! ($httpCode >= HttpResponseAlias::HTTP_MULTIPLE_CHOICES);

            $paginate = [
                'per_page' => '',
                'current_page' => 1,
                'has_more' => false,
            ];
            if ($data instanceof Paginator) {
                $paginate['per_page'] = $data->perPage();
                $paginate['current_page'] = $data->currentPage();
                $paginate['has_more'] = $data->hasMorePages();

                $data = $data->items();
            }

            $result = [
                'result' => is_string($data) ? ['message' => $data] : ['data' => $data],
                'status' => $status,
                'status_code' => $httpCode,
                'paginate' => $paginate,
            ];

            return Response::json($result, $httpCode);
        });
    }
}

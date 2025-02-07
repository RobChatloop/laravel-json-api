<?php
/*
 * Copyright 2024 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace LaravelJsonApi\Laravel\Http\Controllers\Actions;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use LaravelJsonApi\Contracts\Routing\Route;
use LaravelJsonApi\Contracts\Store\Store as StoreContract;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Requests\ResourceQuery;

trait FetchMany
{

    /**
     * Fetch zero to many JSON API resources.
     *
     * @param Route $route
     * @param StoreContract $store
     * @return Responsable|Response
     */
    public function index(Route $route, StoreContract $store)
    {
        $request = ResourceQuery::queryMany(
            $resourceType = $route->resourceType()
        );

        $response = null;

        if (method_exists($this, 'searching')) {
            $response = $this->searching($request);
        }

        if ($response) {
            return $response;
        }

        $data = $store
            ->queryAll($resourceType)
            ->withRequest($request)
            ->firstOrPaginate($request->page());

        if (method_exists($this, 'searched')) {
            $response = $this->searched($data, $request);
        }

        return $response ?: DataResponse::make($data)->withQueryParameters($request);
    }
}

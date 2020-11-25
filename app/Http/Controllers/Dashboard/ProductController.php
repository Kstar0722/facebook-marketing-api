<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

use App\Models\AdAccount;
use Illuminate\Http\JsonResponse;

/**
 * @SWG\Definition(
 *     definition="Product",
 *     type="object",
 *     description="Response for Product",
 *     properties={
 *          @SWG\Property(property="id", type="integer", description="Product UUID"),
 *          @SWG\Property(property="user_id", type="integer", description="User UUID"),
 *          @SWG\Property(property="name", type="string", description="FB User ID"),
 *          @SWG\Property(property="created_at", type="string", description="Created Date"),
 *          @SWG\Property(property="updated_at", type="string", description="Updated Date")
 *     }
 * )
 */

class ProductController extends BaseDashboardController
{
    
    /**
     * @SWG\Get(
     *   path="/product/list",
     *   summary="Get Product List",
     *   tags={"Products"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="status", type="string", description="status"),
     *             @SWG\Property(property="data", type="array", description="The response data", @SWG\items(ref="#/definitions/Product")),
     *             @SWG\Property(property="message", type="string", description="Message"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized"
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function index() {
        return $this->sendResponse('Success', Auth::user()->products);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */

     /**
     * @SWG\Post(
     *   path="/product",
     *   summary="Create Product",
     *   tags={"Product"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     description="Product Name",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="status", type="string", description="status"),
     *             @SWG\Property(property="data", type="object", ref="#/definitions/Product", description="The response data"),
     *             @SWG\Property(property="message", type="string", description="Message"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized"
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function store(Request $request) {
        $user_id = Auth::user()->id;
        $name = $request->name;
        if (!$name) {
            return $this->sendError('The name is required');
        }
        try {
            $product = Product::create([
                'user_id' => $user_id,
                'name' => $request->name
            ]);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse('Success', $product);
    }

    /**
     * get product by id.
     *
     * @param  int  $id
     * @return JsonResponse
     */

    /**
     * @SWG\Get(
     *   path="/product/{productID}",
     *   summary="Get Product by id",
     *   tags={"Product"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Parameter(
     *     name="productID",
     *     in="path",
     *     description="Product ID",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="status", type="string", description="status"),
     *             @SWG\Property(property="data", type="object", ref="#/definitions/Product", description="The response data"),
     *             @SWG\Property(property="message", type="string", description="Message"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized"
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function show($id) {
        try {
            $product = Product::find($id);
            if (empty($product)) {
                return $this->sendError("Can't find product");
            }
            return $this->sendResponse('Success', $product);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */

     /**
     * @SWG\Put(
     *   path="/product/{productID}",
     *   summary="Update Product by id",
     *   tags={"Product"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Parameter(
     *     name="productID",
     *     in="path",
     *     description="Product ID",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     description="Product Name",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="status", type="string", description="status"),
     *             @SWG\Property(property="data", type="object", ref="#/definitions/Product", description="The response data"),
     *             @SWG\Property(property="message", type="string", description="Message"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized"
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function update(Request $request, $id) {
        $name = $request->name;
        if (!$name) {
            return $this->sendError('The name is required');
        }
        try {
            $product = Product::find($id);
            if (!$product) {
                return $this->sendError("The product doesn't exist");
            }
            $product->update([
                'name' => $name
            ]);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse('Success', Product::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */

    /**
     * @SWG\Delete(
     *   path="/product/{productID}",
     *   summary="Delete Product by id",
     *   tags={"Product"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Parameter(
     *     name="productID",
     *     in="path",
     *     description="Product ID",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="status", type="string", description="status"),
     *             @SWG\Property(property="data", type="object", ref="#/definitions/Product", description="The response data"),
     *             @SWG\Property(property="message", type="string", description="Message"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized"
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return $this->sendError("The product doesn't exist");
            }
            foreach($product->adAccounts as $adAccount) {
                $adAccount->update(['product_id' => null]);
            }
            $product->delete();
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse('success', $product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */

     /**
     * @SWG\Get(
     *   path="/product/{id}/ad_account/list",
     *   summary="Get Ad Accounts by Product ID",
     *   tags={"Ad Account"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Product ID",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="status", type="string", description="status"),
     *             @SWG\Property(property="data", type="array", description="The response data", @SWG\items(
     *                ref="#/definitions/AdAccount"
     *             )),
     *             @SWG\Property(property="message", type="string", description="Message"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized"
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function getAdAccounts($id) {
        try {
            $product = Product::find($id);
            if (!$product) {
                return $this->sendError("The product doesn't exist");
            }
            return $this->sendResponse('Success', $product->adAccounts);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}

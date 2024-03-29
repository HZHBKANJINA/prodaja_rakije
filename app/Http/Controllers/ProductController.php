<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search=request('search',false);
        $perPage=request('per_page',10);
        $sortField=request('sort_field','updated_at');
        $sortDirection=request('sort_direction','desc');


        $query=Product::query();
        $query->orderBy($sortField,$sortDirection);
        if($search){
            $query->where('title','like',"%{$search}")->orWhere('description','like',"%{$search}%");
        }

        return ProductListResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data=$request->validated();
        $data['created_by']=$request->user()->id;
        $data['updated_by']=$request->user()->id;

        $image=$data['image'] ?? null;

        if($image){
            $relativePath=$this->saveImage($image);
            $data['image']=URL::to(Storage::url($relativePath));
            $data['image_mime']=$image->getClientMimeType();
            $data['image_size'] = $image->getSize();
        }

        $product=Product::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data=$request->validated();
        $data['updated_by']=$request->user()->id;

        $image=$data['image'] ?? null;

        if($image){
            $relativePath=$this->saveImage($image);
            $data['image']=URL::to(url($relativePath));
            $data['image_mime']=$image->getClientMimeType();
            $data['image_size']=$image->getSize();

            if($product->image){
                Storage::deleteDirectory('/public/' . dirname($product->image));
            }
        }
        $product->update($data);

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }

    private function saveImage(\Illuminate\Http\UploadedFile $image){
        $path ='images/' . Str::random();
        if(!Storage::exists($path)){
            Storage::makeDirectory($path,0755,true);
        }
        if(!Storage::putFileAS('public/' . $path,$image,$image->getClientOriginalName())){
            throw new \Exception("unable to save file \"{$image->getClientOriginalName}\"");
        }
        return $path .'/' . $image->getClientOriginalName();
    }
}

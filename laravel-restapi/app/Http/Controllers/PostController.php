<?php

namespace App\Http\Controllers;

use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    // constructor otomatis terpanggil ketika ada request client
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // menampilkan data
    public function index()
    {
        $data = Post::with(['user'])->paginate(5);
        // menggunakan Resource Collection
        return new PostCollection($data);

        // return response()->json($data, 200);
    }

    // menampilkan data berdasarkan id
    public function show($id)
    {
        $data = Post::find($id);

        // respon jika data tidak ditemukan
        if(is_null($data)) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 403);
        }

        // custom respon dengan api resource
        return new PostResource($data);

        // return response()->json($data, 200);
    }

    // menyimpan data
    public function store(Request $request)
    {
        $data = $request->all();
        
        // menambah validator
        $validator = Validator::make($data, [
            'title' => 'required', 'min:5']);
        // jika validasi gagal tampilkan error
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        // menyimpan data dengan api token
        $response = request()->user()->posts()->create($data);

        // $response = Post::create($data);
        return response()->json($response, 201);
    }

    // mengupdate data dengan metode binding
    public function update(Request $request, Post $post)
    {   
        $post->update($request->all());
        return response()->json($post, 200);
    }

    // menghapus data
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, 200);
    }
}

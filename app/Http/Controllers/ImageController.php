<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use Carbon\Carbon;
use DB;
use Mockery\Exception;
use Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class ImageController extends Controller
{
    public function getImageList(Request $request){

        $imageList = Image::orderBy('created_at', 'asc')->get();
        //try{
            //$user= $request->get('current_user');
            //$user_post =PostManager::where('user_id',$user[0]['id'])->select('id','post_content')->get();
        //}
        //catch (Exception $ex){
            //return $this->errorInternalError();
        //}


        return $this->respondWithSuccess($imageList,200);
    }


    public function uploadImage(Request $request){

        $file = $request->file('file');
        $fileName=explode("?", $file->getFilename())[0];
        $extension=explode("?", $file->getClientOriginalExtension())[0];
        Storage::disk('public')->put($fileName.'.'.$extension,  File::get($file));

        $book = new Image();
        $book->filename = $fileName;
        $book->quality = $request->quality;
        $book->image_url = env('APP_URL').'/uploads/'.$fileName.'.'.$extension;
        $book->save();
        return $this->respondWithSuccess($book->image_url,200);
    }
}

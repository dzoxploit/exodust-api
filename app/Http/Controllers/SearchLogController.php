<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GithubUser;
use App\Models\SearchLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exception;
use Illuminate\Support\Facades\Redis;
use Auth;

class SearchLogController extends Controller
{
    public function index(Request $request){
         
        $query = \json_encode($request->all());
        $data = Cache::remember("search_logs-$query", 120, function () use ($query, $request) {
            $searchlog = SearchLog::where('id_user',auth('api')->user()->id)->orderBy('id','DESC')->get();
            return $searchlog;
        });

        return $this->generateResponse($data, '', 200);
    }
}

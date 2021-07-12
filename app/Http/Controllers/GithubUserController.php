<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use App\Models\GithubUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exception;


class GithubUserController extends Controller
{
    public function index(Request $request){
       
        $query = \json_encode($request->all());
        $data = Cache::remember("github_users-$query", 120, function () use ($query, $request) {
            $githubuser = GithubUser::get();
            return $githubuser;
        });

        return $this->generateResponse($data, '', 200);
    }  

    public function searching(Request $request){
        $query = json_encode($request->all());
        $data = Cache::remember("github_users-$query", 120, function () use ($query,$request) {
           // split on 1+ whitespace & ignore empty (eg. trailing space)
            $searchValues = preg_split('/\s+/', $request->get('q'), -1, PREG_SPLIT_NO_EMPTY);

            $githubuser = GithubUser::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->where('username', 'like', "%{$value}%")
                  ->orWhere('organization','like',"%{$value}%")
                  ->orWhere('company','like',"%{$value}%");
            }
            });

            return $githubuser->simplePaginate();
        });

        return $this->generateResponse($data, '', 200);
    }


    public function update(Request $request, $id){
        try {        
                $githubusers = GithubUser::where('id',$id)->first();
                if($githubusers != null){
                    $user = 'your-username';
                    $pwd = 'your-password';
                    $username = $request->username;
            
                    $url = 'https://api.github.com/users/'.$username;
                    $cInit = curl_init();
                    curl_setopt($cInit, CURLOPT_URL, $url);
                    curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE
                    curl_setopt($cInit, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                    curl_setopt($cInit, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($cInit, CURLOPT_USERPWD, $user . ':' . $pwd);
            
                    $output = curl_exec($cInit);
            
                    $info = curl_getinfo($cInit, CURLINFO_HTTP_CODE);
                    $err = curl_error($cInit);
                    $result = json_decode($output);
            
                    curl_close($cInit);
            
            
                    $githubuser = $githubusers;
                    $githubuser->username = $result->login;
                    $githubuser->name = $result->name;
                    $githubuser->company = RemoveCharCompany($result->company);
                    $githubuser->followers = $result->followers;
                    $githubuser->count_public_repos = $result->public_repos;
                    $githubuser->following = $result->following;
                    $githubuser->average_number_per_followers = get_average_number_followers($result->followers, $result->public_repos);
                    $githubuser->id_user = auth('api')->user()->id;
                    $githubuser->save();
            
                    Cache::tags(['github_users'])->flush();
                    return $this->generateResponse($githubuser, 'data successfully updated', 201);        

                }
        }catch(\Exception $e){
            $data = "Error {$e->getMessage()}";
            Log::error($data);
            return $this->generateResponse($data, 'Callback Transaction gagal di tambahkan', 401);
        }
    }

    public function delete(Request $request, $id){
        try {
            $githubuser = GithubUser::findOrFail($id);
            $githubuser->delete();
            
            DB::statement("SET @count = 0;");
            DB::statement("UPDATE github_users SET github_users.id = @count:= @count + 1;");
            DB::statement("ALTER TABLE github_users AUTO_INCREMENT = 1;");
            
            Cache::tags(['github_users'])->flush();
            return $this->generateResponse([], 'data deleted!', 202);
        }catch(\Exception $e){
            $data = "Error {$e->getMessage()}";
            Log::error($data);
            return $this->generateResponse($data, 'Callback Transaction gagal di tambahkan', 401);
        }
    }
}

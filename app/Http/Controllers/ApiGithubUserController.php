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
class ApiGithubUserController extends Controller
{
    /* Function show index github main */
    public function index_github_main(Request $request){
        /**call env file */
        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $username = $request->username;

        //setup curl
        $url = 'https://api.github.com/users/'.$username;
        $cInit = curl_init();
        curl_setopt($cInit, CURLOPT_URL, $url);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE
        curl_setopt($cInit, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($cInit, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cInit, CURLOPT_USERPWD, $user . ':' . $pwd);

        //show output curl
        $output = curl_exec($cInit);

        $info = curl_getinfo($cInit, CURLINFO_HTTP_CODE);
        $err = curl_error($cInit);
        $result = json_decode($output);

        curl_close($cInit);
        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            try {
                //create redis and set new value redis cache cluster
                $redis = Redis::connection();

                $redis->set('github_user_mains', json_encode([
                    'id' => $result->id,
                    'username' => $result->login,
                    'name' => $result->name,
                    'company' => detail_company_github($result->company),
                    'organizations' => detail_organization_github($result->organizations_url),
                    'following' => $result->following,
                    'detail_following' => get_following_github($result->following_url),
                    'followers' => $result->followers,
                    'detail_followers' => get_followers_github($result->followers),
                    'public_repository_count' => $result->public_repos,
                    'average_number_per_followers' => get_average_number_followers($result->followers, $result->public_repos),
                    'detail_repository' => detail_repository_github($result->repos_url)
                    ])
                );

                $response = $redis->get('github_user_mains');
                $response = json_decode($response);

                return $this->generateResponse($response, 'Data github Search', 200);
            }catch(\Exception $e){
                //call redis cluster
                $redis = Redis::connection();
                $response = $redis->get('github_user_mains');
                $response = json_decode($response);

                return $this->generateResponse($response, "Data github Search", 404);
            }
        }
    }
    /* Function show index github main with auth*/
    public function index_github_main_auth(Request $request){
           /**call env file */
        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $username = $request->username;
        //set curl
        $url = 'https://api.github.com/users/'.$username;
        $cInit = curl_init();
        curl_setopt($cInit, CURLOPT_URL, $url);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE
        curl_setopt($cInit, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($cInit, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cInit, CURLOPT_USERPWD, $user . ':' . $pwd);

        //show output redis
        $output = curl_exec($cInit);

        $info = curl_getinfo($cInit, CURLINFO_HTTP_CODE);
        $err = curl_error($cInit);
        $result = json_decode($output);
        //curl close function
        curl_close($cInit);
        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            try {
                //create redis and set new value redis cache cluster
                $redis = Redis::connection();

                $redis->set('github_user_mains', json_encode([
                    'id' => $result->id,
                    'username' => $result->login,
                    'name' => $result->name,
                    'company' => detail_company_github($result->company),
                    'organizations' => detail_organization_github($result->organizations_url),
                    'following' => $result->following,
                    'detail_following' => get_following_github($result->following_url),
                    'followers' => $result->followers,
                    'detail_followers' => get_followers_github($result->followers),
                    'public_repository_count' => $result->public_repos,
                    'average_number_per_followers' => get_average_number_followers($result->followers, $result->public_repos),
                    'detail_repository' => detail_repository_github($result->repos_url)
                    ])
                );

                $response = $redis->get('github_user_mains');
                $response = json_decode($response);

                //save search log from data search 

                $searchlog = new SearchLog;
                $searchlog->id_user = auth('api')->user()->id;
                $searchlog->searching = $username;
                $searchlog->save();

                return $this->generateResponse($response, 'Data github Search', 201);
            }catch(\Exception $e){
                //call redis cluster
                $redis = Redis::connection();
                $response = $redis->get('github_user_mains');
                $response = json_decode($response);

                $searchlog = SearchLog::orderBy('id','DESC')->first();
                
                if($response->username == $searchlog->searching){
                    return $this->generateResponse($response, "Data github Search In Cache", 200);
                }else{
                    $responsenull = null;
                    return $this->generateResponse($responsenull, "Data github Search In Cache", 200);
                }

                
            }
        }
    }

    //insert data github users
    public function insert_data_github_users(Request $request){
        try {
            $user = env('GITHUB_USERNAME');
            $pwd = env('GITHUB_PASSWORD');
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


            $githubuser = new GithubUser;
            $githubuser->username = $result->login;
            $githubuser->name = $result->name;
            $githubuser->company = RemoveCharCompany($result->company);
            $githubuser->followers = $result->followers;
            $githubuser->following = $result->following;
            $githubuser->count_public_repos = $result->public_repos;
            $githubuser->average_number_per_followers = get_average_number_followers($result->followers, $result->public_repos);
            $githubuser->id_user = auth('api')->user()->id;
            $githubuser->save();

            $searchlog = new SearchLog;
            $searchlog->id_user = auth('api')->user()->id;
            $searchlog->searching = $username;
            $searchlog->save();

            return $this->generateResponse($githubuser, 'Data github berhasil disimpan', 201);
        }catch(\Exception $e){
            $data = "Error {$e->getMessage()}";
            Log::error($data);
            return $this->generateResponse($data, 'Callback Transaction gagal di tambahkan', 401);
        }
    }
    //list organization repositories
    public function list_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $organization = $request->organization;

        $url = 'https://api.github.com/orgs/'.$organization.'/repos';
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

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'Data github organization Search', 200);
        }
    }
    //function get organization repositories 
    public function get_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $owner = $request->owner;
        $repo = $request->repo;

        $url = 'https://api.github.com/repos/'.$owner.'/'.$repo;
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

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'get repo github organization', 200);
        }
    }

    //function create organization repositories
    public function create_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $organization = $request->organization;

        $data = [
            "name" => $request->name,
            "description" => $request->description,
            "homepage" => "https://github.com",
            "private" => false,
            "has_issues" => true,
            "has_projects" => true,
            "has_wiki" => true
        ];

        $url = 'https://api.github.com/orgs/'.$organization.'/repos';
        $cInit = curl_init();
        curl_setopt($cInit, CURLOPT_URL, $url);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE
        curl_setopt($cInit, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($cInit, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cInit, CURLOPT_USERPWD, $user . ':' . $pwd);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE


        $output = curl_exec($cInit);

        $info = curl_getinfo($cInit, CURLINFO_HTTP_CODE);
        $err = curl_error($cInit);
        $result = json_decode($output);

        curl_close($cInit);

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'Create github organization repositories', 200);
        }
    }

     //function create organization repositories
    public function update_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        

        $data = [
            "name" => $request->name,
            "description" => $request->description,
            "homepage" => "https://github.com",
            "private" => false,
            "has_issues" => true,
            "has_projects" => true,
            "has_wiki" => true
        ];

        $owner = $request->owner;
        $repo = $request->repo;

        $url = 'https://api.github.com/repos/'.$owner.'/'.$repo;
        $cInit = curl_init();
        curl_setopt($cInit, CURLOPT_URL, $url);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE
        curl_setopt($cInit, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($cInit, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cInit, CURLOPT_USERPWD, $user . ':' . $pwd);
        curl_setopt($cInit, CURLOPT_PATCH, true);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cInit, CURLOPT_POSTFIELDS, $data);


        $output = curl_exec($cInit);

        $info = curl_getinfo($cInit, CURLINFO_HTTP_CODE);
        $err = curl_error($cInit);
        $result = json_decode($output);

        curl_close($cInit);

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'Update github organization repositories', 200);
        }
    }

     //function delte organization repositories
    public function delete_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $owner = $request->owner;
        $repo = $request->repo;

        $data = [
            "name" => $request->name,
            "description" => $request->description,
            "homepage" => "https://github.com",
            "private" => false,
            "has_issues" => true,
            "has_projects" => true,
            "has_wiki" => true
        ];

        $url = 'https://api.github.com/repos/'.$owner.'/'.$repo;
        $cInit = curl_init();
        curl_setopt($cInit, CURLOPT_URL, $url);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE
        curl_setopt($cInit, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($cInit, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cInit, CURLOPT_USERPWD, $user . ':' . $pwd);
        curl_setopt($cInit, CURLOPT_DELETE, true);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, true);


        $output = curl_exec($cInit);

        $info = curl_getinfo($cInit, CURLINFO_HTTP_CODE);
        $err = curl_error($cInit);
        $result = json_decode($output);

        curl_close($cInit);

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'Update github organization repositories', 200);
        }
    }

    //function for enable automated fixes organization repositories
     public function enable_automated_fixes_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $owner = $request->owner;
        $repo = $request->repo;

        $url = 'https://api.github.com/repos/'.$owner.'/'.$repo.'/automated-security-fixes';

        $data = [
            "name" => $request->name,
            "description" => $request->description,
            "homepage" => "https://github.com",
            "private" => false,
            "has_issues" => true,
            "has_projects" => true,
            "has_wiki" => true
        ];

        $cInit = curl_init();
        curl_setopt($cInit, CURLOPT_URL, $url);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE
        curl_setopt($cInit, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($cInit, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cInit, CURLOPT_USERPWD, $user . ':' . $pwd);
        curl_setopt($cInit, CURLOPT_PUT, true);


        $output = curl_exec($cInit);

        $info = curl_getinfo($cInit, CURLINFO_HTTP_CODE);
        $err = curl_error($cInit);
        $result = json_decode($output);

        curl_close($cInit);

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'Update github organization repositories', 200);
        }
    }

    //function for disable automated fixes organization repositories
    public function disable_automated_fixes_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $owner = $request->owner;
        $repo = $request->repo;

        $url = 'https://api.github.com/repos/'.$owner.'/'.$repo.'/automated-security-fixes';

        $data = [
            "name" => $request->name,
            "description" => $request->description,
            "homepage" => "https://github.com",
            "private" => false,
            "has_issues" => true,
            "has_projects" => true,
            "has_wiki" => true
        ];

        $cInit = curl_init();
        curl_setopt($cInit, CURLOPT_URL, $url);
        curl_setopt($cInit, CURLOPT_RETURNTRANSFER, 1); // 1 = TRUE
        curl_setopt($cInit, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($cInit, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($cInit, CURLOPT_USERPWD, $user . ':' . $pwd);
        curl_setopt($cInit, CURLOPT_DELETE, true);


        $output = curl_exec($cInit);

        $info = curl_getinfo($cInit, CURLINFO_HTTP_CODE);
        $err = curl_error($cInit);
        $result = json_decode($output);

        curl_close($cInit);

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'Update github organization repositories', 200);
        }
    }

    //function get list code owners errors
    public function list_code_owners_errors_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $owner = $request->owner;
        $repo = $request->repo;

        $url = 'https://api.github.com/repos/'.$owner.'/'.$repo.'/codeowners/errors';


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

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'List Code Owners organization repositories', 200);
        }
    }

    //function contributors list code owners errors
    public function list_repository_contributors_organization_repositories_github_main(Request $request){

        $user = env('GITHUB_USERNAME');
        $pwd = env('GITHUB_PASSWORD');
        $owner = $request->owner;
        $repo = $request->repo;

        $url = 'https://api.github.com/repos/'.$owner.'/'.$repo.'/contributors';


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

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'List Code Owners organization repositories', 200);
        }
    }
}

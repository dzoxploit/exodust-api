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
    /* untuk menampilkan data antara news berbasis html */
    public function index_github_main(Request $request){
        
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
        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            try {
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
                $redis = Redis::connection();
                $response = $redis->get('github_user_mains');
                $response = json_decode($response);

                return $this->generateResponse($response, "Data github Search", 404);
            }
        }
    }

    public function index_github_main_auth(Request $request){
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
        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            try {
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

                $searchlog = new SearchLog;
                $searchlog->id_user = auth('api')->user()->id;
                $searchlog->searching = $username;
                $searchlog->save();

                return $this->generateResponse($response, 'Data github Search', 201);
            }catch(\Exception $e){
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

    public function insert_data_github_users(Request $request){
        try {
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

    public function detail_organization_github_main(Request $request){

        $user = 'your-username';
        $pwd = 'your-password';
        $username = $request->organization;

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

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return $this->generateResponse('Error', "cURL Error #:" . $err, 401);
        } else {
            return $this->generateResponse($result, 'Data github Search', 200);
        }
    }

    public function detail_company_github_main(Request $request){
        $company = $request->get('company');

        $companyget = RemoveCharCompany($company);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.github.com/users/{$companyget}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::error("cURL Error #:" . $err);
            return "cURL Error #:" . $err;
        } else {
            return $this->generateResponse($response, 'Data github Search', 200);
        }
    } 

    public function following_click_github_user(Request $request){
        
    }
 
}

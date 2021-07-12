<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GitlabUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exception;

class ApiGitlabUserController extends Controller
{
    public function index_gitlab_main(Request $request){
        $username = $request->get('username');

        $url = 'https://gitlab.com/'.$username;
        $document = new Document($url, true);
        $posts = $document->find("//article[contains(@class, 'simple-post simple-big clearfix')]//header//h3//a", Query::TYPE_XPATH);
        for($i = 0; $i < count($posts); $i++) {
            $coba = $posts[$i];
            $data = new \SimpleXMLElement($coba);
            $linkcuyy[$i] = $data['href'];
            $article[] = array(
                'title' => $posts[$i]->text(),
                'url' => strval($linkcuyy[$i]),
                'date' => Carbon::now()->format('Y-m-d')
            );
        }
    }

    public function insert_gitlab_main(Request $request){
        $username = $request->get('username');
        try{
            $url = 'https://gitlab.com/'.$username;
            $document = new Document($url, true);
            $posts = $document->find("//article[contains(@class, 'simple-post simple-big clearfix')]//header//h3//a", Query::TYPE_XPATH);
            for($i = 0; $i < count($posts); $i++) {
                $coba = $posts[$i];
                $data = new \SimpleXMLElement($coba);
                $linkcuyy[$i] = $data['href'];
                $article[] = array(
                    'title' => $posts[$i]->text(),
                    'url' => strval($linkcuyy[$i]),
                    'date' => Carbon::now()->format('Y-m-d')
                );
            }

            $githubuser = new GithubUser;
            $githubuser->username = $result->login;
            $githubuser->name = $result->name;
            $githubuser->company = RemoveCharCompany($result->company);
            $githubuser->followers = $result->followers;
            $githubuser->following = $result->following;
            $githubuser->average_number_per_followers = get_average_number_followers($result->followers, $result->public_repos);
            $githubuser->save();

            return $this->generateResponse($githubuser, 'Data github berhasil disimpan', 201);
        }catch(\Exception $e){
            $data = "Error {$e->getMessage()}";
            Log::error($data);
            return $this->generateResponse($data, 'Callback Transaction gagal di tambahkan', 401);
        }
        
    }
 
}

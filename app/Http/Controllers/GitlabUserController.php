<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GitlabUser;
use Illuminate\Support\Facades\Cache;

class GitlabUserController extends Controller
{
    public function index(GitlabUser $gitlabuser){
        $query = json_encode($content);
        $gitlab_user = Cache::tags(['gitlab_users'])->remember("gitlab-users-$query", 3600, function() use ($githubuser) {
            return $gitlabhubuser;
        });
        return $this->generateResponse($gitlab_user, 'data gitlab users', 200);
    }  

    public function searching(Request $request){
        $query = json_encode($request->all());
        $data = Cache::remember("gitlab-users-$query", 3600, function () use ($query,$request) {
           // split on 1+ whitespace & ignore empty (eg. trailing space)
            $searchValues = preg_split('/\s+/', $request->get('q'), -1, PREG_SPLIT_NO_EMPTY);

            $githubuser = GitlabUser::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->where('username', 'like', "%{$value}%");
            }
            });

            return $githubuser->simplePaginate();
        });

        return $this->generateResponse($data, '', 200);
    }

    public function update(Request $request, GitlabUser $gitlabuser){
        $payload = collect([
            'username' => 'required',
            'name' => 'required',
            'followers' => 'required|int',
            'following' => 'required|int',
            'average_number_per_followers' => 'required|decimal',

        ]);

        $validation = $request->validate($payload->all());

        $githubuser->update($validation);
        Cache::tags(['gitlab_users'])->flush();
        return $this->generateResponse($gitlabuser, 'data successfully updated', 201);        

    }

    public function delete(GitlabUser $gitlabbuser){
        $gitlabuser->delete();
        Cache::tags(['gitlab_users'])->flush();
        return $this->generateResponse([], 'data deleted!', 202);
    }
}

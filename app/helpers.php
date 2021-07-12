<?php
use DiDom\Document;
use DiDom\Query;

    function get_followers_github($followers_url){
         
        $user = 'your-username';
        $pwd = 'your-password';

        $cInit = curl_init();
        curl_setopt($cInit, CURLOPT_URL, $followers_url);
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
        return "cURL Error #:" . $err;
        } else {
            return $result;
        }
    }
        /* digunakan untuk menampilkan data antara news dari rss xml */
    function get_following_github($following_url){
           
        $user = 'your-username';
        $pwd = 'your-password';

        $url = $following_url;
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
            return "cURL Error #:" . $err;
        } else {
            return $result;
        }
    
    }
         /* digunakan untuk menyimpan data tribunnews dari rss xml ke dalam database */
    function detail_repository_github($repos){
        $user = 'your-username';
        $pwd = 'your-password';

        $url = $repos;
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
        return null;
        } else {
            return $result;
        }
    }
             /* digunakan untuk menyimpan data antara news dari rss xml ke dalam database */
    function detail_organization_github($orgs){
        $user = 'your-username';
        $pwd = 'your-password';

        $url = $orgs;
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
        return null;
        } else {
            return $result;
        }     
    }
             /* digunakan untuk menyimpan data tribunnews dari rss xml ke dalam database dengan metode command */
    function detail_company_github($company){

        $user = 'your-username';
        $pwd = 'your-password';

        $url = "github.com/".RemoveCharCompany($company);
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
        return null;
        } else {
            return $result;
        }
    }

    function get_average_number_followers($followers, $count_public_repos){
        $average_numbers_followers = $followers / $count_public_repos; 
        return $average_numbers_followers;
    }

    function RemoveCharCompany($str){
      
        // Using str_ireplace() function 
        // to replace the word 
        $res = str_ireplace( array( '@'), '', $str);
          
        // returning the result 
        return $res;
    }

    function RemoveOtherUrl($str){
        $res = str_replace("{/other_user}","",$str);

        return $res;
    }
      
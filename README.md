## About Exodust Beta
<br/>
<h1>*route application</h1>
<br/>
<h3>- auth</h3>
<br/>
<h4>[post] http://localhost:8000/api/insert-github-users</h4><br/>
<h4>[post] http://localhost:8000/api/index-github-users/auth</h4><br/>
<h4>[get] http://localhost:8000/api/index-search-logs</h4><br/>
<h4>[get] http://localhost:8000/api/index-github-users-saved</h4><br/>
<h4>[get] http://localhost:8000/api/searching-github-users-saved?q={yoursearch}</h4><br/>
<h4>[post] http://localhost:8000/api/update-github-users/{id}</h4><br/>
<h4>[post] http://localhost:8000/api/delete-github-users/{id}</h4><br/>

<h3>- Non Auth</h3>
<br/>
<h4>[post] http://localhost:8000/api/index-github-users</h4><br/>
<h4>[post] http://localhost:8000/api/register</h4><br/>
<h4>[post] http://localhost:8000/api/login</h4><br/>
<h4>[post] http://localhost:8000/api/login</h4><br/>


- How to make run Application

1. Download Zip application and extract 

2. Open command prompt and direct your folder typing 
    composer install
3. typing in command prompt
    php artisan migrate
    php artisan passport:install
4. download your redis and setting port redis 127.0.0.1:6379
5. typing in command prompt
    1-php artisan config:cache
    2-php artisan cache:clear
6. check helpers.php, ApiGithubUserController.php & GithubUserController.php and rename part code in below change to your username and password github.

  <p>$user = 'your-username';</p><br/>
  <p>$pwd = 'your-password';</p><br/>

</h1>*image route application</h1>
<h3>#route auth</h3>
<br/>
<img src="DocumentionImage/body.PNG">

<br/>
<br/>
<br/>
<br/>


<img src="DocumentionImage/token.PNG">

<br/>
<br/>
<br/>
<br/>

<h3>#register</h3>
<br/>
<img src="DocumentionImage/register.PNG">

<br/>
<br/>
<br/>
<br/>

<h3>#login</h3>
<br/>
<img src="DocumentionImage/login.PNG">

<br/>
<br/>
<br/>
<br/>

<h3>#index github users</h3>
<br/>
<img src="DocumentionImage/index-github-users.PNG">
<br/>
<h5>In Folder test2calculatehamming ready code for Bonus Challenge</h5> 

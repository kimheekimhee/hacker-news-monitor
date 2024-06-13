<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

1. 프로젝트 개요  
    이름: Hacker News Monitor  
    목적: Hacker News의 최신 기사를 주기적으로 가져와 웹 페이지에 표시합니다.  
    주요 기능: 최신 기사 가져오기, 10초마다 자동 업데이트  

2. 기술 스택  
    프레임워크: Laravel  
    프론트엔드: jQuery  
    백엔드: PHP  
    IDE: PhpStorm  
    API: Hacker News API  

3. 주요 파일 및 코드 설명  
    3.1. 라우트 설정 (routes/web.php)  
        역할: 요청을 적절한 컨트롤러 메서드와 연결합니다.  

        ```php
        use App\Http\Controllers\HackerNewsController;

        Route::get('/hacker-news', [HackerNewsController::class, 'index']);
        Route::get('/fetch-hacker-news', [HackerNewsController::class, 'fetch']);
        ```  
    3.2. 컨트롤러 (app/Http/Controllers/HackerNewsController.php)  
        역할: 비즈니스 로직을 처리하고 뷰에 데이터를 전달합니다.  

        ```php
        namespace App\Http\Controllers;

        use App\Models\HackerNews;
        use Illuminate\Http\Request;

        class HackerNewsController extends Controller
        {
            public function index()
            {
                $articles = HackerNews::latest()->take(10)->get();
                return view('hackernews.index', compact('articles'));
            }

            public function fetch()
            {
                $ids = file_get_contents('https://hacker-news.firebaseio.com/v0/topstories.json');
                $ids = json_decode($ids);

                $articles = [];
                foreach (array_slice($ids, 0, 10) as $id) {
                    $item = file_get_contents("https://hacker-news.firebaseio.com/v0/item/$id.json");
                    $item = json_decode($item);

                    $articles[] = [
                        'title' => $item->title,
                        'url' => $item->url ?? '#'
                    ];
                }

                HackerNews::truncate();
                HackerNews::insert($articles);

                return response()->json(['status' => 'success']);
            }
        }
        ```  
    3.3. 뷰 파일 (resources/views/hackernews/index.blade.php)  
        역할: 사용자에게 데이터를 시각적으로 표시합니다.  

        ```html
        <!DOCTYPE html>
        <html>
        <head>
            <title>Hacker News Monitor</title>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    function fetchNews() {
                        $.ajax({
                            url: '/fetch-hacker-news',
                            method: 'GET',
                            success: function(response) {
                                if (response.status === 'success') {
                                    location.reload();
                                }
                            }
                        });
                    }

                    setInterval(fetchNews, 10000);
                });
            </script>
        </head>
        <body>
            <h1>Hacker News Monitor</h1>
            <ul>
                @foreach($articles as $article)
                    <li>
                        <a href="{{ $article->url }}" target="_blank">{{ $article->title }}</a>
                    </li>
                @endforeach
            </ul>
        </body>
        </html>
        ```  
    3.4. 데이터베이스 마이그레이션 (database/migrations/2024_06_13_175357_create_hacker_news_table.php)  
        역할: 데이터베이스 테이블을 생성합니다.  

        ```php
        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        class CreateHackerNewsTable extends Migration
        {
            public function up()
            {
                Schema::create('hacker_news', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->string('url')->nullable();
                    $table->timestamps();
                });
            }

            public function down()
            {
                Schema::dropIfExists('hacker_news');
            }
        }
        ```  

4. 동작 원리  
    사용자 요청: 사용자가 /hacker-news URL에 접근합니다.  
    컨트롤러 액션: HackerNewsController의 index 메서드가 호출되어 최신 기사를 데이터베이스에서 가져와 뷰에 전달합니다.  
    뷰 렌더링: resources/views/hackernews/index.blade.php 뷰 파일이 렌더링되어 사용자에게 표시됩니다.  
    자동 업데이트: jQuery를 통해 10초마다 /fetch-hacker-news URL로 AJAX 요청을 보냅니다.  
    데이터 갱신: HackerNewsController의 fetch 메서드가 호출되어 Hacker News API에서 최신 기사를 가져와 데이터베이스를 갱신합니다.  
    페이지 리로드: AJAX 요청이 성공하면 페이지를 리로드하여 최신 기사를 표시합니다.  

5. 시스템 구성 다이어그램 및 순서도  
    ```
    +---------------------+       +---------------------+
    |   Web Browser       |       |    Hacker News API  |
    |---------------------|       |---------------------|
    | - index.blade.php   | <-->  | - Top Stories       |
    | - jQuery            |       | - Item Details      |
    +---------------------+       +---------------------+
            |
            V
    +---------------------+
    |  Laravel Backend    |
    |---------------------|
    | - Routes            |
    | - Controllers       |
    | - Models            |
    | - Views             |
    +---------------------+
            |
            V
    +---------------------+
    |   SQLite Database   |
    |---------------------|
    | - hacker_news table |
    +---------------------+
    ```  

    ```
    [Start]
    |
    V
    [User accesses /hacker-news]
    |
    V
    [Controller fetches articles from DB]
    |
    V
    [Render view with articles]
    |
    V
    [jQuery setInterval 10s]
    |
    V
    [AJAX request to /fetch-hacker-news]
    |
    V
    [Fetch top stories from Hacker News API]
    |
    V
    [Update DB with new articles]
    |
    V
    [Reload page]
    |
    V
    [End]
    ```  

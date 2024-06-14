<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hacker News Monitor</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: #ffffff;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
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
<div class="container">
    <h1 class="text-center">Hacker News Monitor</h1>
    <ul class="list-group">
        @foreach($articles as $article)
            <li class="list-group-item">
                <a href="{{ $article->url }}" target="_blank">{{ $article->title }}</a>
            </li>
        @endforeach
    </ul>
</div>
</body>
</html>

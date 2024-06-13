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
            <a href="{{ $article->url }}" target="_blank">{{ $article->id }} {{ $article->title }}</a>
        </li>
    @endforeach
</ul>
</body>
</html>

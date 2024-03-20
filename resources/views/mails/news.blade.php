<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily News</title>
</head>
<body>
    <h2>Daily News</h2>

    <p>Here are today's published news:</p>
    <ul>
        @foreach ($news as $item)
            <li>
                <strong>{{ $item->title }}</strong>
                <p>{{ $item->content }}</p>
            </li>
        @endforeach
    </ul>

    <p>You can check out our website for more details.</p>
</body>
</html>

<html>
<head>
    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    <style>
        body
        {
            background-color: #000;
            color: #bd1d25;
            display: table;
            font-family: 'Lato',sans-serif;
            font-weight: 100;
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .container
        {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content
        {
            text-align: center;
        }

        .content img
        {
            display: block;
            height: 76px;
            margin: 0 auto;
            width: 76px;
        }

        .title
        {
            font-size: 72px;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <img src="{{ asset('assets_web/favicon/favicon-76.png') }}" alt="" />
            <div class="title">Page not found.</div>
        </div>
    </div>
</body>
</html>

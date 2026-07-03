<html>
<head>
    <style>
        @page {
            margin-top: 130px;
        }

        body {
            font-family: 'Aptos', sans-serif;
            margin: 0;
        }

        .pdf-header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
        }
    </style>
</head>

<body>

<div class="pdf-header">
    @include('pdf.header', ['title'=>$title ?? null])
</div>

@yield('content')

</body>
</html>

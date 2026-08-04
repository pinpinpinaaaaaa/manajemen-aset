<html>
<head>
    <style>
        @font-face {
            font-family: 'Aptos';
            src: url('{{ public_path('fonts/Aptos-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Aptos';
            src: url('{{ public_path('fonts/Aptos-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }

        @page {
            margin-top: 38mm;
            margin-left: 25.4mm;
            margin-right: 25.4mm;
            margin-bottom: 25.4mm;
        }

        body {
            font-family: 'Aptos', sans-serif;
            margin: 0;
        }

        .pdf-logo-header {
            position: fixed;
            top: -18mm;
            left: 0;
        }
    </style>
</head>
<body>
    @include('pdf.header-laporan')
    @yield('content')
</body>
</html>

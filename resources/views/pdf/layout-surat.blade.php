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

        /*
         * A4: 210 × 297 mm
         * Header: 169,3 × 31,2 mm, mulai 20,3 mm dari tepi atas → berakhir 51,5 mm
         * margin-top 58 mm = 51,5 mm (header) + 6,5 mm celah ke konten
         * Footer: 81,4 mm lebar, bawah tepat 12,7 mm dari tepi bawah
         */
        @page {
            margin-top:    58mm;
            margin-bottom: 25.4mm;
            margin-left:   25.4mm;
            margin-right:  25.4mm;
        }

        body {
            font-family: 'Aptos', sans-serif;
            margin: 0;
        }

        /* Header: fixed → muncul di setiap halaman */
        .surat-header {
            position: fixed;
            top:   -37.7mm;   /* 58 - 20.3 = 37.7 mm di atas batas konten */
            left:  0;
            right: 0;
            text-align: center;
        }

        /* Footer: fixed → muncul di setiap halaman */
        .surat-footer {
            position: fixed;
            bottom: -12.7mm;  /* 12,7 mm dari tepi bawah halaman */
            left:   0;
            right:  0;
            text-align: center;
        }
    </style>
</head>
<body>
    @include('pdf.header-surat')
    @include('pdf.footer-surat')
    @yield('content')
</body>
</html>

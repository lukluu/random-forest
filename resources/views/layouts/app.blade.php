<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kopi Kita')</title>

    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'sans-serif'],
                    },
                    colors: {
                        coffee: {
                            light: '#F8F5F2',
                            /* Cream Background */
                            primary: '#8C6A4F',
                            /* Cokelat Utama */
                            dark: '#5D4037',
                            /* Cokelat Tua (Teks) */
                            accent: '#8C5E3C',
                            /* Warna Tombol */
                            accentHover: '#70482D',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Agar transisi halus di semua elemen */
        body {
            @apply antialiased;
        }
    </style>
</head>

<body class="bg-coffee-light min-h-screen flex items-center justify-center p-4">

    @yield('content')

</body>

</html>
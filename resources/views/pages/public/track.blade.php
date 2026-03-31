<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lacak Status Laporan — PDAM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/LoginApp.jsx', 'resources/js/PublicApp.jsx'])
</head>
<body class="bg-slate-50 antialiased text-slate-800 m-0 p-0">
    <script>
        window.LaravelData = {
            initialTicket: @json($ticket),
            initialCode: @json($ticketCode)
        };
    </script>
    <div id="react-track-root"></div>
</body>
</html>

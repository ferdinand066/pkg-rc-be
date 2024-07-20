<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ env('APP_NAME') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    {{-- @vite('resources/css/app.css') --}}
    <link rel="stylesheet" type="text/css" media="screen" href="{{ $css }}">
    {{-- <link rel="stylesheet" href="http://localhost:8000/build/assets/app-KbJAj8Tt.css"> --}}
</head>

@php
    $header = ["Jam Mulai Pinjam", "Nama Ruangan", "Informasi Peminjam", "Alat yang dipinjam"]   
@endphp
<body style="font-family: ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">
    <div class="max-w-7xl w-full mx-auto flex flex-col gap-4 mt-4">
        <div class="flex flex-col gap-2" style="margin: 0 1rem">
            <h1 class="text-3xl">Laporan Peminjaman Ruangan PKG RC</h1>
            <h3 class="text-xl">Selasa, 16 Juli 2024</h3>
        </div>
        <div class="flex flex-col">
            <div class="w-full">
                {{-- <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8 xl:px-0"> --}}
                    <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg"> 
                        <table id="table" class="min-w-full overflow-hidden divide-y divide-gray-200">
                            <thead style="background-color:#F9FAFB;font-family: ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">
                                <tr>
                                    @foreach($header as $index => $h)
                                        <th scope="col"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase cursor-pointer items-center">
                                            {{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="overflow-y-auto">
                                @forelse($borrowedRooms as $index => $borrowedRoom)
                                    <tr style={{ $index % 2 === 0 ? "background-color:white" : "background-color:#F3F4F6" }}>
                                        <td class="px-6 py-4 text-sm whitespace-wrap text-gray-800 w-16 text-center" style="padding:0.5rem">
                                            {{ $borrowedRoom->start_borrowing_time }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-wrap text-gray-800 w-20" style="padding:0.5rem">
                                            {{ $borrowedRoom->room->name . " (" . $borrowedRoom->room->floor->name . ")"}}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-wrap text-gray-800 w-32" style="padding:0.5rem">
                                            {{ $borrowedRoom->pic_name . " (" . $borrowedRoom->pic_phone_number . ")"}}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-wrap text-gray-800" style="padding:0.5rem">
                                            <div style="display:flex;flex-direction:column">
                                                @forelse($borrowedRoom->borrowedRoomItems as $item)
                                                    <span>{{ $item->item->name . ': ' . $item->quantity }}</span>
                                                    <br>
                                                @empty
                                                    -
                                                @endforelse
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr style= "background-color:#F3F4F6">
                                        <td colspan="4" class="text-center p-2">Tidak ada peminjaman ruang hari ini!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            {{--  </div> --}}
        </div>
    </div>
</body>

</html>

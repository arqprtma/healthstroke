<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- cdn tailwindcss --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Place the first <script> tag in your HTML's <head> -->
    {{-- <script src="https://cdn.tiny.cloud/1/n5fwf9rl9zha92sitq4ifncibtnjo2h6y1mdfwew55yurj1x/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> --}}

    <title>{{ $title }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
     {{-- font --}}
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@200;300;400&display=swap" rel="stylesheet">

     <style>
         *{
             font-family: 'mulish','sans-serif';
         }
     </style>

     <!-- CSS CKEditor 5 -->
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.css" />
</head>

<body class="bg-[#F8F8FF]">
    <div class="bg-auto bg-cover bg-left-top bg-no-repeat -mt-4 lg:h-[356px] h-[300px]" id="bg-blub">
        <div class="container lg:w-[80%] mx-auto pt-24 pb-7 px-5">
            <a href="{{ route('admin.index') }}" class="flex gap-2">
                <img src="{{ asset('images/icons/arrow-left.png ') }}" width="30px" alt="Back Arrow">
            </a>
        </div>
        <div class="container lg:w-[80%] mx-auto pb-7 px-5">
            <h2 class="text-center font-bold text-black lg:text-2xl text-lg mb-10">Tambah Artikel</h2>
        </div>
    </div>
    <form method="post" action="{{ route('admin.artikel.store') }}" class="container lg:w-[80%] mx-auto pb-7 px-5 lg:-mt-20 -mt-24" enctype="multipart/form-data">
        @csrf
        <div class="lg:w-[80%] w-full rounded-md shadow-md bg-[#F2F2F2] p-3 lg:mx-auto">
            <div class="mb-3">
                <label for="judul" class="text-base">Judul</label>
                <input type="text" name="judul" id="judul" placeholder="Masukan judul" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline lg:text-base text-sm mt-2">
            </div>
            <div class="mb-3">
                <label for="cover" class="text-base d-block">Cover</label><br>
                <input type="file" id="fileInput" name="fileInput" class="mt-1 p-2 border border-gray-300 rounded-md d-block">
            </div>
            <div>
                <label for="kat_artikel" class="text-base">Kategori</label><br>
                <div class="relative inline-block w-full mt-1 mb-2" id="kat_artikel">
                    <select name="kat_artikel" id="kat_artikel" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline lg:text-base text-sm mt-2">
                        <option value="" disabled selected>Pilih Kategori</option>
                        @if($kat_artikel)
                            @foreach ($kat_artikel as $data)
                                <option value="{{ $data->id_kat_artikel }}">{{ $data->nama }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="pointer-events-none absolute top-1/2 right-0 transform -translate-x-1/2 translate-y-0 px-1">
                        <svg class="fill-current w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M14.293 5.293a1 1 0 0 0-1.414 0L10 8.586 6.707 5.293a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.414 0l4-4a1 1 0 0 0 0-1.414z"/></svg>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <label for="deskkripsi" class="text-base">Deskripsi</label>
                <!-- Tiny TextArea -->
                {{-- <script>
                    tinymce.init({
                        selector: 'textarea',
                        plugins: 'tinycomments mentions anchor autolink charmap codesample emoticons link lists searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                        tinycomments_mode: 'embedded',
                        tinycomments_author: 'Agung Dwi Sahputra',
                        mergetags_list: [
                        { value: 'First.Name', title: 'First Name' },
                        { value: 'agungdwisahputra@gmail.com', title: 'agungdwisahputra@gmail.com' },
                        ],
                    });
                </script> --}}
                <textarea class="text-base mt-1 mb-2" name="deskripsi" id="deskripsi"></textarea>
                @error('deskripsi')
                    <span class="text-red-700 text-xs">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class=""><button type="submit" class="px-10 py-2 bg-[#15ADA7] hover:bg-[#13A29C] text-white rounded mx-auto block mt-5">Simpan</button></div>
    </form>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('ckeditor.ckeditor5')
    <script>
        $(document).ready(function(){
            CKEDITOR5('#deskripsi');
        });
    </script>

    <!-- Responsive background Image -->
    <script>
        if ($(window).width() < 768) {
            $('#bg-blub').css('background-image', 'url("/images/bg-mobile.png")');
            $('#bg-blub').css('background-position', 'center -50px');
        } else if($(window).width() < 992) {
            $('#bg-blub').css('background-image', 'url("/images/bg-tablet.png")');
            $('#bg-blub').css('background-position', 'center -20px');
        } else {
            $('#bg-blub').css('background-image', 'url("/images/bg-desktop.png")');
            $('#bg-blub').css('background-position', 'center -20px');
        }
    </script>

{{-- ALERT --}}
    @if(session()->has('error'))
    <script>
        var pesan = "{{ session('error') }}"

        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: pesan
        });
        </script>
    @endif
    @if(session()->has('success'))
        <script>
            var pesan = "{{ session('success') }}"
            Swal.fire({
                icon: "success",
                title: "Yeayy...",
                text: pesan
            });
        </script>
    @endif
</body>

</html>

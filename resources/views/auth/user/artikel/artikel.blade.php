<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- cdn tailwindcss --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Artikel - Health Stroke</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
      {{-- font --}}
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@200;300;400&display=swap" rel="stylesheet">

    <style>
        *{
            font-family: 'mulish','sans-serif';
        }
        .active {
            display: block;
        }

        .inactive {
            display: none;
        }
    </style>
</head>

<body class="bg-[#F8F8FF]">
    <div class="container mx-auto">
        <div class="back w-[50px] relative top-[50px]  h-[50px] left-[40px] lg:left-[120px]">
            <a href={{ 'dashboard' }}>
                <img src="images/arrow-back.png" alt="">
            </a>
        </div>
        <div class="content relative top-[50px] lg:top-[50px] w-[95%] lg:w-[80%] mx-auto">
            <h1 class="ms-10 font-bold mt-5 mb-2 lg:mb-5 text-[14px] lg:text-[24px]">Artikel untuk kamu</h1>
            <div class="judul flex justify-evenly pt-7 w-[80%] mx-auto">
                <div class="click-pemahaman cursor-pointer">
                    <h1 class="text-sm lg:text-lg text-center">Pemahaman</h1>
                    <hr class="border-pemahaman border-2 border-solid border-[#15ADA7] w-[100px] lg:w-[120px]">
                </div>
                <div class="click-pertolonganpertama cursor-pointer">
                    <h1 class="text-sm lg:text-lg">Pertolongan Pertama</h1>
                    <hr class="border-pertolongan border-2 border-solid border-[#15ADA7]">
                </div>
                <div class="click-tanda-gejala cursor-pointer">
                    <h1 class="text-sm lg:text-lg">Tanda dan Gejala</h1>
                    <hr class="border-gejala border-2 border-solid border-[#15ADA7]">
                </div>
            </div>
            <div class="berita lg:w-[100%] mx-auto px-10 mt-3">
                @foreach($list_artikel as $artikel)
                    @php
                        if($artikel->kategori == '1'){
                            $kategori = 'pemahaman';
                            $kategori_title = 'Pemahaman';
                        }elseif($artikel->kategori == '2'){
                            $kategori = 'pertolongan';
                            $kategori_title = 'Pertolongan Pertama';
                        }elseif($artikel->kategori == '3'){
                            $kategori = 'gejala';
                            $kategori_title = 'Tanda dan Gejala';
                        }else{
                            $kategori = 'pemahaman';
                            $kategori_title = 'Pemahaman';
                        }

                        $tanggal = date('d M Y', strtotime($artikel->created_at));
                    @endphp
                    <a href="{{ route('detail-artikel', ['id' => $artikel->id]) }}" class="{{ $kategori }} w-[100%] lg:w-[100%] lg:h-[200px] bg-[#FFFF] rounded-lg h-[120px] flex mt-3 justify-evenly shadow-lg relative" style="display: flex">
                        <div class="avatar w-[30%] lg:w-[30%] lg:w-[30%] flex-1.2 flex justify-center items-center">
                            <img src="/images/Logo-apps.png" alt="" class="ms-2 w-[100px] h-[100px] lg:w-[200px] lg:h-[150px]">
                        </div>
                        <div class="deskripsi flex-1 text-sm ">
                            <div class="konten">
                                <h1 class="font-bold truncate mx-5 mt-5 lg:ms-10 lg:mt-7 text-lg text-[#15ADA7]">
                                    {{ $artikel->judul }}
                                </h1>
                                <div class="ms-5 mt-1 lg:ms-10 lg:mt-1 text-md w-[70%] h-[41px] lg:h-[60px] overflow-hidden">
                                    {!! $artikel->deskripsi !!}
                                </div>
                                <p class="ms-5 lg:ms-10 lg:mt-10 text-gray-500">Admin, {{ $tanggal }}</p>
                            </div>

                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let clickPemahaman = document.querySelector(".click-pemahaman");
        let clickPertolonganPertama = document.querySelector(".click-pertolonganpertama");
        let clickTandaGejala = document.querySelector(".click-tanda-gejala");

        let pemahaman = document.querySelectorAll(".pemahaman");
        let pertolongan = document.querySelectorAll(".pertolongan");
        let gejala = document.querySelectorAll(".gejala");

        let borderPemahaman = document.querySelector(".border-pemahaman");
        let borderPertolongan = document.querySelector(".border-pertolongan");
        let borderGejala = document.querySelector(".border-gejala");

        // Tampilkan class pemahaman secara default
        showCategory(pemahaman);
        hideCategory(pertolongan);
        hideCategory(gejala);

        showBorder(borderPemahaman);
        hideBorder(borderPertolongan);
        hideBorder(borderGejala);

        clickPemahaman.addEventListener("click", function() {
            showCategory(pemahaman);
            hideCategory(pertolongan);
            hideCategory(gejala);

            // Tampilkan hr pemahaman dan sembunyikan yang lain
            showBorder(borderPemahaman);
            hideBorder(borderPertolongan);
            hideBorder(borderGejala);
        });

        clickPertolonganPertama.addEventListener("click", function() {
            hideCategory(pemahaman);
            showCategory(pertolongan);
            hideCategory(gejala);

            // Tampilkan hr pertolongan dan sembunyikan yang lain
            hideBorder(borderPemahaman);
            showBorder(borderPertolongan);
            hideBorder(borderGejala);
        });

        clickTandaGejala.addEventListener("click", function() {
            hideCategory(pemahaman);
            hideCategory(pertolongan);
            showCategory(gejala);

            // Tampilkan hr gejala dan sembunyikan yang lain
            hideBorder(borderPemahaman);
            hideBorder(borderPertolongan);
            showBorder(borderGejala);
        });

        function showCategory(category) {
            category.forEach(item => {
                item.style.display = 'flex';
            });
        }

        function hideCategory(category) {
            category.forEach(item => {
                item.style.display = 'none';
            });
        }

        function showBorder(border) {
            border.style.display = 'block';
        }

        function hideBorder(border) {
            border.style.display = 'none';
        }
    });
</script>





</html>

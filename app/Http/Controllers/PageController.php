<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\Artikel;
use App\Models\Kategori_aktivitas;
use App\Models\Kategori_artikel;
use App\Models\Kategori_penanganan;
use App\Models\Komplikasi;
use App\Models\Log_treatment;
use App\Models\Pasien;
use App\Models\Pemicu;
use App\Models\Penanganan;
use App\Models\Treatment;
use App\Models\Trigered_Aktivitas;
use App\Models\Trigered_Penanganan;
use App\Models\User;
use App\Models\UserHealth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Make sure to include this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function template_tailwind($code_access){
        if($code_access == 'esaunggul'){
            return view('template_tailwind');
        }else{
            // redirect akses di tolak
            return redirect()->route('login');
        }
    }
    public function index(Request $request) {
        $data = [
            'title' => 'Flash Screen',
        ];
        return view('welcome', $data);
    }

    public function login(Request $request) {
        $data = [
            'title' => 'Login | StrokeCare',
        ];

        return view('login', $data);
    }

    public function register(Request $request) {
        $data = [
            'title' => 'Register | StrokeCare',
        ];
        return view('register', $data);
    }

    public function forgotpass(Request $request) {
        $data = [
            'title' => 'Forgot Password | StrokeCare',
        ];
        return view('forgot-password', $data);
    }

    // Auth
    public function dashboard(Request $request) {
        // Get the current user's ID
        $userId = Auth::id();

        $pasien = Pasien::with('user')
            ->where('id_user', $userId)->first();


        // Get Artikel
        $artikel = Artikel::get();

        // Get Kategori Aktivitas
        $kat_aktivitas = Kategori_aktivitas::get();

        $today = Carbon::today(); // Get date now


        // $all_log_treatment = Log_treatment::where('id_pasien', $pasien->id_pasien)->get();
        // $log_treatment = Log_treatment::where('id_pasien', $pasien->id_pasien)->whereDate('created_at', $today)->get();
        $all_log_treatment = [];
        $log_treatment = [];
        if ($pasien) {
            $list_id_treatment = Treatment::select('id_penanganan','id_aktivitas')->where('id_pasien', $pasien->id_pasien)->first();
            $list_id_aktivitas = json_decode($list_id_treatment->id_aktivitas,true);
            $list_id_penanganan = json_decode($list_id_treatment->id_penanganan,true);
            $all_log_treatment = Log_treatment::where('id_pasien', $pasien->id_pasien)->get();
            $log_treatment = Log_treatment::where('id_pasien', $pasien->id_pasien)->whereDate('created_at', $today)->get();
        }

        $log_penanganan = [];
        $log_aktivitas = [];
        foreach ($log_treatment as $key => $data) {
            if($data->id_penanganan != null){
                if(in_array($data->id_penanganan, $list_id_penanganan)){
                    $log_penanganan[] = $data->id_penanganan;
                }
            }else{
                if(in_array($data->id_aktivitas, $list_id_aktivitas)){
                    $log_aktivitas[] = $data->id_aktivitas;
                }
            }
        }


        $kat_penanganan = Kategori_penanganan::get();

        $treatment = null;
        $aktivitas = null;
        $penanganan = null;
        $aktivitasId = [];
        $penangananId = [];
        $list_aktivitas = [];
        $list_penanganan = [];
        if ($pasien) {
            $treatment = Treatment::where('id_pasien', $pasien->id_pasien)->first();
            if ($treatment) {
                $aktivitasId = json_decode($treatment->id_aktivitas);
            }
            if ($treatment) {
                $penangananId = json_decode($treatment->id_penanganan);
            }

        }



        // Get Aktivitas Data
        $aktivitas = Aktivitas::with('pemicu','komplikasi','kategori_aktivitas')->whereIn('id_aktivitas', $aktivitasId)->get();
        $penanganan = Penanganan::with('pemicu','komplikasi','kategori_penanganan')->whereIn('id_penanganan', $penangananId)->get();

        // Menggabungkan data Aktivitas berdasarkan kategori aktivitas
        $list_aktivitas = [];
        foreach($kat_aktivitas as $key => $data_kategori){
            foreach ($aktivitas as $jkey => $data_aktivitas) {
                if($data_kategori->id_kat_aktivitas == $data_aktivitas->id_kat_aktivitas){
                    $list_aktivitas[$data_kategori->id_kat_aktivitas][] = $data_aktivitas;
                }
            }
        }
        // Menggabungkan data penanganan berdasarkan kategori penanganan
        $list_penanganan = [];
        foreach($kat_penanganan as $key => $data_kategori){
            foreach ($penanganan as $jkey => $data_penanganan) {
                if($data_kategori->id_kat_penanganan == $data_penanganan->id_kat_penanganan){
                    $list_penanganan[$data_kategori->id_kat_penanganan][] = $data_penanganan;
                }
            }
        }
        // Proses pembuatan Chart log
            // log treatment
            // $oneweekago = Carbon::today()->subWeek();

            // $log_treatmentWeek = Log_treatment::where('id_pasien', $pasien->id_pasien)
            //     ->whereBetween('created_at', [$oneweekago, $today])
            //     ->get()
            //     ->map(function ($log) { // Membuat Created_at menjadi tahun bulan tanggal, untuk jam akan default 00
            //         $log->created_at = Carbon::parse($log->created_at)->format('Y-m-d');
            //         return $log;
            //     })
            //     ->groupBy('created_at');

            // $groupTreatment = [];
            // $no = 0;
            // foreach ($log_treatmentWeek as $time) { // Merubah Index emnjadi angka
            //     foreach ($time as $data) {
            //         $groupTreatment[$no][] = $data;
            //     }
            //     $no++;
            // }
            // $dataTreatment = [];
            // foreach ($groupTreatment as $key => $data) { //Menghitung total data treatment berdasarkan hari
            //     $dataTreatment[] = count($data);
            // }
        // End Proses

        $data = [
            'title' => 'Dashboard | StrokeCare',
            'treatment' => $treatment,
            'aktivitas' => $aktivitas,
            'aktivitasId' => $aktivitasId,
            'kat_aktivitas' => $kat_aktivitas,
            'list_aktivitas' => $list_aktivitas,
            'penanganan' => $penanganan,
            'penangananId' => $penangananId,
            'kat_penanganan' => $kat_penanganan,
            'list_penanganan' => $list_penanganan,
            'log_penanganan' => $log_penanganan,
            'log_aktivitas' => $log_aktivitas,
            'all_log_treatment' => $all_log_treatment,
            // 'log_treatmentWeek' => $log_treatmentWeek,
            // 'dataTreatment' => $dataTreatment,
            'pasien' => $pasien,
            'artikel' => $artikel
        ];
        return view('auth.dashboard', $data);
    }

    public function profile(Request $request) {
        $data = [
            'title' => 'Profile | StrokeCare',
        ];
        return view('auth.profile-user', $data);
    }

    public function show_aktivitas(Request $request, $id) {
        $aktivitas  = Aktivitas::where('id_aktivitas', $id)->first();

        // Get the current user's ID
        $userId = Auth::id();
        $pasien = Pasien::with('user')->where('id_user', $userId)->first();
        $treatment = Treatment::where('id_pasien', $pasien->id_pasien)->first();
        $aktivitasId = json_decode($treatment->id_aktivitas);

        // Get Aktivitas Data
        $list_aktivitas = Aktivitas::with('pemicu','komplikasi','kategori_aktivitas')->whereIn('id_aktivitas', $aktivitasId)->whereNotIn('id_aktivitas', [$id])->get()->take(5);

        $today = Carbon::today();
        $log_treatment_query = Log_treatment::where([
            'id_pasien' => $pasien->id_pasien,
            'id_aktivitas' => $id
        ]);
        // Check jika ada data $log_treatment yang dibuat pada hari ini
        $log_treatment_today = (clone $log_treatment_query)->whereDate('created_at', $today)->count();
        // Dapatkan semua data $log_treatment
        $log_treatment = $log_treatment_query->get();

        // get data user health
        $userHealth = UserHealth::where(['id_pasien' => $pasien->id_pasien, 'id_treatment' => $id])->first();

        $trigerAktivitas = Trigered_Aktivitas::where('id_aktivitas' , '=', $id)->get();

        // $log_treatment = Log_treatment::where(['id_pasien' => $pasien->id_pasien, 'id_aktivitas' => $id])->count();
        $data = [
            'title' => 'Detail Aktivitas | StrokeCare',
            'pasien' => $pasien,
            'aktivitas' => $aktivitas,
            'list_aktivitas' => $list_aktivitas,
            'log_treatment' => $log_treatment,
            'log_treatment_today' => $log_treatment_today,
            'trigerAktivitas' => $trigerAktivitas,
            'userHealth' => $userHealth
        ];
        return view('auth.user.pasien.detail-aktivitas', $data);
    }

    public function show_penanganan(Request $request, $id) {
        $penanganan  = Penanganan::where('id_penanganan', $id)->first();

        // Get the current user's ID
        $userId = Auth::id();
        $pasien = Pasien::with('user')->where('id_user', $userId)->first();
        $treatment = Treatment::where('id_pasien', $pasien->id_pasien)->first();
        $penangananId = json_decode($treatment->id_penanganan);

        // Get Penanganan Data
        $list_penanganan = Penanganan::with('pemicu','komplikasi','kategori_penanganan')->whereIn('id_penanganan', $penangananId)->whereNotIn('id_penanganan', [$id])->get()->take(5);

        $today = Carbon::today();
        $log_treatment_query = Log_treatment::where([
            'id_pasien' => $pasien->id_pasien,
            'id_penanganan' => $id
        ]);
        // Check jika ada data $log_treatment yang dibuat pada hari ini
        $log_treatment_today = (clone $log_treatment_query)->whereDate('created_at', $today)->count();
        // Dapatkan semua data $log_treatment
        $log_treatment = $log_treatment_query->get();

        // get data user health
        $userHealth = UserHealth::where(['id_pasien' => $pasien->id_pasien, 'id_treatment' => $id])->first();

        $trigerPenanganan = Trigered_Penanganan::where('id_penanganan' , '=', $id)->get();

        // $log_treatment = Log_treatment::where(['id_pasien' => $pasien->id_pasien, 'id_penanganan' => $id])->count();
        $data = [
            'title' => 'Detail penanganan | StrokeCare',
            'pasien' => $pasien,
            'penanganan' => $penanganan,
            'list_penanganan' => $list_penanganan,
            'log_treatment' => $log_treatment,
            'log_treatment_today' => $log_treatment_today,
            'trigerPenanganan' => $trigerPenanganan,
            'userHealth' => $userHealth
        ];
        return view('auth.user.pasien.detail-penanganan', $data);
    }

    public function artikel(Request $request) {
        $kategori_artikel = Kategori_artikel::get();
        $list_artikel = Artikel::with('kategori_artikel')->get();

        $data = [
            'title' => 'Artikel | StrokeCare',
            'kategori_artikel' => $kategori_artikel,
            'list_artikel' => $list_artikel,
        ];
        return view('auth.user.artikel.artikel', $data);
    }

    public function show_artikel(Request $request, $id = null) {
        if($id == null){
            return back()->with('error', 'Artikel belum tersedia');
        }
        $artikel  = Artikel::with('kategori_artikel')->where('id', $id)->first();
        $data = [
            'title' => 'Overview Artikel | StrokeCare',
            'artikel' => $artikel,
        ];
        return view('auth.user.artikel.detail-artikel', $data);
    }

    // Pasien

    public function pasien(Request $request) {
        $pemicu = Pemicu::all();
        $komplikasi = Komplikasi::all();
        $data = [
            'title' => 'Pasien | StrokeCare',
            'pemicu' => $pemicu,
            'komplikasi' => $komplikasi,
        ];
        return view('auth.user.pasien.tambah-pasien', $data);
    }

    public function pasien_id(Request $request) {
        $userId = $request->id;
        $pasien = Pasien::where('id_pasien', $userId)->first();
        $pemicu = Pemicu::all();
        $komplikasi = Komplikasi::all();
        $data = [
            'title' => 'Pasien | StrokeCare',
            'pasien' => $pasien,
            'pemicu' => $pemicu,
            'komplikasi' => $komplikasi,
        ];
        return view('auth.user.pasien.update-pasien', $data);
    }


    // Admin
        public function login_admin(Request $request) {
            $data = [
                'title' => 'Login Admin | StrokeCare',
            ];


            return view('auth.admin.login', $data);
        }
        public function admin_dashboard() {
            $users = User::get();
            $pasien = Pasien::get();

            $pasien = Pasien::get();
            $arr_pemicu = [];
            foreach ($pasien as $key => $data) {
                // $arr_pemicu[] = $data->pemicu;
                $arr_pemicu = array_merge($arr_pemicu, $data->pemicu);
            }
            $counts_arr_pemicu = array_count_values($arr_pemicu); //Menghitung setiap id pemicu yang di dapat

            $total_pemicu = [];
            foreach ($counts_arr_pemicu as $key => $data) {
                $total_pemicu[] = $data;
            }

            $pemicu = Pemicu::get();
            $list_pemicu = $pemicu->pluck('nama')->toArray();

            $data = [
                'title' => 'Dashboard Admin | StrokeCare',
                'users' => $users,
                'pasien' => $pasien,
                'counts_arr_pemicu' => $counts_arr_pemicu,
                'total_pemicu' => $total_pemicu,
                'list_pemicu' => $list_pemicu,
            ];

            return view('auth.admin.index', $data);
        }
        public function admin_pemicu() {
            $pemicu = Pemicu::select('id_pemicu','nama')->get();

            $data = [
                'title' => 'Pemicu Admin | StrokeCare',
                'pemicu' => $pemicu,
            ];

            return view('auth.admin.pemicu.index', $data);
        }
        public function admin_komplikasi() {
            $komplikasi = Komplikasi::select('id_komplikasi','nama')->get();

            $data = [
                'title' => 'Komplikasi Admin | StrokeCare',
                'komplikasi' => $komplikasi,
            ];

            return view('auth.admin.komplikasi.index', $data);
        }
        public function admin_aktivitas() {
            $aktivitas = Aktivitas::with('pemicu','komplikasi','kategori_aktivitas')->get();

            $data = [
                'title' => 'Aktivitas Admin | StrokeCare',
                'aktivitas' => $aktivitas,
            ];

            return view('auth.admin.aktivitas.index', $data);
        }
        public function admin_tambah_aktivitas() {
            $kat_aktivitas = Kategori_aktivitas::select('id_kat_aktivitas','nama')->get();
            $pemicu = Pemicu::select('id_pemicu','nama')->get();
            $komplikasi = Komplikasi::select('id_komplikasi','nama')->get();

            $data = [
                'title' => 'Tambah Aktivitas | StrokeCare',
                'kat_aktivitas' => $kat_aktivitas,
                'pemicu' => $pemicu,
                'komplikasi' => $komplikasi,
            ];

            return view('auth.admin.aktivitas.aktivitas', $data);
        }
        public function admin_tambah_kategori_aktivitas() {
            $data = [
                'title' => 'Tambah Kategori Aktivitas | StrokeCare',
            ];

            return view('auth.admin.aktivitas.kategori_aktivitas', $data);
        }
        public function admin_edit_aktivitas($id) {
            $aktivitas = Aktivitas::where('id_aktivitas', $id)->first();

            $kat_aktivitas = Kategori_aktivitas::select('id_kat_aktivitas','nama')->get();
            $pemicu = Pemicu::select('id_pemicu','nama')->get();
            $komplikasi = Komplikasi::select('id_komplikasi','nama')->get();

            $data = [
                'title' => 'Edit Aktivitas | StrokeCare',
                'aktivitas' => $aktivitas,
                'kat_aktivitas' => $kat_aktivitas,
                'pemicu' => $pemicu,
                'komplikasi' => $komplikasi,
            ];

            return view('auth.admin.aktivitas.edit_aktivitas', $data);
        }

        // Penanganan
        public function admin_penanganan() {
            $penanganan = Penanganan::with('pemicu','komplikasi','kategori_penanganan')->get();

            $data = [
                'title' => 'Penanganan Admin | StrokeCare',
                'penanganan' => $penanganan,
            ];

            return view('auth.admin.penanganan.index', $data);
        }
        public function admin_tambah_penanganan() {
            $kat_penanganan = Kategori_penanganan::select('id_kat_penanganan','nama')->get();
            $pemicu = Pemicu::select('id_pemicu','nama')->get();
            $komplikasi = Komplikasi::select('id_komplikasi','nama')->get();

            $data = [
                'title' => 'Tambah penanganan | StrokeCare',
                'kat_penanganan' => $kat_penanganan,
                'pemicu' => $pemicu,
                'komplikasi' => $komplikasi,
            ];

            return view('auth.admin.penanganan.penanganan', $data);
        }
        public function admin_tambah_kategori_penanganan() {
            $data = [
                'title' => 'Tambah Kategori Penanganan | StrokeCare',
            ];

            return view('auth.admin.penanganan.kategori_penanganan', $data);
        }
        public function admin_edit_penanganan($id) {
            $penanganan = Penanganan::where('id_penanganan', $id)->first();

            $kat_penanganan = Kategori_penanganan::select('id_kat_penanganan','nama')->get();
            $pemicu = Pemicu::select('id_pemicu','nama')->get();
            $komplikasi = Komplikasi::select('id_komplikasi','nama')->get();

            $data = [
                'title' => 'Edit Penanganan | StrokeCare',
                'penanganan' => $penanganan,
                'kat_penanganan' => $kat_penanganan,
                'pemicu' => $pemicu,
                'komplikasi' => $komplikasi,
            ];

            return view('auth.admin.penanganan.edit_penanganan', $data);
        }
        public function admin_artikel() {
            $artikel = Artikel::with('kategori_artikel')->get();
            $data = [
                'title' => 'Artikel Admin | StrokeCare',
                'artikel' => $artikel,
            ];

            return view('auth.admin.artikel.index', $data);
        }
        public function admin_tambah_artikel() {
            $kat_artikel = Kategori_artikel::select('id_kat_artikel','nama')->get();

            $data = [
                'title' => 'Tambah penanganan | StrokeCare',
                'kat_artikel' => $kat_artikel,
            ];

            return view('auth.admin.artikel.artikel', $data);
        }
        public function admin_edit_artikel($id) {
            $artikel = Artikel::where('id', $id)->first();

            $kat_artikel = Kategori_artikel::select('id_kat_artikel','nama')->get();

            $data = [
                'title' => 'Edit Artikel | StrokeCare',
                'artikel' => $artikel,
                'kat_artikel' => $kat_artikel,
            ];

            return view('auth.admin.artikel.edit_artikel', $data);
        }

        public function trigered_aktivitas(){
            $trigeredaktivitas = Trigered_Aktivitas::join('aktivitas', 'aktivitas.id_aktivitas', '=', 'trigered_aktivitas.id_aktivitas')
            ->select('aktivitas.deskripsi', 'trigered_aktivitas.judul', 'trigered_aktivitas.level', 'trigered_aktivitas.konten','trigered_aktivitas.kemajuan', 'trigered_aktivitas.kemunduran', 'trigered_aktivitas.id_trigered_aktivitas')
            ->get();

            $data = [
                'title' => 'Tambah Trigered Aktivitas | StrokeCare',
                'trigeredaktivitas' => $trigeredaktivitas
            ];
            return view('auth.admin.trigered_aktivitas.index',$data);
        }

        public function trigered_tambah_aktivitas(){
            $aktivitas = Aktivitas::all();
            $pemicu = Pemicu::select('id_pemicu','nama')->get();
            $komplikasi = Komplikasi::select('id_komplikasi','nama')->get();

            $data = [
                'title' => 'Tambah Trigered aktivitas | StrokeCare',
                'aktivitas' => $aktivitas,
                'pemicu' => $pemicu,
                'komplikasi' => $komplikasi,
            ];
            return view('auth.admin.trigered_aktivitas.store', $data);
        }

        public function trigered_edit_aktivitas($id){
            $trigerAktivitas = Trigered_Aktivitas::join('aktivitas', 'aktivitas.id_aktivitas', '=', 'trigered_aktivitas.id_aktivitas')
            ->select('aktivitas.deskripsi', 'trigered_aktivitas.*')
            ->where('trigered_aktivitas.id_trigered_aktivitas', $id)
            ->first();

            $aktivitas = Aktivitas::select('id_aktivitas','deskripsi')->get();
            $data = [
                'title' => 'Edit Trigered aktivitas | StrokeCare',
                'trigerAktivitas' => $trigerAktivitas,
                'aktivitas' => $aktivitas
            ];

            return view('auth.admin.trigered_aktivitas.edit', $data);
        }

        public function trigered_penanganan(){
            $trigeredpenanganan = Trigered_Penanganan::join('penanganan', 'penanganan.id_penanganan', '=', 'trigered_penanganan.id_penanganan')
            ->select('penanganan.deskripsi', 'trigered_penanganan.judul', 'trigered_penanganan.level', 'trigered_penanganan.konten','trigered_penanganan.kemajuan', 'trigered_penanganan.kemunduran', 'trigered_penanganan.id_trigered_penanganan')
            ->get();

            $data = [
                'title' => 'Tambah Trigered penanganan | StrokeCare',
                'trigeredpenanganan' => $trigeredpenanganan
            ];
            return view('auth.admin.trigered_penanganan.index',$data);
        }

        public function trigered_tambah_penanganan(){
            $penanganan = penanganan::all();
            $pemicu = Pemicu::select('id_pemicu','nama')->get();
            $komplikasi = Komplikasi::select('id_komplikasi','nama')->get();

            $data = [
                'title' => 'Tambah Trigered penanganan | StrokeCare',
                'penanganan' => $penanganan,
                'pemicu' => $pemicu,
                'komplikasi' => $komplikasi,
            ];
            return view('auth.admin.trigered_penanganan.store', $data);
        }

        public function trigered_edit_penanganan($id){
            $trigerPenanganan = Trigered_Penanganan::join('penanganan', 'penanganan.id_penanganan', '=', 'trigered_penanganan.id_penanganan')
            ->select('penanganan.deskripsi', 'trigered_penanganan.*')
            ->where('trigered_penanganan.id_trigered_penanganan', $id)
            ->first();

            $penanganan = penanganan::select('id_penanganan','deskripsi')->get();
            $data = [
                'title' => 'Edit Trigered penanganan | StrokeCare',
                'trigerPenanganan' => $trigerPenanganan,
                'penanganan' => $penanganan
            ];

            return view('auth.admin.trigered_penanganan.edit', $data);
        }


        // End Admin
    }

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\Admin\UserAddRequest;
use App\Http\Requests\Admin\UserEditRequest;
use App\Http\Requests\Admin\UserChangePassRequest;
use App\Imports\UsersImport;
use DataTables;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function json()
    {
        $data = User::select(['id', 'email', 'username', 'npm', 'name'])->orderBy('npm','asc');

        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserAddRequest $request)
    {
        // dd($request->all());
        $data = $request->all();
        $data['password'] = bcrypt($data['password']);

        if (User::create($data)) {
            $request->session()->flash('alert-success', 'User ' . $data['name'] . ' berhasil ditambahkan');
        } else {
            $request->session()->flash('alert-failed', 'User ' . $data['name'] . ' gagal ditambahkan');
        }

        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = User::select('id', 'name', 'email', 'npm')->where('id', $id)->first();

        return view('pages.admin.user.edit', [
            'item'  => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserEditRequest $request, $id)
    {
        $data = $request->all();
        $item = User::findOrFail($id);

        if ($item->update($data)) {
            $request->session()->flash('alert-success', 'User ' . $data['name'] . ' berhasil diupdate');
        } else {
            $request->session()->flash('alert-failed', 'User ' . $data['name'] . ' gagal diupdate');
        }

        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = User::findOrFail($id);

        if ($item->delete()) {
            session()->flash('alert-success', 'User ' . $item->name . ' berhasil dihapus!');
        } else {
            session()->flash('alert-failed', 'User ' . $item->name . ' gagal dihapus');
        }

        return redirect()->route('user.index');
    }

    public function change_pass($id)
    {
        $item = User::select('id', 'name')->where('id', $id)->first();

        return view('pages.admin.user.change-pass', [
            'item'  => $item
        ]);
    }

    public function update_pass(UserChangePassRequest $request, $id)
    {
        $data['password'] = bcrypt($request->input('password'));

        $item = User::findOrFail($id);

        if ($item->update(['password' => $data['password']])) {
            session()->flash('alert-success', 'Password User ' . $item->name . ' berhasil diupdate');
        } else {
            session()->flash('alert-failed', 'Password User ' . $item->name . ' gagal diupdate');
        }

        return redirect()->route('user.index');
    }

    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type'=> 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_user.csv"'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            //header kolom
            fputcsv($file, ['email', 'username', 'npm', 'name']);
            //contoh baris data
            fputcsv($file, ['user@example.com', 'user1', '10050021234', 'Mahasiswa 1']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function showImportForm()
    {
        return view('user.index');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        set_time_limit(300);

        
        try {
            $file = $request->file('file');
            Log::info('Import file original name: ' . $file->getClientOriginalName());

            Excel::import(new UsersImport, $file);

            Log::info('Import finished successfully');
            return redirect()->route('user.index')->with('alert-success', 'Data user berhasil diimport!');
        } catch (\Exception $th) {
            Log::error('Import failed: ' . $th->getMessage());
            return redirect()->route('user.index')->with('error', 'Terjadi kesalahan import: ' . $th->getMessage());
        }

    }
}

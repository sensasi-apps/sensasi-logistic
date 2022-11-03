<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\materials_model;

class materials extends Controller
{
    private function random(){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function index(){
        $data['material'] = materials_model::all();

        return view('index', $data);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'unit' => 'required',
            'tags_json' => 'required',
        ]);

        $materi = new materials_model();
        $materi->code = $this->random();
        $materi->name = $request->name;
        $materi->unit = $request->unit;
        $materi->tags_json = $request->tags_json;
        $materi->save();

        return redirect(route('index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);

    }

    public function update(Request $request){
        $request->validate([
            'name' => 'required',
            'unit' => 'required',
            'tags_json' => 'required',
        ]);

        $materi = materials_model::find($request->id);
        $materi->name = $request->name;
        $materi->unit = $request->unit;
        $materi->tags_json = "$request->tags_json".","."$materi->tags_json";
        $materi->update();

        return redirect(route('index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);

    }

    public function delete(Request $request){
        $request->validate([
            'id' => 'required'
        ]);

        materials_model::where('id', $request->id)->delete();

        return redirect(route('index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);
    }

    public function tags_delete(Request $request){
        $request->validate([
            'id' => 'required',
            'tags' => 'required',
        ]);

        $material = materials_model::where('id', $request->id)->first();
        $materialTags=explode(',',$material->tags_json);
        $mat = explode(',',$request->tags);
        $sisa_tag = array_diff($materialTags, $mat);
        $hasil_tag = implode(',', $sisa_tag);
        
        $material->tags_json = $hasil_tag;
        $material->update();

        return redirect(route('index'))->with('message', [
          'class' => 'success',
          'text' => 'Berhasil menambah riwayat pendidikan'
        ]);
    }
}

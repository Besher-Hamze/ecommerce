<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $settings=Setting::all();
        return view('dashboard.setting.index',compact('settings'));
    }
	 public function create()
    {    
        return view('dashboard.setting.create');
    }
	public function store(Request $request)
	{
		
		Setting::updateOrCreate(['key' => 'meta_title'], [
            'value' => $request['meta_title']
        ]);
		Setting::updateOrCreate(['key' => 'meta_description'], [
            'value' => $request['meta_description']
        ]);
		Setting::updateOrCreate(['key' => 'meta_keyword'], [
            'value' => $request['meta_keyword']
        ]);
		Setting::updateOrCreate(['key' => 'owner'], [
            'value' => $request['owner']
        ]);
		Setting::updateOrCreate(['key' => 'address'], [
            'value' => $request['address']
        ]);
		Setting::updateOrCreate(['key' => 'coordinates'], [
            'value' => $request['coordinates']
        ]);
		Setting::updateOrCreate(['key' => 'email'], [
            'value' => $request['email']
        ]);
		Setting::updateOrCreate(['key' => 'wats_phone'], [
            'value' => $request['wats_phone']
        ]);
		
		Setting::updateOrCreate(['key' => 'const_phone'], [
            'value' => $request['const_phone']
        ]);		
		if ($request->hasFile('image')) {
         $file = $request->file('image');
         $ext = $file->getClientOriginalExtension();
         $filename = time() . '.' . $ext;
         $file->move('img/logo/', $filename);
		Setting::updateOrCreate(['key' => 'image'], [
            'value' => $filename]);
		
      }
	
        Setting::updateOrCreate(['key' => 'name'], [
            'value' => $request['name']
        ]);
		Setting::updateOrCreate(['key' => 'work_time'], [
            'value' => $request['work_time']
        ]);
		Setting::updateOrCreate(['key' => 'about'], [
            'value' => $request['about']
        ]);
		
		return response()->json(['msg'=>'تمت العملية بنجاح' , 'success' => true]);

	}
	public function update()
	{
		return view('dashboard.setting.update');
	}
	public function update_save(Request $request)
	{
		
		Setting::updateOrCreate(['key' => 'meta_title'], [
            'value' => $request['meta_title']
        ]);
		Setting::updateOrCreate(['key' => 'meta_description'], [
            'value' => $request['meta_description']
        ]);
		Setting::updateOrCreate(['key' => 'meta_keyword'], [
            'value' => $request['meta_keyword']
        ]);
		Setting::updateOrCreate(['key' => 'owner'], [
            'value' => $request['owner']
        ]);
		Setting::updateOrCreate(['key' => 'address'], [
            'value' => $request['address']
        ]);
		Setting::updateOrCreate(['key' => 'coordinates'], [
            'value' => $request['coordinates']
        ]);
		Setting::updateOrCreate(['key' => 'email'], [
            'value' => $request['email']
        ]);
		Setting::updateOrCreate(['key' => 'wats_phone'], [
            'value' => $request['wats_phone']
        ]);
		
		Setting::updateOrCreate(['key' => 'const_phone'], [
            'value' => $request['const_phone']
        ]);		if ($request->hasFile('image')) {
         $file = $request->file('image');
         $ext = $file->getClientOriginalExtension();
         $filename = time() . '.' . $ext;
         $file->move('img/logo/', $filename);
		Setting::updateOrCreate(['key' => 'image'], [
            'value' => $filename]);
		
      }
	
	
		
        Setting::updateOrCreate(['key' => 'name'], [
            'value' => $request['name']
        ]);
		Setting::updateOrCreate(['key' => 'work_time'], [
            'value' => $request['work_time']
        ]);
		Setting::updateOrCreate(['key' => 'about'], [
            'value' => $request['about']
        ]);
		
		return response()->json(['msg'=>'تمت العملية بنجاح' , 'success' => true]);

	}
	
}

<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');

        return view('dashboard.setting.index', compact('settings'));
    }

    public function create()
    {
        return view('dashboard.setting.create');
    }

    public function store(Request $request)
    {
        return $this->persist($request);
    }

    public function update()
    {
        $settings = Setting::pluck('value', 'key');

        return view('dashboard.setting.update', compact('settings'));
    }

    public function update_save(Request $request)
    {
        return $this->persist($request);
    }

    private function persist(Request $request)
    {
        $keys = [
            'meta_title', 'meta_description', 'meta_keyword', 'owner', 'address',
            'coordinates', 'email', 'wats_phone', 'const_phone', 'name', 'work_time', 'about',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
            }
        }

        if ($request->hasFile('image')) {
            $dir = public_path('img/logo');
            File::ensureDirectoryExists($dir);

            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            Setting::updateOrCreate(['key' => 'image'], ['value' => $filename]);
        }

        Cache::forget(AppServiceProvider::SETTINGS_CACHE_KEY);

        return response()->json(['msg' => 'تمت العملية بنجاح', 'success' => true]);
    }
}

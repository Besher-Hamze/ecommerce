<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $sliders = Slider::all();
        return view('dashboard.slider.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return view('dashboard.slider.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $slider = new slider;
        $slider->text_ar = $request->text_ar;
        $slider->text_en = $request->text_en;
        $slider->url = $request->url;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('uploads/slider/', $filename);
            $slider->image = $filename;
        }
        if ($slider->save()) {
            return response()->json(['msg' => 'تمت الاضافة بنجاح', 'success' => true]);
        } else {
            return response()->json(['msg' => 'عذرا هناك خطأ في عملية الاضافة', 'success' => false]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     */
    public function update($id)
    {
        $slider = Slider::findORFail($id);
        return view('dashboard.slider.update', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider
     */
    public function update_save(Request $request,  $id)
    {
        $slider = Slider::findOrFail($id);
        $slider->text_ar = $request->text_ar;
        $slider->text_en = $request->text_en;
        $slider->url = $request->url;
        if ($request->hasFile('image')) {
            $path = 'uploads/slider/' . $slider->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('uploads/slider/', $filename);
            $slider->image = $filename;
        }
        if ($slider->update()) {
            return response()->json(['msg' => 'تم التعديل بنجاح', 'success' => true]);
        } else {
            return response()->json(['msg' => 'عذرا هناك خطأ في عملية التعديل', 'success' => false]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider
     */
    public function delete($id)
    {
        $slider = Slider::findOrFail($id);
        if ($slider->delete()) {
            return response()->json(['message' => 'تم الحذف بنجاح', 'status' => 'ok']);
        } else {
            return response()->json(['message' => 'عذرا هناك خطأ في عملية الحذف', 'status' => 'no']);
        }
    }
}

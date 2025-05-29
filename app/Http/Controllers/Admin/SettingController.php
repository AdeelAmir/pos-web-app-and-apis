<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::where('id', 1)->first();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        $Affected = null;
        $setting = Setting::where('id', 1)->first();
        $setting->tax = $request->tax;
        $Affected = $setting->save();
        if ($Affected) {
            DB::commit();
            return redirect()->route('settings')->with('success-message', 'Settings updated successfully');
        } else {
            DB::rollBack();
            return redirect()->route('settings')->with('error-message', 'An unhandled error occurred');
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SpmbSettingController extends Controller
{
    /**
     * Display the SPMB settings form.
     */
    public function edit()
    {
        return view('admin.spmb.settings');
    }

    /**
     * Update SPMB settings in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // General Operational
            'spmb_enabled' => 'nullable|in:0,1',
            'spmb_registration_fee' => 'nullable|numeric|min:0',
            'spmb_contact_phone' => 'nullable|string|max:50',
            'spmb_whatsapp' => 'nullable|string|max:50',
            'spmb_announcement_date' => 'nullable|string|max:100',
            
            // Hero Landing /spmb/info
            'spmb_hero_badge' => 'nullable|string|max:255',
            'spmb_hero_title' => 'nullable|string|max:255',
            'spmb_hero_subtitle' => 'nullable|string',
            'spmb_hero_cta_text' => 'nullable|string|max:100',
            'spmb_scholarship_info' => 'nullable|string|max:255',
            'spmb_info_text' => 'nullable|string',

            // Steps 1 to 4
            'spmb_step_1_title' => 'nullable|string|max:255',
            'spmb_step_1_desc' => 'nullable|string',
            'spmb_step_2_title' => 'nullable|string|max:255',
            'spmb_step_2_desc' => 'nullable|string',
            'spmb_step_3_title' => 'nullable|string|max:255',
            'spmb_step_3_desc' => 'nullable|string',
            'spmb_step_4_title' => 'nullable|string|max:255',
            'spmb_step_4_desc' => 'nullable|string',

            // Document Requirements 1 to 4
            'spmb_doc_req_1_title' => 'nullable|string|max:255',
            'spmb_doc_req_1_desc' => 'nullable|string',
            'spmb_doc_req_2_title' => 'nullable|string|max:255',
            'spmb_doc_req_2_desc' => 'nullable|string',
            'spmb_doc_req_3_title' => 'nullable|string|max:255',
            'spmb_doc_req_3_desc' => 'nullable|string',
            'spmb_doc_req_4_title' => 'nullable|string|max:255',
            'spmb_doc_req_4_desc' => 'nullable|string',
        ]);

        $keys = [
            'spmb_enabled',
            'spmb_registration_fee',
            'spmb_contact_phone',
            'spmb_whatsapp',
            'spmb_announcement_date',
            'spmb_hero_badge',
            'spmb_hero_title',
            'spmb_hero_subtitle',
            'spmb_hero_cta_text',
            'spmb_scholarship_info',
            'spmb_info_text',
            'spmb_step_1_title',
            'spmb_step_1_desc',
            'spmb_step_2_title',
            'spmb_step_2_desc',
            'spmb_step_3_title',
            'spmb_step_3_desc',
            'spmb_step_4_title',
            'spmb_step_4_desc',
            'spmb_doc_req_1_title',
            'spmb_doc_req_1_desc',
            'spmb_doc_req_2_title',
            'spmb_doc_req_2_desc',
            'spmb_doc_req_3_title',
            'spmb_doc_req_3_desc',
            'spmb_doc_req_4_title',
            'spmb_doc_req_4_desc',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return back()->with('success', 'Pengaturan SPMB & Landing Page berhasil diperbarui!');
    }
}

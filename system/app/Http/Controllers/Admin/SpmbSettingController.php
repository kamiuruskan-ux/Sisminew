<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SpmbSettingController extends Controller
{
    public function edit()
    {
        $activeWave = \App\Models\Wave::with('academicYear')->where('status', 'active')->first() 
            ?? \App\Models\Wave::with('academicYear')->latest()->first();
        $spmbAcademicYear = $activeWave?->academicYear?->name 
            ?? \App\Models\AcademicYear::where('is_active', true)->value('name')
            ?? (date('Y') . '/' . (date('Y') + 1));
        $spmbAcademicYear = str_replace('-', '/', $spmbAcademicYear);

        return view('admin.spmb.settings', compact('spmbAcademicYear', 'activeWave'));
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
            'spmb_hero_consult_text' => 'nullable|string|max:100',
            'spmb_scholarship_info' => 'nullable|string|max:255',
            'spmb_hero_feature_1' => 'nullable|string|max:150',
            'spmb_hero_feature_2' => 'nullable|string|max:150',
            'spmb_hero_feature_3' => 'nullable|string|max:150',
            'spmb_info_text' => 'nullable|string',

            // Right Hero Card
            'spmb_card_badge' => 'nullable|string|max:100',
            'spmb_card_title' => 'nullable|string|max:150',
            'spmb_card_cta_text' => 'nullable|string|max:100',

            // Wave Section
            'spmb_wave_badge' => 'nullable|string|max:100',
            'spmb_wave_title' => 'nullable|string|max:150',
            'spmb_wave_subtitle' => 'nullable|string',

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
            'spmb_hero_consult_text',
            'spmb_scholarship_info',
            'spmb_hero_feature_1',
            'spmb_hero_feature_2',
            'spmb_hero_feature_3',
            'spmb_card_badge',
            'spmb_card_title',
            'spmb_card_cta_text',
            'spmb_wave_badge',
            'spmb_wave_title',
            'spmb_wave_subtitle',
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

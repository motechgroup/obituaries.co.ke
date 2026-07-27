<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Obituary;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class ObituarySeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        $samples = [
            [
                'full_name' => 'Mzee Joseph Kiprop Cheruiyot',
                'slug' => 'mzee-joseph-kiprop-cheruiyot',
                'photo' => null,
                'date_of_birth' => '1945-03-12',
                'date_of_death' => '2026-07-15',
                'county' => 'Uasin Gishu',
                'town' => 'Eldoret',
                'biography' => 'Mzee Joseph Kiprop Cheruiyot was a devoted father, Community elder, and retired senior educator who impacted generations of students across Rift Valley. He passed away peacefully surrounded by family.',
                'funeral_date' => '2026-08-01',
                'burial_location' => 'Family Farm, Kapseret, Eldoret',
                'church_service_location' => 'AIC Milimani, Eldoret',
                'programme_file' => null,
                'submitter_name' => 'David Cheruiyot',
                'submitter_phone' => '0712345678',
                'submitter_email' => 'david.cheruiyot@example.com',
                'relationship' => 'Child',
                'family_permission_confirmed' => true,
                'status' => 'published',
                'verification_status' => 'verified',
                'verification_notes' => 'Verified with son via phone confirmation.',
                'verified_by' => $admin?->id,
                'verified_at' => now(),
            ],
            [
                'full_name' => 'Dr. Grace Wambui Njenga',
                'slug' => 'dr-grace-wambui-njenga',
                'photo' => null,
                'date_of_birth' => '1962-08-20',
                'date_of_death' => '2026-07-20',
                'county' => 'Nairobi',
                'town' => 'Westlands',
                'biography' => 'Dr. Grace Wambui Njenga was a pioneer healthcare professional and researcher at Kenyatta National Hospital. Known for her boundless compassion, wisdom, and leadership in public health.',
                'funeral_date' => '2026-08-05',
                'burial_location' => 'Limuru Memorial Gardens, Kiambu',
                'church_service_location' => 'All Saints Cathedral, Nairobi',
                'programme_file' => null,
                'submitter_name' => 'Dr. Peter Njenga',
                'submitter_phone' => '0722998877',
                'submitter_email' => 'peter.njenga@example.com',
                'relationship' => 'Spouse',
                'family_permission_confirmed' => true,
                'status' => 'published',
                'verification_status' => 'verified',
                'verification_notes' => 'Verified with spouse Peter Njenga.',
                'verified_by' => $admin?->id,
                'verified_at' => now(),
            ],
            [
                'full_name' => 'Mama Mary Achieng Otieno',
                'slug' => 'mama-mary-achieng-otieno',
                'photo' => null,
                'date_of_birth' => '1950-11-04',
                'date_of_death' => '2026-07-22',
                'county' => 'Kisumu',
                'town' => 'Kisumu Central',
                'biography' => 'Mama Mary Achieng Otieno lived a remarkable life centered on faith, family, and community empowerment. She leaves behind a cherished legacy of love, unity, and strength.',
                'funeral_date' => '2026-08-08',
                'burial_location' => 'Kombewa Village, Seme, Kisumu',
                'church_service_location' => 'St. Stephen’s Cathedral, Kisumu',
                'programme_file' => null,
                'submitter_name' => 'Susan Otieno',
                'submitter_phone' => '0733112233',
                'submitter_email' => 'susan.o@example.com',
                'relationship' => 'Child',
                'family_permission_confirmed' => true,
                'status' => 'pending_verification',
                'verification_status' => 'pending',
                'verification_notes' => 'Awaiting call confirmation from family representative.',
                'verified_by' => null,
                'verified_at' => null,
            ],
        ];

        foreach ($samples as $sample) {
            $obituary = Obituary::create($sample);

            Payment::create([
                'obituary_id' => $obituary->id,
                'phone_number' => '254712345678',
                'amount' => 500.00,
                'merchant_request_id' => 'MR-' . uniqid(),
                'checkout_request_id' => 'CR-' . uniqid(),
                'mpesa_receipt_number' => 'QGH' . rand(1000000, 9999999),
                'status' => 'completed',
                'result_code' => '0',
                'result_desc' => 'The service request is processed successfully.',
            ]);
        }
    }
}

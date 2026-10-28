<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LicenseController extends Controller
{
    public function index()
    {
        $licenses = License::latest()->paginate(15);

        return view('admin.licenses.index', compact('licenses'));
    }

    public function create()
    {
        return view('admin.licenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pharmacy_name' => ['required', 'string', 'max:150'],
            'pharmacy_address' => ['nullable', 'string', 'max:255'],
            'pharmacy_phone' => ['nullable', 'string', 'max:30'],
            'installation_date' => ['required', 'date'],
            'maintenance_expires_at' => [
                'nullable',
                'date',
                'after_or_equal:installation_date',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'admin_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')
                ->store('pharmacy-logos', 'public');
        }

        try {
            DB::transaction(function () use ($data, $logoPath) {
                $license = License::create([
                    'pharmacy_name' => $data['pharmacy_name'],
                    'pharmacy_address' => $data['pharmacy_address'] ?? null,
                    'pharmacy_phone' => $data['pharmacy_phone'] ?? null,
                    'installation_date' => $data['installation_date'],
                    'maintenance_expires_at' =>
                        $data['maintenance_expires_at'] ?? null,
                    'license_key' => 'DSS-' . strtoupper(Str::random(16)),
                    'status' => 'active',
                    'logo' => $logoPath,
                ]);

                User::create([
                    'name' => $data['admin_name'],
                    'email' => $data['admin_email'],
                    'password' => $data['admin_password'],
                    'role' => 'admin',
                    'license_id' => $license->id,
                ]);
            });
        } catch (\Throwable $e) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.licenses.index')
            ->with(
                'success',
                'Pharmacie, logo et compte administrateur créés avec succès.'
            );
    }

    public function edit(License $license)
    {
        return view('admin.licenses.edit', compact('license'));
    }

    public function update(Request $request, License $license)
    {
        $data = $request->validate([
            'pharmacy_name' => ['required', 'string', 'max:150'],
            'pharmacy_address' => ['nullable', 'string', 'max:255'],
            'pharmacy_phone' => ['nullable', 'string', 'max:30'],
            'installation_date' => ['required', 'date'],
            'maintenance_expires_at' => [
                'nullable',
                'date',
                'after_or_equal:installation_date',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'suspended', 'expired']),
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $oldLogoPath = $license->logo;
        $newLogoPath = null;

        if ($request->hasFile('logo')) {
            $newLogoPath = $request->file('logo')
                ->store('pharmacy-logos', 'public');

            $data['logo'] = $newLogoPath;
        }

        try {
            $license->update($data);
        } catch (\Throwable $e) {
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            throw $e;
        }

        if ($newLogoPath && $oldLogoPath) {
            Storage::disk('public')->delete($oldLogoPath);
        }

        return redirect()
            ->route('admin.licenses.index')
            ->with('success', 'Pharmacie et logo mis à jour avec succès.');
    }
}

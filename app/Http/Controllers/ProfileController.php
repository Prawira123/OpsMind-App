<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\TenantUpdateRequest;
use App\Http\Requests\Profile\TwoFAUpdateRequest;
use App\Http\Requests\Profile\UserUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Tenant;
use App\Services\ProfileService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

    public function index(){

        $user = Auth::user();
        $tenant = Tenant::where('user_id', $user->id)->first();

        return Inertia::render('Profile/index', [
            'status' => session('success'),
            'user' => $user,
            'tenant' => $tenant
            ]);
    }

    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function updateTenant(TenantUpdateRequest $request, ProfileService $service){

        $tenant = Tenant::where('id', Auth::user()->tenant_id)->first();

        if($tenant->id !== Auth::user()->tenant_id){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $data = $request->validated();

        if($request->hasFile('logo')){
            if($tenant->logo){
                Storage::disk('public')->delete($tenant->logo);
            }
            $file = $request->file('logo');
            $fileName = time().'-'.$file->getClientOriginalName();
            $logoPath = 'tenant_images/'.$fileName;

            $file->move(public_path('tenant_images'), $fileName);

            $data['logo'] = $logoPath;

        } else {
            unset($data['logo']);
        }

        $service->updateTenant($data);

        return redirect()->route('profiles.index')->with('success', 'Profil Tenant berhasil diubah');
    }

    public function updateUser(UserUpdateRequest $request, ProfileService $service, $id){

        $user = Auth::user();
        
        if(!$user){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $service->updateUser($request->validated());

        return redirect()->route('profiles.index')->with('success', 'Profile User berhasil diubah');
    }

    public function update2FA(TwoFAUpdateRequest $request, ProfileService $service){

        $tenant = Tenant::where('user_id', Auth::user()->id)->first();

        if($tenant->id !== Auth::user()->tenant_id){
            abort(403, 'Kamu tidak Punya Akses');
        }

        $service->update2FA($request->validated());

        return redirect()->route('profiles.index')->with('success', 'Pengaturan 2FA berhasil diubah');
    }

    
    // public function destroy(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'password' => ['required', 'current_password'],
    //     ]);

    //     $user = $request->user();

    //     Auth::logout();

    //     $user->delete();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return Redirect::to('/');
    // }
}

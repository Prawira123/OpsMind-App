<?php

namespace App\Http\Controllers;

use App\Models\TenantInvitation;
use App\Models\User;
use App\Services\TenantInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TenantInvitationController extends Controller
{
    public function __construct(public TenantInvitationService $service){}

    /**
     * Display a listing of the team members and invitations.
     */
    public function index()
    {
        $teams = $this->service->getDataInvitations();

        return Inertia::render('Invitation/Index', [
            'members' => Inertia::defer(fn () => $teams),
        ]);
    }

    public function store(Request $request){
        // dd($request->all());
        \Illuminate\Support\Facades\Log::info('TenantInvitationController@store hit', $request->all());
        try{
            $data = $request->validate([
                'email' => 'required|email',
                'role' => 'required|string'
            ]);
            
            $this->service->store($data);
            return redirect()->route('team.index')->with('success', 'Undangan berhasil dikirim');
        }catch(\Illuminate\Validation\ValidationException $v){
            \Illuminate\Support\Facades\Log::warning('Validation failed', $v->errors());
            throw $v;
        }catch(\Exception $e){
            \Illuminate\Support\Facades\Log::error('Error in TenantInvitationController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengirim undangan: ' . $e->getMessage());
        }
    }

    public function acceptInvitation($id){
        try{
            $this->service->acceptInvitation($id);
            return redirect()->route('login')->with('success', 'Undangan diterima! Silakan cek email Anda untuk detail login.');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Gagal menerima undangan');
        }
    }

    public function destroy(Request $request, $id){
        try{
            $tenantId = Auth::user()->tenant_id;
            
            // Try to find as user first
            $user = User::where('tenant_id', $tenantId)->find($id);
            if ($user) {
                if ($user->id === Auth::id()) {
                    return back()->with('error', 'Anda tidak dapat menghapus diri sendiri dari tim.');
                }
                $user->tenant_id = null;
                $user->save();
                $user->syncRoles([]);
                return redirect()->route('team.index')->with('success', 'Anggota berhasil dihapus dari tim.');
            }

            // Fallback to invitation
            $invitation = TenantInvitation::where('tenant_id', $tenantId)->find($id);
            if ($invitation) {
                $invitation->delete();
                return redirect()->route('team.index')->with('success', 'Undangan berhasil dibatalkan.');
            }

            return redirect()->route('team.index')->with('error', 'Data tidak ditemukan.');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

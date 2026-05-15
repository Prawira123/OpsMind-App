<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\TenantInvitation;
use App\Models\User;
use App\Notifications\TenantInvitationNotification;
use App\Notifications\TenantWelcomeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TenantInvitationService extends BaseService
{
    public function __construct()
    {}

    public function getDataInvitations(){
        $tenantId = Auth::user()->tenant_id;

        $members = User::where('tenant_id', $tenantId)
            ->with(['roles'])
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first() ?? 'Member',
                'is_active' => $user->is_active,
                'avatar' => $user->profile_photo_url ?? null,
                'status' => 'active',
                'joined_at' => $user->created_at->diffForHumans(),
            ]);

        $invitations = TenantInvitation::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->get()
            ->map(fn($invitation) => [
                'id' => $invitation->id,
                'name' => 'Pending Invitation',
                'email' => $invitation->email,
                'role' => $invitation->role,
                'status' => 'pending',
                'created_at' => $invitation->created_at->diffForHumans(),
            ]);

        $teams = $members->concat($invitations);

        return $teams;
    }

    /**
     * Create an invitation and send the first email.
     */
    public function store($data){
        Log::info('TenantInvitationService@store hit', $data);
        return DB::transaction(function () use ($data){
            $invitation = TenantInvitation::create([
                'tenant_id' => Auth::user()->tenant_id,
                'invited_by' => Auth::user()->id,
                'email' => $data['email'],
                'role' => $data['role'],
                'token' => Str::random(32),
                'status' => 'pending',
            ]);
            Log::info('Data sudah di simpan');

            // Notify the invitee with the first email
            Notification::route('mail', $data['email'])
                ->notify(new TenantInvitationNotification(
                    $invitation->id,
                    Auth::user()->tenant->name,
                    $data['role'],
                    $data['email']
                ));
            Log::info('Email sudah di kirim');
            
            return $invitation;
        });
    }

    /**
     * Accept invitation (placeholder for the second email logic).
     */
    public function acceptInvitation($id){
        return DB::transaction(function () use ($id){
            $invitation = TenantInvitation::findOrFail($id);
            if($invitation->status === 'accepted'){
                return $this->error('Invitation already accepted');
            }

            $password = Str::random(10);
            $user = User::create([
                'name' => Str::before($invitation->email, '@'),
                'email' => $invitation->email,
                'password' => bcrypt($password),
                'tenant_id' => $invitation->tenant_id,
                'is_active' => true,
            ]);

            $user->assignRole($invitation->role);

            $tenant_id = $invitation->tenant_id;
            $order_id = 'SBS-'.uniqid();
            $substenant = Subscription::where('tenant_id', $tenant_id)
                ->where('status', 'active')
                ->first();

            if ($substenant) {
                Subscription::create([
                    'user_id' => $user->id,
                    'tenant_id' => $tenant_id,
                    'order_id' => $order_id,
                    'status' => 'active',
                    'subs_plan_id' => $substenant->subs_plan_id,
                    'starts_at' => $substenant->starts_at,
                    'ends_at' => $substenant->ends_at,
                    'paid_at' => $substenant->paid_at,
                    'payment_method' => $substenant->payment_method,
                ]);
            }

            $invitation->update([
                'user_id' => $user->id,
                'status' => 'accepted',
                'accepted_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);

            Notification::route('mail', $user->email)
                ->notify(new TenantWelcomeNotification(
                    $invitation->tenant->name,
                    $user->name,
                    $user->email,
                    $password
                ));

            return $invitation;
        });
    }
}
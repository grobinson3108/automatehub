<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workflow;
use App\Mail\WorkflowNewsletter;
use App\Mail\PremiumExclusiveAccess;
use App\Mail\WeeklyDigest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailMarketingService
{
    /**
     * Send early access email to premium members
     */
    public function sendEarlyAccessEmail(Workflow $workflow)
    {
        // Get premium users
        $premiumUsers = User::whereIn('subscription_type', ['premium', 'pro'])
            ->where('subscription_expires_at', '>', now())
            ->where('email_notifications', true)
            ->get();
        
        foreach ($premiumUsers as $user) {
            try {
                Mail::to($user)->send(new PremiumExclusiveAccess($workflow, $user));
                
                // Log email sent
                activity()
                    ->performedOn($workflow)
                    ->causedBy($user)
                    ->withProperties(['type' => 'early_access_email'])
                    ->log('Early access email sent');
                    
            } catch (\Exception $e) {
                Log::error('Failed to send early access email to ' . $user->email . ': ' . $e->getMessage());
            }
        }
        
        return count($premiumUsers);
    }
    
    /**
     * Send weekly digest to active users
     */
    public function sendWeeklyDigest()
    {
        // Get users who want weekly emails
        $users = User::where('email_notifications', true)
            ->where('weekly_digest', true)
            ->whereHas('downloads', function($query) {
                // Active users (downloaded in last 30 days)
                $query->where('created_at', '>', now()->subDays(30));
            })
            ->get();
        
        // Get this week's new workflows
        $newWorkflows = Workflow::where('created_at', '>', now()->subWeek())
            ->where('published', true)
            ->get();
        
        if ($newWorkflows->isEmpty()) {
            return 0;
        }
        
        foreach ($users as $user) {
            try {
                Mail::to($user)->send(new WeeklyDigest($user, $newWorkflows));
                
                // Update last email sent
                $user->update(['last_email_sent_at' => now()]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send weekly digest to ' . $user->email . ': ' . $e->getMessage());
            }
        }
        
        return count($users);
    }
    
    /**
     * Send targeted campaign (smart segmentation)
     */
    public function sendTargetedCampaign($segment, $subject, $content)
    {
        $users = $this->getSegmentUsers($segment);
        $sent = 0;
        
        foreach ($users as $user) {
            // Check email frequency limits
            if ($this->canSendEmail($user)) {
                try {
                    Mail::to($user)->send(new WorkflowNewsletter($subject, $content, $user));
                    $user->update(['last_email_sent_at' => now()]);
                    $sent++;
                } catch (\Exception $e) {
                    Log::error('Failed to send campaign email to ' . $user->email . ': ' . $e->getMessage());
                }
            }
        }
        
        return $sent;
    }
    
    /**
     * Get users by segment
     */
    private function getSegmentUsers($segment)
    {
        switch ($segment) {
            case 'youtube_only':
                // Users who haven't downloaded anything
                return User::where('email_notifications', true)
                    ->whereDoesntHave('downloads')
                    ->where('created_at', '<', now()->subDays(7))
                    ->get();
                    
            case 'freemium_active':
                // Active freemium users
                return User::where('subscription_type', 'freemium')
                    ->where('email_notifications', true)
                    ->whereHas('downloads', function($query) {
                        $query->where('created_at', '>', now()->subDays(14));
                    })
                    ->get();
                    
            case 'premium_expiring':
                // Premium users expiring soon
                return User::whereIn('subscription_type', ['premium', 'pro'])
                    ->where('email_notifications', true)
                    ->whereBetween('subscription_expires_at', [now(), now()->addDays(7)])
                    ->get();
                    
            default:
                return collect();
        }
    }
    
    /**
     * Check if we can send email to user (frequency limits)
     */
    private function canSendEmail(User $user)
    {
        if (!$user->email_notifications) {
            return false;
        }
        
        // Premium users: no limits
        if (in_array($user->subscription_type, ['premium', 'pro'])) {
            return true;
        }
        
        // Freemium users: max 1 per week
        if ($user->subscription_type === 'freemium') {
            return !$user->last_email_sent_at || 
                   $user->last_email_sent_at->lt(now()->subWeek());
        }
        
        // YouTube only: max 1 per month
        return !$user->last_email_sent_at || 
               $user->last_email_sent_at->lt(now()->subMonth());
    }
}
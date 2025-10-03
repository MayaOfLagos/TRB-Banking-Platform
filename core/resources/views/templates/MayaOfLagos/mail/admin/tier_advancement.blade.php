@component('mail::message')
# User Tier Advancement - {{ $newTier }} Achieved

Hello Admin,

A user has advanced to a higher tier in the rebate program!

## Tier Advancement Details
- **User:** {{ $user->firstname }} {{ $user->lastname }} ({{ $user->username }})
- **Email:** {{ $user->email }}
- **User ID:** #{{ $user->id }}
- **Previous Tier:** {{ $previousTier }}
- **New Tier:** {{ $newTier }}
- **Achievement Date:** {{ now()->format('M d, Y \a\t g:i A') }}

## Tier Benefits Unlocked
### Multiplier Increase
- **Previous:** {{ $previousMultiplier }}x multiplier
- **New:** {{ $newMultiplier }}x multiplier
- **Increase:** {{ number_format((($newMultiplier - $previousMultiplier) / $previousMultiplier) * 100, 1) }}% more on all rebates

### New Benefits
@if($newTier == 'Silver')
- ✨ 25% bonus on all rebates
- 🎯 Priority customer support
- 🔒 Exclusive Silver member programs
- 📧 Weekly program updates
- 🚀 Early access to new programs
@elseif($newTier == 'Gold')
- ✨ 50% bonus on all rebates
- 🎯 Premium customer support
- 🔒 Exclusive Gold member programs
- 📧 Daily program updates
- 🚀 Early access to new programs
- ⚡ Expedited rebate processing (1-2 days)
@elseif($newTier == 'Platinum')
- ✨ 100% bonus on all rebates
- 🎯 VIP customer support
- 🔒 Exclusive Platinum programs
- 📧 Real-time notifications
- 🚀 Beta access to new features
- 👤 Personal account manager consideration
- ⚡ Instant rebate processing
- 🎪 Special event invitations
@endif

## User Journey Summary
- **Total Earned:** {{ showAmount($totalEarned) }} {{ $general->cur_text }}
- **Total Rebates:** {{ $totalRebates }}
- **Member Since:** {{ $user->created_at->format('M Y') }}
- **Average Monthly Activity:** {{ number_format($totalRebates / max(1, $user->created_at->diffInMonths(now())), 1) }} rebates

@if($nextTier)
## Future Goals
- **Next Tier:** {{ $nextTier }}
- **Amount Needed:** {{ showAmount($amountToNext) }} {{ $general->cur_text }}
- **Estimated Timeline:** Based on current activity
@else
## Achievement Status
🏆 **HIGHEST TIER REACHED** - This user has achieved our premium Platinum status!
@endif

@component('mail::button', ['url' => route('admin.users.detail', $user->id)])
View User Profile
@endcomponent

## Business Impact
- **Customer Retention:** ✅ High-value customer
- **Revenue Potential:** ✅ Increased due to higher multiplier
- **Loyalty Status:** ✅ Premium member
@if($newTier == 'Platinum')
- **VIP Consideration:** ✅ Consider personal outreach
@endif

## Recommended Actions
1. **Congratulate the user** on their achievement
2. **Monitor usage patterns** for this tier
3. **Provide excellent support** to maintain satisfaction
@if($newTier == 'Platinum')
4. **Consider VIP treatment** for this premium customer
@endif

The user has been automatically notified of their tier advancement with details about their new benefits.

Best regards,<br>
{{ config('app.name') }} Admin Analytics

---
<small>This is an automated admin notification for tier advancement. User {{ $user->username }} achieved {{ $newTier }} tier on {{ now()->format('F d, Y') }}.</small>
@endcomponent
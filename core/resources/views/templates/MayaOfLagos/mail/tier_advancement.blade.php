@component('mail::message')
# Tier Advancement - Congratulations!

Hello {{ $user->firstname }},

🎉 **Congratulations!** You have successfully advanced to the **{{ $newTier }}** tier!

## Your New Benefits
As a **{{ $newTier }}** member, you now enjoy:

### Rebate Multiplier
- **Previous:** {{ $previousMultiplier }}x multiplier
- **New:** {{ $newMultiplier }}x multiplier
- **Increase:** {{ number_format((($newMultiplier - $previousMultiplier) / $previousMultiplier) * 100, 1) }}% more on all rebates!

### Exclusive Benefits
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
- ⚡ Expedited rebate processing
@elseif($newTier == 'Platinum')
- ✨ 100% bonus on all rebates
- 🎯 VIP customer support
- 🔒 Exclusive Platinum programs
- 📧 Real-time notifications
- 🚀 Beta access to new features
- 👤 Personal account manager
- ⚡ Instant rebate processing
- 🎪 Special event invitations
@endif

## Your Journey
- **Total Earned:** {{ showAmount($totalEarned) }} {{ $general->cur_text }}
- **Rebates Processed:** {{ $totalRebates }}
- **Member Since:** {{ $user->created_at->format('M Y') }}

@component('mail::button', ['url' => route('user.rebate.tiers')])
View Tier Benefits
@endcomponent

## What's Next?
@if($nextTier)
Your next goal is **{{ $nextTier }}** tier! You need {{ showAmount($amountToNext) }} {{ $general->cur_text }} more to reach the next level.
@else
You've reached our highest tier! Enjoy all the premium benefits and keep earning amazing rewards.
@endif

@component('mail::button', ['url' => route('user.rebate.upload')])
Continue Earning Rewards
@endcomponent

## Share Your Achievement
Don't forget to share this milestone with your friends and family. You've earned it!

Thank you for being such a valued member of our community. We're excited to see you continue growing with us!

Best regards,<br>
{{ config('app.name') }} Team

---
<small>You achieved {{ $newTier }} tier on {{ now()->format('F d, Y') }}. This email was sent to {{ $user->email }}.</small>
@endcomponent
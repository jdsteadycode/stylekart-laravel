<div style="font-family: sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 500px;">
    <h2 style="color: #f43f5e;">StyleKart</h2>
    <p>Hi {{ $order->user->name }},</p>
    <p>Your order <strong>#{{ $order->order_number }}</strong> is out for delivery! 🚚</p>

    <div style="background: #f1f5f9; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
        <p style="font-size: 12px; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Your Delivery OTP</p>
        <h1 style="font-size: 32px; letter-spacing: 5px; color: #0f172a; margin: 0;">{{ $otp }}</h1>
    </div>

    <p style="font-size: 14px; color: #475569;">Please share this OTP with our delivery partner only at the time of delivery.</p>
    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
    <p style="font-size: 12px; color: #94a3b8;">Thank you for shopping with StyleKart!</p>
</div>

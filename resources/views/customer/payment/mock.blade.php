<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Payment Gateway | Stylekart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900">

    <div class="min-h-screen flex flex-col items-center justify-center p-4">

        <div class="mb-8 flex items-center space-x-2 opacity-80">
            <div class="bg-indigo-600 p-1.5 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-slate-700">MockPay<span class="text-indigo-600">.in</span></span>
        </div>

        <div class="max-w-[480px] w-full bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 overflow-hidden">

            <div class="px-8 pt-10 pb-6 text-center border-b border-dashed border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Payable Amount</p>
                <h2 class="text-4xl font-extrabold text-slate-900">₹ {{ $order->total_amount ?? 0 }}<span class="text-xl text-gray-400 font-medium">.00</span></h2>
                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider">
                    ORDER NUMBER: {{ $order->order_number ?? 'N/A' }}
                </div>
            </div>

            <div class="p-8">
                <form action="{{ route('customer.payment.mock.process', ['orderNumber' => $order->order_number]) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Card Number</label>
                            {{-- <div class="flex space-x-1 opacity-60">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" class="h-3" alt="Visa">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" class="h-4" alt="Mastercard">
                            </div> --}}
                        </div>
                        <div class="relative group">
                            <input type="text" name="card_number" placeholder="4111 0000 0000 0000" required
                                class="w-full px-5 py-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-indigo-600 focus:bg-white outline-none transition-all duration-200 text-lg font-medium placeholder:text-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Expiry Date</label>
                            <input type="text" name="expiry" placeholder="MM / YY" required
                                class="w-full px-5 py-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-indigo-600 focus:bg-white outline-none transition-all duration-200 font-medium placeholder:text-gray-300">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">CVV Code</label>
                            <input type="password" name="cvv" maxlength="3" placeholder="•••" required
                                class="w-full px-5 py-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-indigo-600 focus:bg-white outline-none transition-all duration-200 font-medium placeholder:text-gray-300 text-center tracking-widest">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Cardholder Name</label>
                        <input type="text" name="card_name" placeholder="John Doe" required
                            class="w-full px-5 py-4 bg-gray-50 rounded-2xl border-2 border-transparent focus:border-indigo-600 focus:bg-white outline-none transition-all duration-200 font-medium placeholder:text-gray-300">
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-5 rounded-2xl shadow-[0_10px_20px_rgba(79,70,229,0.3)] hover:shadow-[0_15px_25px_rgba(79,70,229,0.4)] transition-all duration-300 transform active:scale-95 text-lg">
                        Make Payment
                    </button>
                </form>

                <div class="mt-10 flex flex-col items-center space-y-4">
                    <div class="flex items-center space-x-4 opacity-30 grayscale">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/cb/Rupay-Logo.png" class="h-4" alt="RuPay">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" class="h-4" alt="PayPal">
                    </div>
                    <div class="flex items-center text-[10px] text-gray-400 font-semibold tracking-widest uppercase">
                        <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                        Encrypted by 256-bit SSL
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('customer.checkout') }}" class="mt-8 text-sm font-semibold text-gray-400 hover:text-indigo-600 transition-colors">
            &larr; Cancel and return to Merchant
        </a>

    </div>

</body>
</html>

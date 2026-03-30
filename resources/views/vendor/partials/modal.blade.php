{{-- 🛡️ The Stylekart Report Selector Modal --}}
<div id="reportModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100">

        {{-- Modal Header --}}
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <h3 id="modalTitle" class="text-sm font-black text-slate-800 uppercase tracking-widest">Generate Report</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition text-2xl">&times;</button>
        </div>

        {{-- Modal Body --}}
        <form id="reportForm" action="{{ route('vendor.reports.generate') }}" method="GET" class="p-8">
            {{-- This hidden input is populated by your prepareReport() JS function --}}
            <input type="hidden" name="report_type" id="report_type">

            <div class="grid grid-cols-3 gap-4 mb-6">
                {{-- Year --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Year</label>
                    <select name="year" id="year" onchange="validateDateSelection()" class="w-full border-slate-200 rounded-xl text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(range(date('Y'), date('Y')-2) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Month --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Month</label>
                    <select name="month" id="month" onchange="validateDateSelection()" class="w-full border-slate-200 rounded-xl text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Full Year</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ date('m') == $m ? 'selected' : '' }}>
                                {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Day --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Day</label>
                    <select name="day" id="day" onchange="validateDateSelection()" class="w-full border-slate-200 rounded-xl text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Full Month</option>
                        @foreach(range(1, 31) as $d)
                            <option value="{{ sprintf('%02d', $d) }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- JS Date Validation Message --}}
            <div id="dateError" class="hidden mb-6 p-3 bg-red-50 text-red-600 rounded-xl border border-red-100 flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-tight">⚠️ Future dates not allowed</span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col gap-3">
                <button type="submit" name="action" value="preview" id="btnPreview"
                    class="w-full py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-200 transition duration-200">
                    Preview Data
                </button>

                <button type="submit" name="download" value="1" id="btnDownload"
                    class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition duration-200">
                    Download PDF
                </button>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-50 text-center">
                <p class="text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em] mb-4">Quick Financial Summary</p>

                {{-- 🎯 Add name="download" and value="1" here --}}
                <button type="submit" name="download" value="1"
                    onclick="document.getElementById('quarterly_input').value='1'"
                    class="w-full py-3 bg-white border-2 border-indigo-100 text-indigo-600 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-indigo-50 hover:border-indigo-200 transition">
                    📥 Download Current Quarter (3-Mo)
                </button>

                {{-- Add this hidden input inside the form to store the quarterly flag --}}
                <input type="hidden" name="quarterly" id="quarterly_input" value="0">
            </div>
        </form>
    </div>
</div>

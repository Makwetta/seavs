@extends('layouts.app')
@section('title', 'Live Verification')
@section('page-title', 'Live Attendance Verification')

@push('styles')
<style>
.verify-panel {
    border-radius: 16px; overflow: hidden;
}
.fp-big-ring {
    width: 200px; height: 200px; border-radius: 50%;
    border: 4px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px; position: relative;
    transition: all .35s;
}
.fp-big-ring::before {
    content: '';
    position: absolute;
    width: calc(100% + 20px); height: calc(100% + 20px);
    border-radius: 50%;
    border: 2px solid transparent;
    transition: all .35s;
}
.fp-big-ring.scanning {
    border-color: #2563a8;
    animation: big-pulse 1.2s infinite;
}
.fp-big-ring.scanning::before { border-color: rgba(37,99,168,.2); }
.fp-big-ring.success { border-color: #16a34a; }
.fp-big-ring.success::before { border-color: rgba(22,163,74,.2); }
.fp-big-ring.rejected { border-color: #dc2626; }
.fp-big-ring.rejected::before { border-color: rgba(220,38,38,.2); }

@keyframes big-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(37,99,168,.3); }
    50%       { box-shadow: 0 0 0 24px rgba(37,99,168,0); }
}
.fp-big-ring i {
    font-size: 5.5rem;
    color: #94a3b8; transition: all .35s;
}
.fp-big-ring.scanning i  { color: #2563a8; animation: fp-scale .9s infinite alternate; }
.fp-big-ring.success i   { color: #16a34a; }
.fp-big-ring.rejected i  { color: #dc2626; }
@keyframes fp-scale { to { transform: scale(1.07); } }

.result-card {
    border-radius: 14px; padding: 20px;
    display: none; transition: all .3s;
}
.result-card.show { display: flex; }

.log-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 8px;
    margin-bottom: 6px; font-size: .85rem;
    animation: slide-in .3s ease;
}
@keyframes slide-in {
    from { opacity: 0; transform: translateX(12px); }
    to   { opacity: 1; transform: translateX(0); }
}
.log-item.verified { background: #f0fdf4; border: 1px solid #bbf7d0; }
.log-item.rejected { background: #fff1f2; border: 1px solid #fecaca; }

.counter-box {
    border-radius: 12px; padding: 16px 20px; text-align: center;
}
.counter-val { font-size: 2rem; font-weight: 700; font-family: 'Space Grotesk', sans-serif; line-height: 1; }
</style>
@endpush

@section('content')
<div class="row g-4">
    {{-- LEFT: Scanner Panel --}}
    <div class="col-lg-7">
        <div class="card verify-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-shield-check me-2 text-success"></i>Fingerprint Verification</span>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" id="devStatus" style="background:#fef9c3;color:#854d0e;">
                        <span class="me-1">●</span> Scanner Offline
                    </span>
                </div>
            </div>
            <div class="card-body p-4">

                {{-- Exam Selector --}}
                <div class="mb-4">
                    <label class="form-label">Select Examination Session</label>
                    <select class="form-select" id="examSelect" onchange="loadExamInfo()">
                        <option value="">— Choose an exam —</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->exam_id }}"
                                data-name="{{ $exam->name }}"
                                data-subject="{{ $exam->subject->name ?? '' }}"
                                data-time="{{ \Carbon\Carbon::parse($exam->exam_time)->format('H:i') }}"
                                data-date="{{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}"
                                {{ request('exam_id') == $exam->exam_id ? 'selected' : '' }}>
                                {{ $exam->name }} — {{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}
                                {{ \Carbon\Carbon::parse($exam->exam_time)->format('H:i') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Exam Info Banner --}}
                <div id="examBanner" class="p-3 rounded-3 mb-4" style="background:#eff6ff;border:1.5px solid #bfdbfe;display:none;">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="text-muted" style="font-size:.72rem;">EXAM</div>
                            <div class="fw-600" style="font-size:.85rem;" id="bannerName">—</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted" style="font-size:.72rem;">DATE</div>
                            <div class="fw-600" style="font-size:.85rem;" id="bannerDate">—</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted" style="font-size:.72rem;">TIME</div>
                            <div class="fw-600" style="font-size:.85rem;" id="bannerTime">—</div>
                        </div>
                    </div>
                </div>

                {{-- Big Fingerprint Ring --}}
                <div class="text-center">
                    <div class="fp-big-ring" id="bigRing">
                        <i class="bi bi-fingerprint" id="bigFpIcon"></i>
                    </div>
                    <h5 class="fw-700 mb-1" id="mainLabel">Place Finger on Scanner</h5>
                    <p class="text-muted mb-4" id="mainSub" style="font-size:.88rem;">
                        Select an exam session then have the student scan their fingerprint
                    </p>

                    {{-- Result Card --}}
                    <div class="result-card align-items-center gap-3 mb-4" id="resultCard">
                        <div id="resultAvatar" style="width:56px;height:56px;border-radius:50%;flex-shrink:0;
                            display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;">
                        </div>
                        <div class="text-start flex-grow-1">
                            <div class="fw-700" id="resultName" style="font-size:1.05rem;"></div>
                            <div class="text-muted" id="resultReg" style="font-size:.82rem;"></div>
                            <div id="resultStatus" style="font-size:.82rem;margin-top:3px;"></div>
                        </div>
                        <div id="resultBadge"></div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-primary-ihet px-4" id="scanBtn" onclick="triggerScan()" disabled>
                            <i class="bi bi-fingerprint me-2"></i>Scan Fingerprint
                        </button>
                        <button class="btn btn-outline-secondary px-3" onclick="resetVerify()" id="resetBtn" style="display:none;">
                            <i class="bi bi-arrow-clockwise"></i> Next Student
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Stats + Log --}}
    <div class="col-lg-5">
        {{-- Session Stats --}}
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Session Statistics</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="counter-box" style="background:#f0fdf4;">
                            <div class="counter-val text-success" id="cntVerified">0</div>
                            <div class="text-muted" style="font-size:.78rem;margin-top:4px;">Verified</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="counter-box" style="background:#fff1f2;">
                            <div class="counter-val text-danger" id="cntRejected">0</div>
                            <div class="text-muted" style="font-size:.78rem;margin-top:4px;">Rejected</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between mb-1" style="font-size:.82rem;">
                            <span class="text-muted fw-600">Verification Rate</span>
                            <span id="rateLabel">—</span>
                        </div>
                        <div class="quality-bar" style="height:10px;background:#e2e8f0;border-radius:6px;overflow:hidden;">
                            <div id="rateFill" style="height:100%;width:0%;background:#16a34a;border-radius:6px;transition:width .4s;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Log --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Live Activity Log</span>
                <button onclick="clearLog()" class="btn btn-sm btn-outline-secondary">Clear</button>
            </div>
            <div class="card-body p-3" style="max-height:380px;overflow-y:auto;" id="logContainer">
                <div class="text-center text-muted py-4" id="logEmpty">
                    <i class="bi bi-clock" style="font-size:1.8rem;"></i>
                    <p class="mt-2 mb-0 small">No activity yet</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let verifiedCount = 0;
let rejectedCount = 0;
let currentExamId = null;

// Set scanner to online
setTimeout(() => {
    document.getElementById('devStatus').innerHTML = '<span class="me-1">●</span> Scanner Online';
    document.getElementById('devStatus').style.cssText = 'background:#dcfce7;color:#166534;';
}, 1200);

function loadExamInfo() {
    const sel = document.getElementById('examSelect');
    const opt = sel.options[sel.selectedIndex];
    currentExamId = sel.value;

    if (!currentExamId) {
        document.getElementById('examBanner').style.display = 'none';
        document.getElementById('scanBtn').disabled = true;
        return;
    }

    document.getElementById('bannerName').textContent = opt.dataset.name;
    document.getElementById('bannerDate').textContent = opt.dataset.date;
    document.getElementById('bannerTime').textContent = opt.dataset.time;
    document.getElementById('examBanner').style.display = 'block';
    document.getElementById('scanBtn').disabled = false;
}

function triggerScan() {
    if (!currentExamId) {
        alert('Please select an exam session first.');
        return;
    }
    document.getElementById('scanBtn').disabled = true;
    document.getElementById('mainLabel').textContent = 'Scanning...';
    document.getElementById('mainSub').textContent = 'Reading fingerprint, please hold still';
    document.getElementById('bigRing').className = 'fp-big-ring scanning';
    document.getElementById('resultCard').className = 'result-card';

    // ─── REPLACE WITH REAL SDK CALL ────────────────────────────────────────
    // Call your fingerprint SDK to capture template, then send to server:
    //
    //   const template = await fingerprintSdk.capture();
    //   const resp = await fetch('/attendance/verify-ajax', {
    //       method: 'POST',
    //       headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
    //       body: JSON.stringify({ fingerprint_template: template, exam_id: currentExamId })
    //   });
    //   const data = await resp.json();
    //   processResult(data);
    //
    setTimeout(() => {
        // Simulated 50/50 result — replace with actual server response
        const verified = Math.random() > 0.35;
        processResult({
            success: verified,
            student: verified ? {
                name: ['Yuaja Makweta', 'Irene Vumu', 'Ibrahim Sombi'][Math.floor(Math.random()*3)],
                reg_no: 'IHET/DIT/2024/000' + Math.floor(Math.random()*9 + 1),
                course: 'Diploma in IT'
            } : null,
            message: verified ? 'Identity verified successfully' : 'Fingerprint not recognized'
        });
    }, 2200);
    // ───────────────────────────────────────────────────────────────────────
}

function processResult(data) {
    const ring = document.getElementById('bigRing');
    const resultCard = document.getElementById('resultCard');

    if (data.success && data.student) {
        ring.className = 'fp-big-ring success';
        document.getElementById('mainLabel').textContent = 'Verified!';
        document.getElementById('mainSub').textContent = data.message;

        const initials = data.student.name.split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
        document.getElementById('resultAvatar').textContent = initials;
        document.getElementById('resultAvatar').style.cssText = 'width:56px;height:56px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;background:#dcfce7;color:#166534;';
        document.getElementById('resultName').textContent = data.student.name;
        document.getElementById('resultReg').textContent = data.student.reg_no;
        document.getElementById('resultStatus').innerHTML = '<i class="bi bi-check-circle text-success me-1"></i>' + (data.student.course || '');
        document.getElementById('resultBadge').innerHTML = '<span class="badge badge-verified" style="font-size:.85rem;padding:6px 12px;">VERIFIED</span>';
        resultCard.style.cssText = 'display:flex;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:14px;padding:20px;align-items:center;gap:12px;margin-bottom:16px;';

        verifiedCount++;
        addLog(data.student.name, data.student.reg_no, 'verified');
    } else {
        ring.className = 'fp-big-ring rejected';
        document.getElementById('mainLabel').textContent = 'Not Recognized';
        document.getElementById('mainSub').textContent = data.message || 'Fingerprint did not match any enrolled student';

        document.getElementById('resultAvatar').textContent = '?';
        document.getElementById('resultAvatar').style.cssText = 'width:56px;height:56px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;background:#fee2e2;color:#991b1b;';
        document.getElementById('resultName').textContent = 'Unknown Student';
        document.getElementById('resultReg').textContent = '—';
        document.getElementById('resultStatus').innerHTML = '<i class="bi bi-x-circle text-danger me-1"></i>Not enrolled or not registered';
        document.getElementById('resultBadge').innerHTML = '<span class="badge badge-rejected" style="font-size:.85rem;padding:6px 12px;">REJECTED</span>';
        resultCard.style.cssText = 'display:flex;background:#fff1f2;border:1.5px solid #fecaca;border-radius:14px;padding:20px;align-items:center;gap:12px;margin-bottom:16px;';

        rejectedCount++;
        addLog('Unknown', '—', 'rejected');
    }

    // Update counters
    document.getElementById('cntVerified').textContent = verifiedCount;
    document.getElementById('cntRejected').textContent = rejectedCount;
    const total = verifiedCount + rejectedCount;
    const rate = total > 0 ? Math.round((verifiedCount / total) * 100) : 0;
    document.getElementById('rateLabel').textContent = rate + '%';
    document.getElementById('rateFill').style.width = rate + '%';

    document.getElementById('resetBtn').style.display = 'inline-flex';
    document.getElementById('scanBtn').style.display = 'none';
}

function addLog(name, reg, type) {
    const container = document.getElementById('logContainer');
    const empty = document.getElementById('logEmpty');
    if (empty) empty.remove();

    const time = new Date().toLocaleTimeString('en-GB');
    const item = document.createElement('div');
    item.className = 'log-item ' + type;
    item.innerHTML = `
        <div style="width:26px;height:26px;border-radius:50%;background:${type === 'verified' ? '#dcfce7' : '#fee2e2'};
             display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.8rem;
             color:${type === 'verified' ? '#166534' : '#991b1b'};">
            <i class="bi bi-${type === 'verified' ? 'check' : 'x'}"></i>
        </div>
        <div class="flex-grow-1">
            <span class="fw-600">${name}</span>
            <span class="text-muted ms-2" style="font-size:.78rem;">${reg}</span>
        </div>
        <span class="text-muted" style="font-size:.72rem;">${time}</span>
    `;
    container.insertBefore(item, container.firstChild);
}

function clearLog() {
    const container = document.getElementById('logContainer');
    container.innerHTML = '<div class="text-center text-muted py-4" id="logEmpty"><i class="bi bi-clock" style="font-size:1.8rem;"></i><p class="mt-2 mb-0 small">No activity yet</p></div>';
}

function resetVerify() {
    document.getElementById('bigRing').className = 'fp-big-ring';
    document.getElementById('mainLabel').textContent = 'Place Finger on Scanner';
    document.getElementById('mainSub').textContent = 'Ready for next student';
    document.getElementById('resultCard').style.display = 'none';
    document.getElementById('resetBtn').style.display = 'none';
    document.getElementById('scanBtn').style.display = 'inline-flex';
    document.getElementById('scanBtn').disabled = !currentExamId;
}

// Auto-load if exam_id in URL
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('examSelect').value) {
        loadExamInfo();
    }
});
</script>
@endpush
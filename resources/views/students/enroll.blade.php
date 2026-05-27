@extends('layouts.app')
@section('title', 'Fingerprint Enrollment')
@section('page-title', 'Fingerprint Enrollment')

@push('styles')
<style>
.fp-step {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 14px; border-radius: 10px; background: #f8fafc;
    margin-bottom: 10px; border: 1.5px solid transparent;
    transition: all .25s;
}
.fp-step.active { background: #eff6ff; border-color: #bfdbfe; }
.fp-step.done   { background: #f0fdf4; border-color: #bbf7d0; }
.fp-step.error  { background: #fff1f2; border-color: #fecaca; }
.fp-step-num {
    width: 28px; height: 28px; border-radius: 50%;
    background: #e2e8f0; color: #64748b;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 700; flex-shrink: 0;
    transition: all .25s;
}
.fp-step.active .fp-step-num { background: #1a3a5c; color: #fff; }
.fp-step.done .fp-step-num   { background: #16a34a; color: #fff; }
.fp-step.error .fp-step-num  { background: #dc2626; color: #fff; }

.scan-ring {
    width: 160px; height: 160px; border-radius: 50%;
    border: 3px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    position: relative;
    transition: all .3s;
}
.scan-ring.scanning {
    border-color: #2563a8;
    animation: ring-pulse 1.5s infinite;
}
.scan-ring.success { border-color: #16a34a; }
.scan-ring.error   { border-color: #dc2626; }
@keyframes ring-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(37,99,168,.3); }
    50%       { box-shadow: 0 0 0 16px rgba(37,99,168,0); }
}
.scan-ring i {
    font-size: 4rem;
    color: #94a3b8;
    transition: all .3s;
}
.scan-ring.scanning i { color: #2563a8; animation: fp-bounce .8s infinite alternate; }
.scan-ring.success i  { color: #16a34a; }
.scan-ring.error i    { color: #dc2626; }
@keyframes fp-bounce {
    from { transform: scale(1); }
    to   { transform: scale(1.08); }
}

.quality-bar { height: 8px; border-radius: 6px; background: #e2e8f0; overflow: hidden; }
.quality-fill { height: 100%; border-radius: 6px; transition: width .4s ease; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <a href="{{ route('students.index') }}" class="text-decoration-none text-muted" style="font-size:.85rem;">
        <i class="bi bi-arrow-left me-1"></i> Back to Students
    </a>
</div>

<div class="row g-4">
    {{-- Student Info Card --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-badge me-2"></i>Student Profile</div>
            <div class="card-body text-center py-4">
                <div style="width:72px;height:72px;border-radius:50%;
                    background:#dbeafe;display:flex;align-items:center;
                    justify-content:center;margin:0 auto 16px;
                    font-size:1.6rem;font-weight:700;color:#1a3a5c;">
                    {{ strtoupper(substr($student->full_name, 0, 2)) }}
                </div>
                <h6 class="fw-700 mb-1" style="font-family:'Space Grotesk',sans-serif;">
                    {{ $student->full_name }}
                </h6>
                <p class="text-muted mb-3" style="font-size:.85rem;">{{ $student->reg_no }}</p>

                <div class="text-start">
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                        <span class="text-muted">Course</span>
                        <span class="fw-600">{{ $student->course->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                        <span class="text-muted">Gender</span>
                        <span class="fw-600">{{ $student->gender }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                        <span class="text-muted">Date of Birth</span>
                        <span class="fw-600">{{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="font-size:.83rem;">
                        <span class="text-muted">Fingerprint</span>
                        @if($student->fingerprint)
                            <span class="badge badge-verified">Enrolled</span>
                        @else
                            <span class="badge badge-rejected">Not Enrolled</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Steps Guide --}}
        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-list-ol me-2"></i>Enrollment Steps</div>
            <div class="card-body p-3">
                <div class="fp-step active" id="step1">
                    <div class="fp-step-num">1</div>
                    <div>
                        <div class="fw-600" style="font-size:.85rem;">Connect Scanner</div>
                        <small class="text-muted">Plug in the USB fingerprint scanner</small>
                    </div>
                </div>
                <div class="fp-step" id="step2">
                    <div class="fp-step-num">2</div>
                    <div>
                        <div class="fw-600" style="font-size:.85rem;">Place Finger</div>
                        <small class="text-muted">Student places index finger on scanner</small>
                    </div>
                </div>
                <div class="fp-step" id="step3">
                    <div class="fp-step-num">3</div>
                    <div>
                        <div class="fw-600" style="font-size:.85rem;">Scan Again</div>
                        <small class="text-muted">Repeat for confirmation (2nd scan)</small>
                    </div>
                </div>
                <div class="fp-step" id="step4">
                    <div class="fp-step-num">4</div>
                    <div>
                        <div class="fw-600" style="font-size:.85rem;">Save Template</div>
                        <small class="text-muted">Encrypted template saved securely</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fingerprint Scanner Panel --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-fingerprint me-2 text-success"></i>Fingerprint Capture</span>
                <span class="badge" id="scannerStatus" style="background:#fef9c3;color:#854d0e;">
                    <i class="bi bi-usb-drive me-1"></i>Awaiting Scanner
                </span>
            </div>
            <div class="card-body p-4">

                <div id="statusAlert" class="alert alert-info mb-4" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    Connect the USB fingerprint scanner and click <strong>Start Enrollment</strong> to begin.
                </div>

                {{-- Scanner Visual --}}
                <div class="text-center mb-4">
                    <div class="scan-ring" id="scanRing">
                        <i class="bi bi-fingerprint" id="fpIcon"></i>
                    </div>
                    <h6 id="scanLabel" class="fw-600 mb-1">Ready to Scan</h6>
                    <p id="scanSubLabel" class="text-muted mb-0" style="font-size:.85rem;">
                        Click "Start Enrollment" to begin
                    </p>
                </div>

                {{-- Quality Indicator --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.82rem;">
                        <span class="fw-600">Scan Quality</span>
                        <span id="qualityPct" class="text-muted">—</span>
                    </div>
                    <div class="quality-bar">
                        <div class="quality-fill bg-secondary" id="qualityFill" style="width:0%;"></div>
                    </div>
                </div>

                {{-- Scan Results --}}
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background:#f8fafc;border:1.5px solid #e2e8f0;">
                            <div class="fw-700" style="font-size:1.3rem;font-family:'Space Grotesk',sans-serif;" id="scan1Status">—</div>
                            <div class="text-muted" style="font-size:.78rem;">First Scan</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background:#f8fafc;border:1.5px solid #e2e8f0;">
                            <div class="fw-700" style="font-size:1.3rem;font-family:'Space Grotesk',sans-serif;" id="scan2Status">—</div>
                            <div class="text-muted" style="font-size:.78rem;">Second Scan (Confirm)</div>
                        </div>
                    </div>
                </div>

                {{-- Hidden form to save fingerprint data --}}
                <form method="POST" action="{{ route('students.enroll.save', $student) }}" id="enrollForm">
                    @csrf
                    <input type="hidden" name="fingerprint_template" id="fingerprintTemplate">
                    <input type="hidden" name="finger_type" id="fingerType" value="index_right">

                    <div class="mb-3">
                        <label class="form-label">Finger to Enroll</label>
                        <select class="form-select" name="finger_type" id="fingerTypeSelect" style="max-width:280px;">
                            <option value="index_right">Right Index Finger (Recommended)</option>
                            <option value="thumb_right">Right Thumb</option>
                            <option value="index_left">Left Index Finger</option>
                            <option value="thumb_left">Left Thumb</option>
                        </select>
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <button type="button" class="btn btn-primary-ihet px-4" id="startBtn" onclick="startEnrollment()">
                            <i class="bi bi-play-circle me-2"></i>Start Enrollment
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-4" id="retryBtn" onclick="resetScan()" style="display:none;">
                            <i class="bi bi-arrow-clockwise me-2"></i>Retry
                        </button>
                        <button type="submit" class="btn btn-accent px-4" id="saveBtn" style="display:none;">
                            <i class="bi bi-check2-circle me-2"></i>Save Fingerprint
                        </button>
                    </div>
                </form>

                <div class="mt-4 p-3 rounded-3" style="background:#fffbeb;border:1px solid #fde68a;">
                    <p class="mb-0" style="font-size:.8rem;color:#854d0e;">
                        <i class="bi bi-shield-lock me-2"></i>
                        <strong>Privacy Notice:</strong> Fingerprint templates are encrypted (AES-256) before storage.
                        Raw biometric images are never saved. Data is protected under Tanzania's Personal Data Protection Act (2022).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * FINGERPRINT ENROLLMENT CONTROLLER
 *
 * In a real deployment, replace the simulated functions below with actual
 * SDK calls from your biometric vendor (e.g. DigitalPersona, SecuGen, etc.)
 *
 * The SDK typically exposes:
 *   - startCapture()  → fires callback with template base64 string + quality score
 *   - stopCapture()
 *   - matchTemplates(t1, t2) → returns boolean + match score
 */

let scan1Template = null;
let scan2Template = null;
let currentStep = 0;

function setStep(n) {
    for (let i = 1; i <= 4; i++) {
        const el = document.getElementById('step' + i);
        el.className = 'fp-step ' + (i < n ? 'done' : i === n ? 'active' : '');
        el.querySelector('.fp-step-num').textContent = i < n ? '✓' : i;
    }
}

function setRingState(state) {
    const ring = document.getElementById('scanRing');
    ring.className = 'scan-ring ' + state;
}

function setAlert(type, msg) {
    const el = document.getElementById('statusAlert');
    el.className = 'alert alert-' + type + ' mb-4';
    el.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle' : type === 'danger' ? 'x-circle' : 'info-circle') + ' me-2"></i>' + msg;
}

function setQuality(pct) {
    document.getElementById('qualityPct').textContent = pct + '%';
    const fill = document.getElementById('qualityFill');
    fill.style.width = pct + '%';
    fill.className = 'quality-fill ' + (pct >= 75 ? 'bg-success' : pct >= 50 ? 'bg-warning' : 'bg-danger');
}

function startEnrollment() {
    document.getElementById('startBtn').style.display = 'none';
    document.getElementById('retryBtn').style.display = 'inline-flex';
    document.getElementById('scannerStatus').innerHTML = '<i class="bi bi-circle-fill me-1" style="font-size:.5rem;"></i>Scanning...';
    document.getElementById('scannerStatus').style.cssText = 'background:#dcfce7;color:#166534;';

    if (scan1Template === null) {
        captureScan(1);
    } else {
        captureScan(2);
    }
}

function captureScan(scanNumber) {
    setStep(scanNumber === 1 ? 2 : 3);
    setRingState('scanning');
    document.getElementById('scanLabel').textContent = 'Scanning...';
    document.getElementById('scanSubLabel').textContent = 'Please place your ' + document.getElementById('fingerTypeSelect').value.replace('_', ' ') + ' on the scanner';

    // ─── REPLACE THIS BLOCK WITH REAL SDK CALL ───────────────────────────────
    // Example for DigitalPersona WebSdk:
    //   reader.startAcquisition(FingerprintSdkTest.ACQUISITION_ONE_FINGER, deviceId);
    //   reader.onSamplesAcquired = function(s) { processTemplate(s.samples[0]); }
    //
    // For demonstration, we simulate a 2-second scan:
    setTimeout(() => simulateScanResult(scanNumber), 2000);
    // ─────────────────────────────────────────────────────────────────────────
}

function simulateScanResult(scanNumber) {
    // Simulated quality score (60–98%) — replace with SDK quality score
    const quality = Math.floor(Math.random() * 38) + 60;
    // Simulated base64 template — replace with actual SDK template
    const fakeTemplate = 'FP_TEMPLATE_' + scanNumber + '_' + Date.now() + '_' + btoa(Math.random().toString()).substring(0, 32);

    setQuality(quality);

    if (quality >= 50) {
        setRingState('success');

        if (scanNumber === 1) {
            scan1Template = fakeTemplate;
            document.getElementById('scan1Status').textContent = '✓ OK';
            document.getElementById('scan1Status').style.color = '#16a34a';
            setAlert('info', 'First scan captured successfully. Please place the <strong>same finger</strong> again for confirmation.');
            setStep(3);
            document.getElementById('scanLabel').textContent = 'First Scan Done!';
            document.getElementById('scanSubLabel').textContent = 'Click Start Enrollment for second scan';
            document.getElementById('startBtn').style.display = 'inline-flex';
            document.getElementById('startBtn').innerHTML = '<i class="bi bi-fingerprint me-2"></i>Scan Again (Confirm)';

        } else {
            scan2Template = fakeTemplate;
            document.getElementById('scan2Status').textContent = '✓ OK';
            document.getElementById('scan2Status').style.color = '#16a34a';

            // Match templates — replace with real SDK matchTemplates() call
            const matched = true; // SDK: matchTemplates(scan1Template, scan2Template)

            if (matched) {
                setStep(4);
                setAlert('success', '<strong>Enrollment successful!</strong> Both scans match. Click <strong>Save Fingerprint</strong> to complete enrollment.');
                document.getElementById('scanLabel').textContent = 'Match Confirmed!';
                document.getElementById('scanSubLabel').textContent = 'Templates match – ready to save';
                document.getElementById('fingerprintTemplate').value = scan1Template;
                document.getElementById('startBtn').style.display = 'none';
                document.getElementById('saveBtn').style.display = 'inline-flex';
            } else {
                setRingState('error');
                setAlert('danger', 'Fingerprints do not match. Please retry the enrollment process.');
                document.getElementById('scan2Status').textContent = '✗ Mismatch';
                document.getElementById('scan2Status').style.color = '#dc2626';
            }
        }
    } else {
        setRingState('error');
        setAlert('danger', 'Scan quality too low (' + quality + '%). Ensure the finger is clean and dry, then try again.');
        document.getElementById('scanLabel').textContent = 'Low Quality';
        document.getElementById('scanSubLabel').textContent = 'Clean finger and retry';
        if (scanNumber === 1) document.getElementById('startBtn').style.display = 'inline-flex';
    }
}

function resetScan() {
    scan1Template = null;
    scan2Template = null;
    currentStep = 0;
    setStep(1);
    setRingState('');
    setQuality(0);
    document.getElementById('qualityPct').textContent = '—';
    document.getElementById('scan1Status').textContent = '—';
    document.getElementById('scan1Status').style.color = '';
    document.getElementById('scan2Status').textContent = '—';
    document.getElementById('scan2Status').style.color = '';
    document.getElementById('scanLabel').textContent = 'Ready to Scan';
    document.getElementById('scanSubLabel').textContent = 'Click "Start Enrollment" to begin';
    document.getElementById('startBtn').innerHTML = '<i class="bi bi-play-circle me-2"></i>Start Enrollment';
    document.getElementById('startBtn').style.display = 'inline-flex';
    document.getElementById('saveBtn').style.display = 'none';
    document.getElementById('scannerStatus').innerHTML = '<i class="bi bi-usb-drive me-1"></i>Awaiting Scanner';
    document.getElementById('scannerStatus').style.cssText = 'background:#fef9c3;color:#854d0e;';
    setAlert('info', 'Enrollment reset. Click <strong>Start Enrollment</strong> to begin again.');
}
</script>
@endpush
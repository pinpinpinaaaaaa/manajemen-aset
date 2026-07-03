<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Vendor</title>
    <style>
        input[type="url"] {
            width: 100%;
            padding: 0.5rem 0;
            border: none;
            border-bottom: 1px solid #e0e0e0;
            font-size: 16px;
            font-family: inherit;
            outline: none;
            transition: all 0.3s ease;
        }

        input[type="url"]:focus {
            border-bottom-width: 2px;
            border-bottom-color: #ebca56;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #ffffff;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .progress-bar-container {
            margin-bottom: 1.5rem;
        }

        .progress-bar {
            height: 8px;
            background-color: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: #ebca56;
            transition: width 0.5s ease-out;
            width: 0%;
        }

        .progress-text {
            text-align: right;
            margin-top: 0.5rem;
            font-size: 12px;
            color: #666666;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            width: auto;
            height: auto;
            border-radius: 0;
            animation: none;
        }

        .logo-img {
            max-width: 300px;
            height: auto;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 400;
            color: #000000;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 14px;
            color: #666666;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .field-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: translateX(-20px);
        }

        .field-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .field-card.focused {
            border-color: #ebca56 !important;
            box-shadow: 0 0 0 3px rgba(235, 202, 86, 0.1);
        }

        .field-card.completed::before {
            content: '';
            position: absolute;
            right: 16px;
            top: 16px;
            width: 24px;
            height: 24px;
            background-color: #ebca56;
            border-radius: 50%;
            animation: bounce 0.6s ease-out;
        }

        .field-card.completed::after {
            content: '✓';
            position: absolute;
            right: 22px;
            top: 18px;
            color: #000000;
            font-weight: bold;
            font-size: 14px;
            z-index: 1;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 16px;
            font-weight: 400;
            color: #000000;
        }

        .required {
            color: #d93025;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 0.5rem 0;
            border: none;
            border-bottom: 1px solid #e0e0e0;
            font-size: 16px;
            font-family: inherit;
            outline: none;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            border-bottom-width: 2px;
            border-bottom-color: #ebca56;
        }

        textarea {
            resize: none;
            min-height: 80px;
        }

        .error-message {
            margin-top: 0.5rem;
            font-size: 12px;
            color: #d93025;
            animation: slideIn 0.3s ease-out;
        }

        .file-upload-area {
            margin-top: 0.5rem;
        }

        .file-input-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background-color: #f5f5f5;
            color: #666666;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input-label:hover {
            background-color: #ebca56;
            transform: scale(1.05);
        }

        .file-input-label svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
        }

        input[type="file"] {
            display: none;
        }

        .file-preview {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background-color: #f5f5f5;
            border-radius: 6px;
        }

        .file-preview-content {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .file-preview-content svg {
            width: 20px;
            height: 20px;
            fill: #ebca56;
        }

        .file-preview-name {
            color: #000000;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-remove-btn {
            padding: 0.25rem;
            background: none;
            border: none;
            color: #d93025;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .file-remove-btn:hover {
            background-color: rgba(217, 48, 37, 0.1);
        }

        .file-remove-btn svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding-top: 1rem;
            opacity: 0;
            transform: translateX(-20px);
        }

        .btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-cancel {
            background-color: transparent;
            color: #666666;
        }

        .btn-cancel:hover:not(:disabled) {
            background-color: #f5f5f5;
        }

        .btn-submit {
            background-color: #ebca56;
            color: #000000;
            border-color: #ebca56;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover:not(:disabled) {
            background-color: #d4b54a;
        }

        .spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-top-color: #000000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
        }

        .success-modal {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        .success-modal.show {
            display: flex;
        }

        .success-modal-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #ebca56;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: checkmark 0.8s ease-in-out;
        }

        .success-icon svg {
            width: 40px;
            height: 40px;
            stroke: #000000;
            fill: none;
        }

        .success-modal h2 {
            font-size: 24px;
            font-weight: 500;
            color: #000000;
            margin-bottom: 0.5rem;
        }

        .success-modal p {
            color: #666666;
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
        }

        .footer p {
            font-size: 12px;
            color: #666666;
        }

        @media (min-width: 640px) {
            .container {
                padding: 2rem 1.5rem;
            }

            .button-group {
                flex-direction: row;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes checkmark {
            0% {
                transform: rotate(0deg) scale(0);
            }

            50% {
                transform: rotate(180deg) scale(1.2);
            }

            100% {
                transform: rotate(360deg) scale(1);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(235, 202, 86, 0.4);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(235, 202, 86, 0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="progress-bar-container">
            <div class="progress-bar">
                <div class="progress-bar-fill" id="progressBar"></div>
            </div>
            <p class="progress-text"><span id="progressText">0</span>% selesai</p>
        </div>

        <div class="header">
            <div class="logo">
                <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-img">
            </div>
            <h1>Form Pendaftaran Vendor</h1>
            <p>Lengkapi formulir di bawah ini dengan data yang benar</p>
        </div>

        @if (session('error'))
            <div style="background:#fee2e2;border:1px solid #f87171;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
                {{ session('error') }}
            </div>
        @endif

        <form id="vendorForm" class="form-container" action="{{ route('vendor.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="field-card" data-field="namaPerusahaan">
                <label>Nama Perusahaan <span class="required">*</span></label>
                <input type="text" name="nama_perusahaan" placeholder="Masukkan nama perusahaan">
                <div class="error-message"></div>
            </div>

            <div class="field-card" data-field="bidangUsaha">
                <label>Bidang Usaha <span class="required">*</span></label>
                <input type="text" name="bidang_usaha" placeholder="Masukkan bidang usaha">
                <div class="error-message"></div>
            </div>

            <div class="field-card" data-field="contactPerson">
                <label>Contact Person <span class="required">*</span></label>
                <input type="text" name="contact_person" placeholder="Masukkan nama contact person">
                <div class="error-message"></div>
            </div>

            <div class="field-card" data-field="jabatan_cp">
                <label>Jabatan Contact Person <span class="required">*</span></label>
                <input type="text" name="jabatan_cp" placeholder="Manager Purchasing">
                <div class="error-message"></div>
            </div>

            <div class="field-card" data-field="no_telp_cp">
                <label>No Telp Contact Person <span class="required">*</span></label>
                <input type="tel" name="no_telp_cp" placeholder="628123456789" pattern="62[0-9]{8,15}"
                    maxlength="16" required>
                <div class="error-message"></div>
            </div>

            <div class="field-card" data-field="emailPerusahaan">
                <label>Email Perusahaan <span class="required">*</span></label>
                <input type="email" name="email_perusahaan" placeholder="email@perusahaan.com">
                <div class="error-message"></div>
            </div>

            <div class="field-card" data-field="alamat">
                <label>Alamat Perusahaan <span class="required">*</span></label>
                <textarea name="alamat" placeholder="Masukkan alamat lengkap perusahaan"></textarea>
                <div class="error-message"></div>
            </div>

            <div class="field-card" data-field="aktaPendirian">
                <label>Akta Pendirian</label>

                <div style="margin-bottom:15px;">
                    <label style="display:inline-block;margin-right:20px;">
                        <input type="radio" name="akta_type" value="file" checked>
                        Upload File
                    </label>

                    <label style="display:inline-block;">
                        <input type="radio" name="akta_type" value="link">
                        Gunakan Link
                    </label>
                </div>

                <div class="file-upload-area akta-file">
                    <label class="file-input-label">
                        <svg viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Pilih File
                        <input type="file" name="akta" accept=".pdf,.jpg,.jpeg,.png">
                    </label>
                </div>

                <div class="akta-link" style="display:none;">
                    <input type="url" name="akta_link" placeholder="https://drive.google.com/...">
                </div>
            </div>

            <div class="field-card" data-field="nib">
                <label>NIB (Nomor Induk Berusaha)</label>
                <div style="margin-bottom:15px;">
                    <label style="display:inline-block;margin-right:20px;">
                        <input type="radio" name="nib_type" value="file" checked>
                        Upload File
                    </label>

                    <label style="display:inline-block;">
                        <input type="radio" name="nib_type" value="link">
                        Gunakan Link
                    </label>
                </div>

                <div class="file-upload-area nib-file">
                    <label class="file-input-label">
                        <svg viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Pilih File
                        <input type="file" name="nib" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </label>
                </div>

                <div class="nib-link" style="display:none;">
                    <input type="url" name="nib_link" placeholder="https://drive.google.com/...">
                </div>
            </div>

            <div class="field-card" data-field="npwp">
                <label>NPWP</label>

                <div style="margin-bottom:15px;">
                    <label style="display:inline-block;margin-right:20px;">
                        <input type="radio" name="npwp_type" value="file" checked>
                        Upload File
                    </label>

                    <label style="display:inline-block;">
                        <input type="radio" name="npwp_type" value="link">
                        Gunakan Link
                    </label>
                </div>

                <div class="file-upload-area" data-file="npwp-file">
                    <label class="file-input-label">
                        <svg viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Pilih File
                        <input type="file" name="npwp" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </label>
                </div>

                <div class="npwp-link" style="display:none;">
                    <input type="url" name="npwp_link" placeholder="https://drive.google.com/...">
                </div>
            </div>

            <div class="field-card" data-field="pakta_integritas">
                <label>Pakta Integritas</label>
                <div style="margin-bottom:15px;">
                    <label style="display:inline-block;margin-right:20px;">
                        <input type="radio" name="pakta_integritas_type" value="file" checked>
                        Upload File
                    </label>

                    <label style="display:inline-block;">
                        <input type="radio" name="pakta_integritas_type" value="link">
                        Gunakan Link
                    </label>
                </div>

                <div class="file-upload-area" data-file="pakta_integritas-file">
                    <label class="file-input-label">
                        <svg viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Pilih File
                        <input type="file" name="pakta_integritas" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </label>
                </div>

                <div class="pakta_integritas-link" style="display:none;">
                    <input type="url" name="pakta_integritas_link" placeholder="https://drive.google.com/...">
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-submit" id="submitBtn">
                    <span>Simpan</span>
                </button>
            </div>

        </form>

        <div class="footer">
            <p>Form ini dibuat untuk pendaftaran vendor baru</p>
        </div>
    </div>

    <div class="success-modal" id="successModal">
        <div class="success-modal-content">
            <div class="success-icon">
                <svg viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h2>Berhasil!</h2>
            <p>Data vendor berhasil dikirim.</p>
        </div>
    </div>

    <script>
        document.getElementById('vendorForm')
            .addEventListener('submit', function() {

                console.log(document.querySelector('input[name="akta"]'));
                console.log(document.querySelector('input[name="nib"]'));
                console.log(document.querySelector('input[name="npwp"]'));
                console.log(document.querySelector('input[name="pakta_integritas"]'));

            });
        document.addEventListener('DOMContentLoaded', function() {

            const docs = [
                'akta',
                'nib',
                'npwp',
                'pakta_integritas'
            ];

            docs.forEach(doc => {

                document.querySelectorAll(`input[name="${doc}_type"]`)
                    .forEach(radio => {

                        radio.addEventListener('change', function() {

                            const fileDiv = document.querySelector(`.${doc}-file`);
                            const linkDiv = document.querySelector(`.${doc}-link`);

                            if (this.value === 'file') {
                                fileDiv.style.display = 'block';
                                linkDiv.style.display = 'none';
                            } else {
                                fileDiv.style.display = 'none';
                                linkDiv.style.display = 'block';
                            }

                        });

                    });

            });

        });


        // State management
        const state = {
            formData: {},
            files: {},
            completedFields: new Set(),
            isSubmitting: false
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
            animateFieldCards();
        });

        function initializeForm() {
            const form = document.getElementById('vendorForm');

            // Text inputs and textareas
            const textInputs = form.querySelectorAll('input[type="text"], input[type="email"], textarea');
            textInputs.forEach(input => {
                input.addEventListener('focus', handleInputFocus);
                input.addEventListener('blur', handleInputBlur);
                input.addEventListener('input', handleInputChange);
            });

            // File inputs
            const fileInputs = form.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', handleFileChange);
            });

            // Form submission
            form.addEventListener('submit', handleSubmit);
        }

        function handleInputFocus(e) {
            const card = e.target.closest('.field-card');
            card.classList.add('focused');
        }

        function handleInputBlur(e) {
            const card = e.target.closest('.field-card');
            card.classList.remove('focused');
        }

        function handleInputChange(e) {
            const input = e.target;
            const name = input.name;
            const value = input.value;
            const card = input.closest('.field-card');
            const errorDiv = card.querySelector('.error-message');

            state.formData[name] = value;

            // Clear error
            errorDiv.textContent = '';

            // Update completion status
            if (value.trim()) {
                if (input.type === 'email') {
                    if (validateEmail(value)) {
                        state.completedFields.add(name);
                        card.classList.add('completed');
                    } else {
                        state.completedFields.delete(name);
                        card.classList.remove('completed');
                    }
                } else {
                    state.completedFields.add(name);
                    card.classList.add('completed');
                }
            } else {
                state.completedFields.delete(name);
                card.classList.remove('completed');
            }

            updateProgress();
        }

        function handleFileChange(e) {
            const input = e.target;
            const file = input.files[0];

            if (!file) return;

            const card = input.closest('.field-card');
            card.classList.add('completed');

            let preview = card.querySelector('.file-preview');

            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'file-preview';
                card.appendChild(preview);
            }

            preview.innerHTML = `
                <div class="file-preview-content">
                    <svg viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="file-preview-name">${file.name}</span>
                </div>
                <button type="button"
                        class="file-remove-btn"
                        onclick="removeFile(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
        }

        function removeFile(name) {
            delete state.files[name];
            state.completedFields.delete(name);

            const card = document.querySelector(`[data-field="${name}"]`);
            card.classList.remove('completed');

            const uploadArea = card.querySelector('.file-upload-area');
            uploadArea.innerHTML = `
                <label class="file-input-label">
                    <svg viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Pilih File
                    <input type="file" name="${name}" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                </label>
            `;

            // Re-attach event listener
            const newInput = uploadArea.querySelector('input[type="file"]');
            newInput.addEventListener('change', handleFileChange);

            updateProgress();
        }

        function updateProgress() {
            const totalFields = 10;
            const completed = state.completedFields.size;
            const progress = Math.round((completed / totalFields) * 100);

            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressText').textContent = progress;
        }

        function validateEmail(email) {
            return /\S+@\S+\.\S+/.test(email);
        }

        function validateForm() {
            let isValid = true;
            const form = document.getElementById('vendorForm');

            // Required fields
            const requiredFields = [{
                    name: 'nama_perusahaan',
                    message: 'Nama perusahaan wajib diisi'
                },
                {
                    name: 'bidang_usaha',
                    message: 'Bidang usaha wajib diisi'
                },
                {
                    name: 'alamat',
                    message: 'Alamat perusahaan wajib diisi'
                },
                {
                    name: 'contact_person',
                    message: 'Contact person wajib diisi'
                },
                {
                    name: 'email_perusahaan',
                    message: 'Email perusahaan wajib diisi'
                },
                {
                    name: 'no_telp_cp',
                    message: 'Nomor telepon wajib diisi'
                },
                {
                    name: 'jabatan_cp',
                    message: 'Jabatan wajib diisi'
                }
            ];

            requiredFields.forEach(field => {
                const input = form.querySelector(`[name="${field.name}"]`);
                const value = input.value.trim();
                const card = input.closest('.field-card');
                const errorDiv = card.querySelector('.error-message');

                if (!value) {
                    errorDiv.textContent = field.message;
                    isValid = false;
                } else if (field.name.includes('email') && !validateEmail(value)) {
                    errorDiv.textContent = 'Email tidak valid';
                    isValid = false;
                }
                if (field.name === 'no_telp_cp') {

                    if (!/^62[0-9]{8,15}$/.test(value)) {

                        errorDiv.textContent =
                            'Nomor telepon harus diawali 62 dan hanya angka';

                        isValid = false;
                    }

                } else {
                    errorDiv.textContent = '';
                }
            });

            return isValid;
        }
        document.querySelector('input[name="no_telp_cp"]')
            .addEventListener('input', function() {

                this.value = this.value.replace(/[^0-9]/g, '');

            });

        function handleSubmit(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }

            const submitBtn = document.getElementById('submitBtn');

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="spinner"></div>
                <span>Menyimpan...</span>
            `;
        }

        function handleCancel() {
            const form = document.getElementById('vendorForm');
            form.reset();

            state.formData = {};
            state.files = {};
            state.completedFields.clear();

            // Remove all completed states
            document.querySelectorAll('.field-card').forEach(card => {
                card.classList.remove('completed');
            });

            // Clear all errors
            document.querySelectorAll('.error-message').forEach(error => {
                error.textContent = '';
            });

            // Reset all file uploads
            document.querySelectorAll('.file-upload-area').forEach(area => {
                const fieldName = area.getAttribute('data-file');
                area.innerHTML = `
                    <label class="file-input-label">
                        <svg viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Pilih File
                        <input type="file" name="${fieldName}" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </label>
                `;

                const newInput = area.querySelector('input[type="file"]');
                newInput.addEventListener('change', handleFileChange);
            });

            updateProgress();
        }

        function animateFieldCards() {
            const cards = document.querySelectorAll('.field-card');
            const buttonGroup = document.querySelector('.button-group');

            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.animation = `slideIn 0.5s ease-out ${index * 0.1}s forwards`;
                }, 100);
            });

            setTimeout(() => {
                buttonGroup.style.animation = `slideIn 0.5s ease-out ${cards.length * 0.1}s forwards`;
            }, 100);
        }
    </script>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const modal = document.getElementById('successModal');

                if (modal) {
                    modal.classList.add('show');

                    setTimeout(() => {
                        modal.classList.remove('show');
                    }, 3000);
                }

            });
        </script>
    @endif
</body>

</html>

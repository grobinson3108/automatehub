@extends('layouts.backend')

@section('title', 'Traduction de Workflows JSON - Automatehub Admin')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          <i class="fa fa-language me-2 text-primary"></i>Traduction de Workflows JSON
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Traduisez vos workflows n8n en français automatiquement
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item">
            <a class="link-fx" href="#">Outils</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Traduction Workflows
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">

  <!-- Status Information -->
  <div class="row">
    <div class="col-lg-12">
      <div class="block block-rounded" id="status-block">
        <div class="block-header block-header-default">
          <h3 class="block-title">
            <i class="fa fa-info-circle me-2"></i>Statut du Système
          </h3>
          <div class="block-options">
            <button type="button" class="btn btn-sm btn-alt-secondary" onclick="checkStatus()">
              <i class="fa fa-sync-alt"></i> Actualiser
            </button>
          </div>
        </div>
        <div class="block-content pb-4">
          <div id="status-content">
            <div class="text-center py-3">
              <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
              <p class="mt-2 text-muted">Vérification du statut...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Translation Interface -->
  <div class="row">
    <div class="col-xl-6">
      <!-- Input Panel -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">
            <i class="fa fa-upload me-2"></i>Workflow Original (JSON)
          </h3>
          <div class="block-options">
            <button type="button" class="btn btn-sm btn-alt-secondary" onclick="clearInput()">
              <i class="fa fa-eraser"></i> Effacer
            </button>
          </div>
        </div>
        <div class="block-content pb-4">
          <form id="translation-form" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label class="form-label" for="workflow_name">Nom du workflow (optionnel)</label>
              <input type="text" class="form-control" id="workflow_name" name="workflow_name"
                     placeholder="Ex: Mon Super Workflow">
              <div class="form-text">Si vide, un nom automatique sera généré</div>
            </div>

            <!-- Upload Method Selection -->
            <div class="mb-3">
              <label class="form-label">Méthode d'importation</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="import_method" id="import_paste" value="paste" checked>
                  <label class="form-check-label" for="import_paste">
                    <i class="fa fa-paste me-1"></i> Coller le JSON
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="import_method" id="import_file" value="file">
                  <label class="form-check-label" for="import_file">
                    <i class="fa fa-upload me-1"></i> Importer un fichier
                  </label>
                </div>
              </div>
            </div>

            <!-- File Upload -->
            <div class="mb-3" id="file-upload-section" style="display: none;">
              <label class="form-label" for="workflow_file">Fichier JSON du workflow</label>
              <input type="file" class="form-control" id="workflow_file" name="workflow_file"
                     accept=".json" onchange="handleFileSelect(this)">
              <div class="form-text">
                <i class="fa fa-file-code me-1"></i>
                Sélectionnez un fichier .json de votre workflow n8n
              </div>
            </div>

            <!-- JSON Content -->
            <div class="mb-3" id="json-paste-section">
              <label class="form-label" for="json_content">Contenu JSON du workflow</label>
              <textarea class="form-control font-monospace" id="json_content" name="json_content"
                        rows="20" placeholder='Collez ici le contenu JSON de votre workflow n8n...'></textarea>
              <div class="form-text">
                <i class="fa fa-info-circle me-1"></i>
                Copiez le contenu JSON de votre workflow n8n ici
              </div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg" id="translate-btn">
                <i class="fa fa-language me-2"></i>Traduire en Français
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-xl-6">
      <!-- Output Panel -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">
            <i class="fa fa-download me-2"></i>Workflow Traduit
          </h3>
          <div class="block-options">
            <button type="button" class="btn btn-sm btn-alt-success" id="download-btn" style="display: none;" onclick="downloadTranslated()">
              <i class="fa fa-download"></i> Télécharger
            </button>
            <button type="button" class="btn btn-sm btn-alt-secondary" id="copy-btn" style="display: none;" onclick="copyToClipboard()">
              <i class="fa fa-copy"></i> Copier
            </button>
          </div>
        </div>
        <div class="block-content pb-4">
          <div id="translation-result">
            <div class="text-center py-5">
              <i class="fa fa-language fa-3x text-muted"></i>
              <h4 class="fw-semibold text-muted mt-3">Prêt pour la traduction</h4>
              <p class="text-muted">
                Collez votre JSON dans le panneau de gauche et cliquez sur "Traduire"
              </p>
            </div>
          </div>

          <textarea class="form-control font-monospace" id="translated_content"
                    rows="20" style="display: none;" readonly></textarea>
        </div>
      </div>
    </div>
  </div>

  <!-- Translation Log -->
  <div class="row" id="log-section" style="display: none;">
    <div class="col-lg-12">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">
            <i class="fa fa-terminal me-2"></i>Log de Traduction
          </h3>
          <div class="block-options">
            <button type="button" class="btn btn-sm btn-alt-secondary" onclick="toggleLog()">
              <i class="fa fa-eye"></i> <span id="log-toggle-text">Masquer</span>
            </button>
          </div>
        </div>
        <div class="block-content pb-4" id="log-content">
          <pre class="bg-body-dark text-light p-3 rounded" id="translation-log"></pre>
        </div>
      </div>
    </div>
  </div>

</div>
<!-- END Page Content -->

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h4 class="modal-title">
          <i class="fa fa-check-circle me-2"></i>Traduction Réussie
        </h4>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Votre workflow a été traduit avec succès !</p>
        <p class="mb-0">Vous pouvez maintenant télécharger le fichier traduit ou copier le contenu.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-success" onclick="downloadTranslated()" data-bs-dismiss="modal">
          <i class="fa fa-download me-1"></i>Télécharger
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js_after')
<script>
let translatedData = null;

// Check system status on page load
document.addEventListener('DOMContentLoaded', function() {
    checkStatus();
    setupImportMethodToggle();
});

// Setup import method toggle
function setupImportMethodToggle() {
    const pasteRadio = document.getElementById('import_paste');
    const fileRadio = document.getElementById('import_file');
    const pasteSection = document.getElementById('json-paste-section');
    const fileSection = document.getElementById('file-upload-section');

    pasteRadio.addEventListener('change', function() {
        if (this.checked) {
            pasteSection.style.display = 'block';
            fileSection.style.display = 'none';
            document.getElementById('workflow_file').value = '';
        }
    });

    fileRadio.addEventListener('change', function() {
        if (this.checked) {
            pasteSection.style.display = 'none';
            fileSection.style.display = 'block';
            document.getElementById('json_content').value = '';
        }
    });
}

// Handle file selection
function handleFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Vérifier que c'est un fichier JSON
        if (!file.name.toLowerCase().endsWith('.json')) {
            alert('Veuillez sélectionner un fichier .json');
            input.value = '';
            return;
        }

        // Lire le contenu du fichier
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                // Vérifier que c'est un JSON valide
                const jsonContent = e.target.result;
                JSON.parse(jsonContent);

                // Si le nom du workflow n'est pas défini, utiliser le nom du fichier
                const workflowNameInput = document.getElementById('workflow_name');
                if (!workflowNameInput.value.trim()) {
                    const fileName = file.name.replace('.json', '');
                    workflowNameInput.value = fileName;
                }

                // Afficher une preview du fichier chargé
                showFilePreview(file.name, jsonContent);

            } catch (error) {
                alert('Le fichier sélectionné n\'est pas un JSON valide');
                input.value = '';
            }
        };
        reader.readAsText(file);
    }
}

// Show file preview
function showFilePreview(fileName, content) {
    const fileSection = document.getElementById('file-upload-section');

    // Supprimer l'ancienne preview s'il y en a une
    const existingPreview = fileSection.querySelector('.file-preview');
    if (existingPreview) {
        existingPreview.remove();
    }

    // Créer la preview
    const preview = document.createElement('div');
    preview.className = 'file-preview mt-2 p-3 bg-light rounded';

    const jsonData = JSON.parse(content);
    const nodeCount = jsonData.nodes ? jsonData.nodes.length : 0;
    const workflowName = jsonData.name || 'Sans nom';

    preview.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fa fa-file-code fa-2x text-success me-3"></i>
            <div>
                <h6 class="mb-1">${fileName}</h6>
                <small class="text-muted">
                    Workflow: ${workflowName} • ${nodeCount} nœud(s) • ${(content.length / 1024).toFixed(1)} KB
                </small>
            </div>
        </div>
    `;

    fileSection.appendChild(preview);
}

// Check translation system status
function checkStatus() {
    fetch('{{ route("admin.tools.workflow-translation.status") }}')
        .then(response => response.json())
        .then(data => {
            updateStatusDisplay(data);
        })
        .catch(error => {
            console.error('Error:', error);
            updateStatusDisplay({
                success: false,
                message: 'Erreur lors de la vérification du statut'
            });
        });
}

// Update status display
function updateStatusDisplay(data) {
    const statusContent = document.getElementById('status-content');

    if (data.success) {
        statusContent.innerHTML = `
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-${data.scripts_available ? 'check text-success' : 'times text-danger'} me-2"></i>
                        <span class="fw-medium">Scripts de traduction</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-python me-2 text-info"></i>
                        <span class="fw-medium">${data.python_version || 'Non détecté'}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted fs-sm">
                        Chemin: ${data.script_path || 'Non configuré'}
                    </div>
                </div>
            </div>
        `;
    } else {
        statusContent.innerHTML = `
            <div class="alert alert-warning d-flex align-items-center mb-0">
                <i class="fa fa-exclamation-triangle me-2"></i>
                <span>${data.message || 'Erreur inconnue'}</span>
            </div>
        `;
    }
}

// Handle form submission
document.getElementById('translation-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Vérifier qu'on a soit du JSON collé soit un fichier
    const importMethod = document.querySelector('input[name="import_method"]:checked').value;
    const jsonContent = document.getElementById('json_content').value.trim();
    const fileInput = document.getElementById('workflow_file');

    if (importMethod === 'paste' && !jsonContent) {
        alert('Veuillez coller le contenu JSON du workflow');
        return;
    }

    if (importMethod === 'file' && !fileInput.files.length) {
        alert('Veuillez sélectionner un fichier JSON');
        return;
    }

    const formData = new FormData(this);
    const translateBtn = document.getElementById('translate-btn');
    const originalText = translateBtn.innerHTML;

    // Show loading state
    translateBtn.disabled = true;
    translateBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Traduction en cours...';

    // Hide previous results
    document.getElementById('download-btn').style.display = 'none';
    document.getElementById('copy-btn').style.display = 'none';
    document.getElementById('translated_content').style.display = 'none';
    document.getElementById('log-section').style.display = 'none';

    // Préparer les headers pour l'upload de fichier
    const headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    // Ne pas définir Content-Type pour les uploads de fichier (laisse le navigateur le faire)
    fetch('{{ route("admin.tools.workflow-translation.translate") }}', {
        method: 'POST',
        body: formData,
        headers: headers
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(`HTTP ${response.status}: ${err.message || 'Erreur serveur'}`);
            });
        }
        return response.json();
    })
    .then(data => {
        translateBtn.disabled = false;
        translateBtn.innerHTML = originalText;

        if (data.success) {
            handleTranslationSuccess(data);
        } else {
            handleTranslationError(data);
        }
    })
    .catch(error => {
        translateBtn.disabled = false;
        translateBtn.innerHTML = originalText;

        console.error('Fetch error:', error);
        handleTranslationError({
            message: 'Erreur de communication avec le serveur: ' + error.message
        });
    });
});

// Handle successful translation
function handleTranslationSuccess(data) {
    translatedData = data.translated_content;

    // Show translated content
    const translatedTextarea = document.getElementById('translated_content');
    translatedTextarea.value = data.translated_content;
    translatedTextarea.style.display = 'block';

    // Hide placeholder
    document.getElementById('translation-result').style.display = 'none';

    // Show action buttons
    document.getElementById('download-btn').style.display = 'inline-block';
    document.getElementById('copy-btn').style.display = 'inline-block';

    // Show log if available
    if (data.script_output) {
        document.getElementById('translation-log').textContent = data.script_output;
        document.getElementById('log-section').style.display = 'block';
    }

    // Show success modal
    new bootstrap.Modal(document.getElementById('successModal')).show();
}

// Handle translation error
function handleTranslationError(data) {
    const resultDiv = document.getElementById('translation-result');
    resultDiv.innerHTML = `
        <div class="alert alert-danger">
            <h4 class="alert-heading">
                <i class="fa fa-exclamation-triangle me-2"></i>Erreur de traduction
            </h4>
            <p class="mb-0">${data.message || 'Une erreur inconnue s\'est produite'}</p>
            ${data.script_output ? `
                <hr>
                <pre class="mb-0 fs-sm">${data.script_output}</pre>
            ` : ''}
        </div>
    `;
    resultDiv.style.display = 'block';
}

// Download translated workflow
function downloadTranslated() {
    if (!translatedData) return;

    const workflowName = document.getElementById('workflow_name').value || 'workflow_traduit';
    const filename = workflowName.replace(/[^a-zA-Z0-9_-]/g, '_') + '_FR.json';

    const formData = new FormData();
    formData.append('translated_json', translatedData);
    formData.append('filename', filename);

    fetch('{{ route("admin.tools.workflow-translation.download") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Erreur lors du téléchargement');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Download error:', error);
        alert('Erreur lors du téléchargement du fichier');
    });
}

// Copy to clipboard
function copyToClipboard() {
    const textarea = document.getElementById('translated_content');
    textarea.select();
    document.execCommand('copy');

    // Show feedback
    const copyBtn = document.getElementById('copy-btn');
    const originalText = copyBtn.innerHTML;
    copyBtn.innerHTML = '<i class="fa fa-check me-1"></i>Copié!';
    copyBtn.classList.remove('btn-alt-secondary');
    copyBtn.classList.add('btn-success');

    setTimeout(() => {
        copyBtn.innerHTML = originalText;
        copyBtn.classList.remove('btn-success');
        copyBtn.classList.add('btn-alt-secondary');
    }, 2000);
}

// Clear input
function clearInput() {
    document.getElementById('workflow_name').value = '';
    document.getElementById('json_content').value = '';

    // Reset output
    document.getElementById('translated_content').style.display = 'none';
    document.getElementById('translation-result').style.display = 'block';
    document.getElementById('translation-result').innerHTML = `
        <div class="text-center py-5">
            <i class="fa fa-language fa-3x text-muted"></i>
            <h4 class="fw-semibold text-muted mt-3">Prêt pour la traduction</h4>
            <p class="text-muted">
                Collez votre JSON dans le panneau de gauche et cliquez sur "Traduire"
            </p>
        </div>
    `;

    document.getElementById('download-btn').style.display = 'none';
    document.getElementById('copy-btn').style.display = 'none';
    document.getElementById('log-section').style.display = 'none';

    translatedData = null;
}

// Toggle log visibility
function toggleLog() {
    const logContent = document.getElementById('log-content');
    const toggleText = document.getElementById('log-toggle-text');

    if (logContent.style.display === 'none') {
        logContent.style.display = 'block';
        toggleText.textContent = 'Masquer';
    } else {
        logContent.style.display = 'none';
        toggleText.textContent = 'Afficher';
    }
}
</script>
@endsection
<!DOCTYPE html>
<html>
<head>
    <title>Traduction Workflows - Debug</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .error { color: red; background: #fee; padding: 10px; margin: 10px 0; }
        .success { color: green; background: #efe; padding: 10px; margin: 10px 0; }
        textarea { width: 100%; height: 200px; }
        button { padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🔧 Traduction de Workflows (Mode Debug)</h1>

    @if(isset($error))
        <div class="error">
            <strong>Erreur détectée :</strong> {{ $error }}
        </div>
    @endif

    <p><strong>Statut :</strong> Page de secours chargée avec succès</p>

    <form id="simple-form">
        @csrf
        <h3>Test de traduction simple</h3>

        <label>Nom du workflow :</label><br>
        <input type="text" name="workflow_name" value="Test Workflow" style="width: 300px;"><br><br>

        <label>JSON du workflow :</label><br>
        <textarea name="json_content" placeholder="Collez votre JSON ici...">{
  "name": "Simple Test",
  "nodes": [
    {
      "name": "HTTP Request",
      "type": "n8n-nodes-base.httpRequest"
    }
  ],
  "connections": {},
  "tags": []
}</textarea><br><br>

        <button type="submit">🌐 Traduire</button>
    </form>

    <div id="result" style="margin-top: 20px;"></div>

    <script>
        console.log('Script simple chargé');

        document.getElementById('simple-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');

            resultDiv.innerHTML = '<p>⏳ Traduction en cours...</p>';

            fetch('{{ route("admin.tools.workflow-translation.translate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                console.log('Réponse:', response);
                return response.json();
            })
            .then(data => {
                console.log('Données:', data);
                if (data.success) {
                    resultDiv.innerHTML = '<div class="success"><h3>✅ Succès!</h3><pre>' +
                        JSON.stringify(data.translated_json, null, 2) + '</pre></div>';
                } else {
                    resultDiv.innerHTML = '<div class="error"><h3>❌ Erreur:</h3><p>' +
                        data.message + '</p></div>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                resultDiv.innerHTML = '<div class="error"><h3>💥 Erreur réseau:</h3><p>' +
                    error.message + '</p></div>';
            });
        });

        // Test de l'API de statut
        fetch('{{ route("admin.tools.workflow-translation.status") }}')
            .then(response => response.json())
            .then(data => {
                console.log('Statut API:', data);
                document.body.insertAdjacentHTML('beforeend',
                    '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0;">' +
                    '<strong>Statut API:</strong> ' + JSON.stringify(data) + '</div>'
                );
            })
            .catch(error => {
                console.error('Erreur statut:', error);
            });
    </script>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Email Configuration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">🧪 Test Email Configuration</h3>
                    <p class="text-gray-600 mt-1">Debug email sending issues</p>
                </div>
                
                <div class="p-6">
                    <form id="testEmailForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Tujuan</label>
                            <input type="email" id="testEmail" name="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="test@example.com">
                        </div>
                        
                        <button type="submit" 
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            🚀 Test Email
                        </button>
                    </form>
                    
                    <div id="result" class="mt-6 hidden"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('testEmailForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('testEmail').value;
            const resultDiv = document.getElementById('result');
            
            // Show loading
            resultDiv.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-blue-800">⏳ Mengirim test email...</p>
                </div>
            `;
            resultDiv.classList.remove('hidden');
            
            try {
                const response = await fetch('/test-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold text-green-800 mb-2">✅ Test Email Berhasil!</h4>
                            <p class="text-green-700 mb-3">${data.message}</p>
                            <p class="text-sm text-green-600">Dikirim ke: ${data.sent_to}</p>
                            
                            <div class="mt-4 p-3 bg-green-100 rounded">
                                <h5 class="font-medium text-green-800 mb-2">Konfigurasi Email:</h5>
                                <pre class="text-xs text-green-700">${JSON.stringify(data.config, null, 2)}</pre>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="font-semibold text-red-800 mb-2">❌ Test Email Gagal!</h4>
                            <p class="text-red-700 mb-3">${data.message}</p>
                            
                            <div class="mt-4 p-3 bg-red-100 rounded">
                                <h5 class="font-medium text-red-800 mb-2">Konfigurasi Email:</h5>
                                <pre class="text-xs text-red-700">${JSON.stringify(data.config, null, 2)}</pre>
                            </div>
                            
                            ${data.error_details ? `
                                <div class="mt-4 p-3 bg-red-100 rounded">
                                    <h5 class="font-medium text-red-800 mb-2">Error Details:</h5>
                                    <p class="text-xs text-red-700">File: ${data.error_details.file}</p>
                                    <p class="text-xs text-red-700">Line: ${data.error_details.line}</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-semibold text-red-800 mb-2">❌ Network Error!</h4>
                        <p class="text-red-700">${error.message}</p>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>

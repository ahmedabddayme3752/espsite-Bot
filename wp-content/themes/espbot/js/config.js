const CONFIG = {
    API_URL: 'http://192.168.102.74:8000',  // Backend server URL
    API_ENDPOINTS: {
        QUERY: '/api/query',
        HEALTH: '/health',
        MESSAGE: '/api/message'
    }
};

// Load the message template
let messageTemplate = null;

// Function to load the message template
async function loadMessageTemplate() {
    try {
        const response = await fetch('/templates/body.json');
        messageTemplate = await response.json();
    } catch (error) {
        console.error('Error loading message template:', error);
        throw error;
    }
}

// Load the template when the script initializes
loadMessageTemplate();

// Function to send message to backend
async function sendMessage(message, language = 'fr', credentials = { username: "user", password: "" }) {
    try {
        const messageData = {
            data: message,
            RAGConfig: {
                Reader: {
                    selected: "Default",
                    components: {
                        Default: {
                            name: "Default",
                            variables: [],
                            library: ["pypdf", "docx", "spacy"],
                            description: "Ingests text, code, PDF, and DOCX files",
                            config: {},
                            type: "FILE",
                            available: true
                        }
                    }
                },
                Chunker: {
                    selected: "Token",
                    components: {
                        Token: {
                            name: "Token",
                            variables: [],
                            library: [],
                            description: "Splits documents based on word tokens",
                            config: {
                                Tokens: {
                                    type: "number",
                                    value: 250,
                                    description: "Choose how many Token per chunks",
                                    values: []
                                },
                                Overlap: {
                                    type: "number",
                                    value: 50,
                                    description: "Choose how many Tokens should overlap between chunks",
                                    values: []
                                }
                            },
                            type: "",
                            available: true
                        }
                    }
                },
                Embedder: {
                    selected: "Ollama",
                    components: {
                        Ollama: {
                            name: "Ollama",
                            variables: [],
                            library: [],
                            description: "Vectorizes documents and queries using Ollama",
                            config: {
                                Model: {
                                    type: "dropdown",
                                    value: "llama3.2:latest",
                                    description: "Select a installed Ollama model",
                                    values: ["llama3.2:latest"]
                                }
                            },
                            type: "",
                            available: true
                        }
                    }
                },
                Retriever: {
                    selected: "Advanced",
                    components: {
                        Advanced: {
                            name: "Advanced",
                            variables: [],
                            library: [],
                            description: "Retrieve relevant chunks from Weaviate",
                            config: {
                                Suggestion: {
                                    type: "bool",
                                    value: 1,
                                    description: "Enable Autocomplete Suggestions",
                                    values: []
                                },
                                "Search Mode": {
                                    type: "dropdown",
                                    value: "Hybrid Search",
                                    description: "Switch between search types.",
                                    values: ["Hybrid Search"]
                                }
                            },
                            type: "",
                            available: true
                        }
                    }
                },
                Generator: {
                    selected: "Ollama",
                    components: {
                        Ollama: {
                            name: "Ollama",
                            variables: [],
                            library: [],
                            description: "Generate answers using Ollama",
                            config: {
                                Model: {
                                    type: "dropdown",
                                    value: "llama3.2:latest",
                                    description: "Select an installed Ollama model",
                                    values: ["llama3.2:latest"]
                                }
                            },
                            type: "",
                            available: true
                        }
                    }
                }
            }
        };

        console.log('Sending request to:', `${CONFIG.API_URL}${CONFIG.API_ENDPOINTS.QUERY}`);
        console.log('Request payload:', messageData);

        const response = await fetch(`${CONFIG.API_URL}${CONFIG.API_ENDPOINTS.QUERY}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Origin': window.location.origin
            },
            credentials: 'include',
            body: JSON.stringify(messageData)
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server response error:', response.status, errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Server response:', data);
        return data;
    } catch (error) {
        console.error('Error sending message:', error);
        throw error;
    }
}

// Health check function
async function checkHealth() {
    try {
        const response = await fetch(`${CONFIG.API_URL}${CONFIG.API_ENDPOINTS.HEALTH}`);
        return response.ok;
    } catch (error) {
        console.error('Health check failed:', error);
        return false;
    }
}

// Export the configuration and functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        CONFIG,
        sendMessage,
        loadMessageTemplate,
        checkHealth
    };
} else {
    // For browser environment
    window.CONFIG = CONFIG;
    window.sendMessage = sendMessage;
    window.loadMessageTemplate = loadMessageTemplate;
    window.checkHealth = checkHealth;
}

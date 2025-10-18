// Chatbot JavaScript - Student Panel

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const toggleBtn = document.getElementById('chatbot-toggle');
    const chatWindow = document.getElementById('chatbot-window');
    const minimizeBtn = document.getElementById('chatbot-minimize');
    const messagesArea = document.getElementById('chatbot-messages');
    const inputField = document.getElementById('chatbot-input');
    const chatForm = document.getElementById('chatbot-form');
    const typingIndicator = document.getElementById('typing-indicator');
    const quickActions = document.querySelectorAll('.quick-action-btn');
    const statusElement = document.querySelector('.chatbot-status');
    const statusDot = document.querySelector('.status-dot');

    // State
    let isOpen = false;
    let messageCount = 0;
    let ragServiceOnline = false;

    // Check RAG service status on load
    checkRAGServiceStatus();
    
    // Periodic status check (every 30 seconds)
    setInterval(checkRAGServiceStatus, 30000);

    // Function to check RAG service status
    async function checkRAGServiceStatus() {
        try {
            const response = await fetch('/rag-health', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (response.ok) {
                const data = await response.json();
                
                // Determine mode based on response
                if (data.status === 'healthy' && data.rag_service === true) {
                    // Mode 1: RAG Active
                    updateServiceStatus(true, 'rag_active', data.ui_indicator, data.ui_text);
                } else if (data.status === 'limited' && data.fallback_available === true) {
                    // Mode 2: Limited Mode (Laravel fallback)
                    updateServiceStatus(false, 'limited', data.ui_indicator, data.ui_text);
                } else {
                    // Mode 3: Offline Mode
                    updateServiceStatus(false, 'offline', '🔴', 'Offline - Limited Mode');
                }
            } else {
                // HTTP error - Laravel not responding
                updateServiceStatus(false, 'offline', '🔴', 'Offline - Limited Mode');
            }
        } catch (error) {
            // Network error - complete system failure
            updateServiceStatus(false, 'offline', '🔴', 'Offline - Limited Mode');
        }
    }
    // Function to update UI based on service status
    function updateServiceStatus(isOnline, mode, indicator, text) {
        ragServiceOnline = isOnline;
        
        if (statusElement && statusDot) {
            if (mode === 'rag_active') {
                // Mode 1: RAG Active - Green
                statusDot.style.backgroundColor = '#10b981'; // Green
                statusElement.innerHTML = `<span class="status-dot" style="background-color: #10b981;"></span> ${indicator} ${text}`;
                statusElement.style.color = '#10b981';
            } else if (mode === 'limited') {
                // Mode 2: Limited Mode - Yellow
                statusDot.style.backgroundColor = '#f59e0b'; // Yellow
                statusElement.innerHTML = `<span class="status-dot" style="background-color: #f59e0b;"></span> ${indicator} ${text}`;
                statusElement.style.color = '#f59e0b';
            } else {
                // Mode 3: Offline Mode - Red
                statusDot.style.backgroundColor = '#ef4444'; // Red
                statusElement.innerHTML = `<span class="status-dot" style="background-color: #ef4444;"></span> ${indicator} ${text}`;
                statusElement.style.color = '#ef4444';
            }
        }
    }
    
    // Update status from chat response
    function updateServiceStatusFromResponse(modelUsed) {
        if (!statusElement || !statusDot) return;
        
        if (modelUsed === 'qwen/qwen-2.5-72b-instruct:free' || modelUsed === 'deepseek/deepseek-v3.1:free') {
            // RAG Active
            statusDot.style.backgroundColor = '#10b981';
            statusElement.innerHTML = `<span class="status-dot" style="background-color: #10b981;"></span> 🟢 RAG Active`;
            statusElement.style.color = '#10b981';
        } else if (modelUsed === 'fallback' || modelUsed === 'limited') {
            // Limited Mode
            statusDot.style.backgroundColor = '#f59e0b';
            statusElement.innerHTML = `<span class="status-dot" style="background-color: #f59e0b;"></span> 🟡 Limited Mode`;
            statusElement.style.color = '#f59e0b';
        } else {
            // Offline/Error
            statusDot.style.backgroundColor = '#ef4444';
            statusElement.innerHTML = `<span class="status-dot" style="background-color: #ef4444;"></span> 🔴 Offline`;
            statusElement.style.color = '#ef4444';
        }
    }

    // Toggle chat window
    toggleBtn.addEventListener('click', function() {
        isOpen = !isOpen;
        if (isOpen) {
            chatWindow.classList.add('active');
            toggleBtn.classList.add('active');
            inputField.focus();
            // Recheck status when opening
            checkRAGServiceStatus();
        } else {
            chatWindow.classList.remove('active');
            toggleBtn.classList.remove('active');
        }
    });

    // Minimize chat window
    minimizeBtn.addEventListener('click', function() {
        isOpen = false;
        chatWindow.classList.remove('active');
        toggleBtn.classList.remove('active');
    });

    // Quick action buttons
    quickActions.forEach(btn => {
        btn.addEventListener('click', function() {
            const message = this.getAttribute('data-message');
            sendMessage(message);
        });
    });

    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = inputField.value.trim();
        if (message) {
            sendMessage(message);
        }
    });

    // Send message function
    async function sendMessage(message) {
        // Add user message to chat
        addMessage(message, 'user');
        
        // Clear input
        inputField.value = '';
        inputField.focus();
        
        // Show typing indicator
        showTypingIndicator();
        
        // Determine mode based on localStorage setting (for testing)
        const useRAG = localStorage.getItem('chatbot_mode') !== 'normal';
        
        try {
            // Debug logging
            console.log('Sending chatbot request:', { message, mode: useRAG ? 'rag' : 'normal' });
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                console.error('CSRF token not found!');
                throw new Error('CSRF token missing');
            }
            
            // Call OpenRouter RAG API with proper error handling
            const response = await fetch('/student/rag-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message
                }),
                // Add timeout for better UX
                signal: AbortSignal.timeout(30000) // 30 second timeout
            }).catch(error => {
                console.error('Fetch error:', error);
                // Return a mock response for network errors
                return {
                    ok: false,
                    status: 503,
                    json: async () => ({
                        success: true,
                        message: generateOfflineResponse(message),
                        mode: 'offline',
                        error: 'Network error - using offline mode',
                        rag_status: 'offline'
                    })
                };
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            hideTypingIndicator();
            
            if (response.ok) {
                const data = await response.json();
                console.log('Success response data:', data);
                
                // Handle OpenRouter RAG response format
                let responseText = data.message || data.response || '';
                
                // Check if response is empty or just whitespace
                if (!responseText || responseText.trim().length === 0) {
                    responseText = "I apologize, but I'm having trouble generating a response. Please try asking your question again.";
                }
                
                // Get model used from response
                const modelUsed = data.model_used || data.rag_status || 'unknown';
                
                // Build status indicator
                const statusEmoji = {
                    'qwen/qwen-2.5-72b-instruct:free': '🟢',
                    'deepseek/deepseek-v3.1:free': '🟢', 
                    'fallback': '🟡',
                    'limited': '🟡',
                    'offline': '🔴',
                    'error': '🔴'
                }[modelUsed] || '⚪';
                
                const statusText = {
                    'qwen/qwen-2.5-72b-instruct:free': 'Qwen AI',
                    'deepseek/deepseek-v3.1:free': 'DeepSeek AI',
                    'fallback': 'Database',
                    'limited': 'Limited',
                    'offline': 'Offline',
                    'error': 'Error'
                }[modelUsed] || modelUsed;
                
                // Prepare service info object with updated model indicator
                const serviceInfo = {
                    indicator: `${statusEmoji} ${statusText}`
                };
                
                // Update header status to match current model
                updateServiceStatusFromResponse(modelUsed);
                
                // Add message directly (no typing animation)
                addMessageDirect(responseText, '', 'bot', serviceInfo);
                
                // Show action buttons immediately
                if (data.actions && data.actions.length > 0) {
                    addActionButtons(data.actions);
                }
                
                // Show follow-up questions immediately
                if (data.follow_up_questions && data.follow_up_questions.length > 0) {
                    addFollowUpQuestions(data.follow_up_questions);
                }
            } else {
                // API error - check if it's authentication or other error
                console.log('Error response, status:', response.status);
                
                if (response.status === 401) {
                    // Authentication required - user needs to login
                    console.log('Authentication required - user not logged in');
                    const authMessage = "Please log in to use the AI assistant. Once logged in, you'll have access to intelligent responses and real-time data.";
                    const statusIndicator = '<div style="margin-top: 8px; padding: 6px 10px; background: #fef3c7; border-left: 3px solid #f59e0b; border-radius: 4px; font-size: 12px; color: #92400e;"><strong>🔐 Authentication Required:</strong> Please log in to access the AI assistant.</div>';
                    addMessage(authMessage + statusIndicator, 'bot');
                } else if (response.status === 503 || response.status === 500) {
                    // Server error - try to get Laravel fallback response
                    try {
                        const errorData = await response.json();
                        console.log('Error response data:', errorData);
                        
                        if (errorData.message && errorData.model_used === 'limited') {
                            // Laravel provided a limited mode response
                            const statusIndicator = '<div style="margin-top: 8px; padding: 6px 10px; background: #fef3c7; border-left: 3px solid #f59e0b; border-radius: 4px; font-size: 12px; color: #92400e;"><strong>🟡 Limited Mode:</strong> AI assistant is offline. Using database responses.</div>';
                            addMessage(errorData.message + statusIndicator, 'bot');
                        } else {
                            console.log('No limited mode response, using offline fallback');
                            const fallbackResponse = generateOfflineResponse(message);
                            const statusIndicator = '<div style="margin-top: 8px; padding: 6px 10px; background: #fee2e2; border-left: 3px solid #dc2626; border-radius: 4px; font-size: 12px; color: #991b1b;"><strong>🔴 Offline Mode:</strong> AI service unavailable. Please use the portal navigation.</div>';
                            addMessage(fallbackResponse + statusIndicator, 'bot');
                        }
                    } catch (parseError) {
                        console.error('Error parsing error response:', parseError);
                        const fallbackResponse = generateOfflineResponse(message);
                        const statusIndicator = '<div style="margin-top: 8px; padding: 6px 10px; background: #fee2e2; border-left: 3px solid #dc2626; border-radius: 4px; font-size: 12px; color: #991b1b;"><strong>🔴 Offline Mode:</strong> AI service unavailable. Please use the portal navigation.</div>';
                        addMessage(fallbackResponse + statusIndicator, 'bot');
                    }
                } else {
                    // Other HTTP errors - use offline fallback
                    console.log('HTTP error ' + response.status + ', using offline fallback');
                    const fallbackResponse = generateOfflineResponse(message);
                    const statusIndicator = '<div style="margin-top: 8px; padding: 6px 10px; background: #fee2e2; border-left: 3px solid #dc2626; border-radius: 4px; font-size: 12px; color: #991b1b;"><strong>🔴 Offline Mode:</strong> AI service unavailable. Please use the portal navigation.</div>';
                    addMessage(fallbackResponse + statusIndicator, 'bot');
                }
            }
        } catch (error) {
            console.error('Chatbot API error:', error);
            hideTypingIndicator();
            
            // Network error - use fallback with proper status indicator
            const fallbackResponse = generateOfflineResponse(message);
            const statusIndicator = '<div style="margin-top: 8px; padding: 6px 10px; background: #fee2e2; border-left: 3px solid #dc2626; border-radius: 4px; font-size: 12px; color: #991b1b;"><strong>🔴 Offline Mode:</strong> Network error. AI service unavailable.</div>';
            addMessage(fallbackResponse + statusIndicator, 'bot');
        }
    }

    // Format markdown to HTML
    function formatMarkdown(text) {
        if (!text) return '';
        
        console.log('Input text:', text);
        
        // More aggressive preprocessing for inline lists
        // Handle the specific pattern: "text: • item1 • item2 • item3"
        text = text.replace(/:\s*•\s*/g, ':\n• ');
        text = text.replace(/\.\s*•\s*/g, '.\n• ');
        text = text.replace(/\?\s*•\s*/g, '?\n• ');
        text = text.replace(/!\s*•\s*/g, '!\n• ');
        
        // Split on bullet points that appear anywhere in text
        text = text.replace(/\s+•\s+/g, '\n• ');
        
        // Handle dash lists similarly (both regular dash and em-dash)
        text = text.replace(/:\s*[-–]\s*/g, ':\n– ');
        text = text.replace(/\.\s*[-–]\s*/g, '.\n– ');
        text = text.replace(/\?\s*[-–]\s*/g, '?\n– ');
        text = text.replace(/!\s*[-–]\s*/g, '!\n– ');
        text = text.replace(/\s+[-–]\s+/g, '\n– ');
        
        console.log('After preprocessing:', text);
        
        // Convert **bold** to <strong>
        text = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        
        // Convert emoji + bold text pattern (📝 **text**)
        text = text.replace(/(📝|✅|⚠️|❌|🎯|💡|📊|🔥|⏰|📚)\s*\*\*([^*]+)\*\*/g, '$1 <strong>$2</strong>');
        
        // Convert line breaks
        text = text.replace(/\n/g, '<br>');
        
        // Split into lines for processing
        const lines = text.split('<br>');
        let formatted = [];
        let inList = false;
        let listType = null;
        
        console.log('Lines to process:', lines);
        
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const trimmed = line.trim();
            
            // Skip empty lines but preserve spacing
            if (trimmed.length === 0) {
                if (inList) {
                    formatted.push(`</${listType}>`);
                    inList = false;
                    listType = null;
                }
                formatted.push('<div style="height: 12px;"></div>'); // Blank line spacing
                continue;
            }
            
            // Check for bullet points (•)
            if (trimmed.startsWith('•')) {
                if (!inList || listType !== 'ul') {
                    if (inList) formatted.push(`</${listType}>`);
                    formatted.push('<ul style="margin: 12px 0; padding-left: 0; list-style-type: none;">');
                    inList = true;
                    listType = 'ul';
                }
                let content = trimmed.substring(1).trim();
                // Capitalize first letter of list item
                content = content.charAt(0).toUpperCase() + content.slice(1);
                formatted.push(`<li style="margin: 8px 0; padding-left: 20px; position: relative; line-height: 1.6;"><span style="color: var(--chatbot-primary, #2563eb); font-weight: bold; position: absolute; left: 0; top: 0;">•</span>${content}</li>`);
            }
            // Check for dash-prefixed lines (assessment details) - handles both "- " and "–"
            else if (trimmed.startsWith('- ') || trimmed.startsWith('–')) {
                if (!inList || listType !== 'details') {
                    if (inList) formatted.push(`</${listType}>`);
                    formatted.push('<ul style="margin: 12px 0; padding-left: 0; list-style-type: none;">');
                    inList = true;
                    listType = 'details';
                }
                let content = trimmed.startsWith('–') ? trimmed.substring(1).trim() : trimmed.substring(2).trim();
                // Capitalize first letter of list item
                content = content.charAt(0).toUpperCase() + content.slice(1);
                formatted.push(`<li style="margin: 6px 0; padding-left: 20px; position: relative; color: var(--chatbot-text, #1e293b); font-size: 14px; line-height: 1.5;"><span style="color: var(--chatbot-primary, #2563eb); position: absolute; left: 0; top: 0;">–</span> ${content}</li>`);
            }
            // Check for numbered lists
            else if (trimmed.match(/^\d+\./)) {
                if (!inList || listType !== 'ol') {
                    if (inList) formatted.push(`</${listType}>`);
                    formatted.push('<ol style="margin: 12px 0; padding-left: 24px; counter-reset: list-counter;">');
                    inList = true;
                    listType = 'ol';
                }
                let content = trimmed.replace(/^\d+\.\s*/, '');
                // Capitalize first letter of list item
                content = content.charAt(0).toUpperCase() + content.slice(1);
                formatted.push(`<li style="margin: 8px 0; line-height: 1.6;">${content}</li>`);
            }
            // Check for emoji-prefixed assessment names (📝 Test3567)
            else if (trimmed.match(/^(📝|✅|⚠️|❌|🎯)\s+/)) {
                if (inList) {
                    formatted.push(`</${listType}>`);
                    inList = false;
                    listType = null;
                }
                formatted.push(`<div style="margin: 16px 0; padding: 12px 16px; background: var(--chatbot-primary-light, #dbeafe); border-left: 4px solid var(--chatbot-primary, #2563eb); border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"><strong style="color: var(--chatbot-primary, #2563eb); font-size: 16px; font-weight: 600;">${trimmed}</strong></div>`);
            }
            // Regular paragraph
            else {
                if (inList) {
                    formatted.push(`</${listType}>`);
                    inList = false;
                    listType = null;
                }
                
                // Style different types of paragraphs
                let style = 'margin: 12px 0; line-height: 1.6; font-size: 15px;';
                
                // Questions or calls to action
                if (trimmed.endsWith('?')) {
                    style += ' font-weight: 600; color: var(--chatbot-text, #1e293b);';
                }
                // Instructions or important info
                else if (trimmed.toLowerCase().includes('click') || trimmed.toLowerCase().includes('ready') || trimmed.toLowerCase().includes('start')) {
                    style += ' color: var(--chatbot-primary, #2563eb); font-weight: 500;';
                }
                // Greeting messages
                else if (trimmed.toLowerCase().includes('hi') || trimmed.toLowerCase().includes('hello')) {
                    style += ' font-weight: 600; color: var(--chatbot-text, #1e293b);';
                }
                
                formatted.push(`<p style="${style}">${trimmed}</p>`);
            }
        }
        
        // Close any open lists
        if (inList) {
            formatted.push(`</${listType}>`);
        }
        
        return formatted.join('');
    }
    
    // Add message directly (no typing animation)
    function addMessageDirect(text, debugInfo = '', sender, serviceInfo = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        messageDiv.style.animationDelay = '0s';
        
        const time = new Date().toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit' 
        });
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'message-bubble';
        
        // Format the message text
        const formattedText = formatMarkdown(text);
        bubbleDiv.innerHTML = formattedText + debugInfo;
        
        // Build model/status indicator
        let statusIndicator = '';
        if (serviceInfo && serviceInfo.indicator) {
            statusIndicator = `<span class="status-indicator" style="display: inline-block; margin-left: 8px; font-size: 11px; opacity: 0.8;">${serviceInfo.indicator}</span>`;
        }
        
        const timeSpan = document.createElement('span');
        timeSpan.className = 'message-time';
        timeSpan.innerHTML = time + statusIndicator;
        
        const avatar = `
            <div class="message-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2a10 10 0 1 0 10 10H12z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </div>
        `;
        
        messageDiv.innerHTML = avatar;
        contentDiv.appendChild(bubbleDiv);
        contentDiv.appendChild(timeSpan);
        messageDiv.appendChild(contentDiv);
        
        // Remove quick actions after first interaction
        if (messageCount === 0 && sender === 'user') {
            const quickActionsDiv = document.querySelector('.quick-actions');
            if (quickActionsDiv) {
                quickActionsDiv.style.display = 'none';
            }
        }
        
        messagesArea.appendChild(messageDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
        messageCount++;
    }
    
    // Keep typing function for backward compatibility
    function addMessageWithTyping(text, debugInfo = '', sender, serviceInfo = null) {
        // Just call the direct version now
        addMessageDirect(text, debugInfo, sender, serviceInfo);
    }
    
    // Add message to chat (for user messages, instant)
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        messageDiv.style.animationDelay = '0s';
        
        const time = new Date().toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit' 
        });
        
        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        <p>${escapeHtml(text)}</p>
                    </div>
                    <span class="message-time">${time}</span>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 1 0 10 10H12z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        ${formatMarkdown(text)}
                    </div>
                    <span class="message-time">${time}</span>
                </div>
            `;
        }
        
        // Remove quick actions after first interaction
        if (messageCount === 0 && sender === 'user') {
            const quickActionsDiv = document.querySelector('.quick-actions');
            if (quickActionsDiv) {
                quickActionsDiv.style.display = 'none';
            }
        }
        
        messagesArea.appendChild(messageDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
        messageCount++;
    }

    // Show typing indicator
    function showTypingIndicator() {
        typingIndicator.style.display = 'flex';
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    // Hide typing indicator
    function hideTypingIndicator() {
        typingIndicator.style.display = 'none';
    }

    // Escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    // Add action buttons from OpenRouter response
    function addActionButtons(actions) {
        if (!actions || actions.length === 0) return;
        
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'message-actions';
        actionsDiv.style.cssText = 'padding: 10px 0; display: flex; gap: 8px; flex-wrap: wrap;';
        
        actions.forEach(action => {
            const button = document.createElement('a');
            button.href = action.url;
            button.className = 'btn btn-sm btn-outline-primary';
            button.style.cssText = 'padding: 6px 12px; font-size: 12px; text-decoration: none; border-radius: 4px; border: 1px solid #667eea; color: #667eea;';
            button.textContent = action.label;
            button.target = '_self';
            actionsDiv.appendChild(button);
        });
        
        messagesArea.appendChild(actionsDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    // Generate bot response (DEPRECATED - use generateOfflineResponse instead)
    // Keeping this as an alias for backward compatibility
    function generateBotResponse(message) {
        return generateOfflineResponse(message);
    }

    // Keyboard shortcut to open chatbot (Ctrl+/)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === '/') {
            e.preventDefault();
            toggleBtn.click();
        }
    });

    // Close chatbot when clicking outside
    document.addEventListener('click', function(e) {
        if (isOpen && !chatWindow.contains(e.target) && !toggleBtn.contains(e.target)) {
            // Optional: uncomment to close chat when clicking outside
            // isOpen = false;
            // chatWindow.classList.remove('active');
            // toggleBtn.classList.remove('active');
        }
    });

    // Auto-open chatbot for first-time users (optional)
    const hasSeenChatbot = localStorage.getItem('hasSeenChatbot');
    if (!hasSeenChatbot) {
        setTimeout(() => {
            // Show notification dot
            const notification = toggleBtn.querySelector('.chatbot-notification');
            if (notification) {
                notification.classList.add('show');
            }
            
            // Optional: auto-open after delay
            // setTimeout(() => {
            //     toggleBtn.click();
            //     localStorage.setItem('hasSeenChatbot', 'true');
            // }, 3000);
        }, 2000);
    }

    // Enter key to send (already handled by form submission)
    inputField.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });
    
    // Add action buttons to chat
    function addActionButtons(actions) {
        if (!actions || actions.length === 0) return;
        
        const actionContainer = document.createElement('div');
        actionContainer.className = 'chat-actions';
        actionContainer.style.cssText = 'display: flex; flex-wrap: wrap; gap: 8px; margin: 12px 0;';
        
        actions.forEach(action => {
            const button = document.createElement('button');
            button.className = 'action-button';
            button.style.cssText = `
                padding: 6px 12px;
                background: #f3f4f6;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.2s;
            `;
            
            // Add icon if provided
            const icon = action.icon ? `<span style="margin-right: 4px;">${getActionIcon(action.icon)}</span>` : '';
            button.innerHTML = `${icon}${action.label}`;
            
            // Add click handler
            button.addEventListener('click', () => {
                if (action.url) {
                    window.location.href = action.url;
                } else if (action.action === 'retry') {
                    // Retry last message
                    const lastUserMessage = messagesArea.querySelector('.message-user:last-of-type');
                    if (lastUserMessage) {
                        const text = lastUserMessage.textContent.trim();
                        sendMessage(text);
                    }
                }
            });
            
            // Hover effect
            button.addEventListener('mouseenter', () => {
                button.style.background = '#e5e7eb';
            });
            button.addEventListener('mouseleave', () => {
                button.style.background = '#f3f4f6';
            });
            
            actionContainer.appendChild(button);
        });
        
        messagesArea.appendChild(actionContainer);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
    
    // Add follow-up questions
    function addFollowUpQuestions(questions) {
        if (!questions || questions.length === 0) return;
        
        const container = document.createElement('div');
        container.className = 'follow-up-questions';
        container.style.cssText = 'margin: 12px 0;';
        
        const label = document.createElement('div');
        label.style.cssText = 'font-size: 12px; color: #6b7280; margin-bottom: 8px;';
        label.textContent = 'Suggested questions:';
        container.appendChild(label);
        
        const questionsDiv = document.createElement('div');
        questionsDiv.style.cssText = 'display: flex; flex-direction: column; gap: 6px;';
        
        questions.forEach(question => {
            const chip = document.createElement('button');
            chip.className = 'follow-up-chip';
            chip.style.cssText = `
                padding: 8px 12px;
                background: white;
                border: 1px solid #d1d5db;
                border-radius: 16px;
                font-size: 13px;
                text-align: left;
                cursor: pointer;
                transition: all 0.2s;
            `;
            chip.textContent = question;
            
            chip.addEventListener('click', () => {
                sendMessage(question);
            });
            
            chip.addEventListener('mouseenter', () => {
                chip.style.background = '#f9fafb';
                chip.style.borderColor = '#9ca3af';
            });
            chip.addEventListener('mouseleave', () => {
                chip.style.background = 'white';
                chip.style.borderColor = '#d1d5db';
            });
            
            questionsDiv.appendChild(chip);
        });
        
        container.appendChild(questionsDiv);
        messagesArea.appendChild(container);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
    
    // Get icon for action buttons
    function getActionIcon(iconName) {
        const icons = {
            'eye': '👁️',
            'list': '📋',
            'clock': '🕐',
            'home': '🏠',
            'book-open': '📖',
            'file-text': '📄',
            'refresh': '🔄',
            'help-circle': '❓'
        };
        return icons[iconName] || '▶';
    }
    
    // Generate offline response (no hardcoded assessment names)
    function generateOfflineResponse(message) {
        const lowerMessage = message.toLowerCase();
        
        // Greetings
        if (lowerMessage.includes('hi') || lowerMessage.includes('hello') || lowerMessage.includes('hey')) {
            return formatMarkdown("**Hello! 👋**\n\nI'm currently in offline mode and can't provide AI-powered assistance.\n\n**You can still:**\n• View **Assessments** from the sidebar\n• Check **History** for your results\n• Manage your **Profile**\n• Use the navigation menu\n\nThe AI assistant will be back online soon!");
        }
        
        // Assessment queries
        if (lowerMessage.includes('assessment') || lowerMessage.includes('test') || lowerMessage.includes('quiz') || lowerMessage.includes('exam')) {
            return formatMarkdown("**📝 Assessments**\n\nI'm in offline mode and can't access live assessment data.\n\n**To view assessments:**\n• Click **'Assessments'** in the sidebar\n• You'll see all available tests with details\n• Start any test when ready\n\nThe AI assistant will return when back online.");
        }
        
        // Results/Score queries
        if (lowerMessage.includes('result') || lowerMessage.includes('score') || lowerMessage.includes('grade') || lowerMessage.includes('performance')) {
            return formatMarkdown("**📊 Results**\n\nI'm in offline mode and can't access your results.\n\n**To check your results:**\n• Go to **'History'** from the sidebar\n• You'll see all completed assessments\n• View detailed scores and analysis\n\nThe AI assistant will return when back online.");
        }
        
        // How-to queries
        if (lowerMessage.includes('how') && (lowerMessage.includes('start') || lowerMessage.includes('take') || lowerMessage.includes('do'))) {
            return formatMarkdown("**📚 How to Use the Portal**\n\nI'm in offline mode, but here's quick guidance:\n\n**To take an assessment:**\n1. Go to **'Assessments'** from sidebar\n2. Choose a test and click **'Start Assessment'**\n3. Read instructions carefully\n4. Complete within the time limit\n5. Submit when done\n\n**Tip:** Timer cannot be paused!");
        }
        
        // Profile queries
        if (lowerMessage.includes('profile') || lowerMessage.includes('account') || lowerMessage.includes('settings')) {
            return formatMarkdown("**👤 Profile & Settings**\n\nI'm in offline mode.\n\n**To manage your profile:**\n• Click **'Profile'** in the sidebar\n• Update personal information\n• Change password\n• Adjust preferences\n\nThe AI assistant will return when back online.");
        }
        
        // Help queries
        if (lowerMessage.includes('help')) {
            return formatMarkdown("**🤖 Help**\n\nI'm in offline mode but can guide you:\n\n**Available Features:**\n• 📝 **Assessments** - Take tests\n• 📊 **History** - View results\n• 👤 **Profile** - Manage account\n• 🧭 **Dashboard** - Overview\n\nUse the sidebar to navigate!");
        }
        
        // Thanks/acknowledgment
        if (lowerMessage.includes('thank') || lowerMessage.includes('thanks')) {
            return formatMarkdown("**You're welcome! 😊**\n\nI'm in offline mode right now, but you can still use all portal features through the sidebar navigation.\n\nThe AI assistant will be back soon!");
        }
        
        // Default offline response
        return formatMarkdown("**⚠️ Offline Mode**\n\nI'm currently offline and can't provide AI assistance.\n\n**You can still:**\n• Navigate using the sidebar menu\n• Access all portal features directly\n• View assessments, results, and profile\n\n**The AI service will return shortly.**\n\nAll data is available through the portal pages!");
    }
    
    // Add testing controls (can be removed in production)
    addTestingControls();
    
    // Remove test logging in production
    if (localStorage.getItem('chatbot_debug') === 'true') {
        console.log('Chatbot initialized with debug mode');
    }
    
    function addTestingControls() {
        // Create testing panel
        const testPanel = document.createElement('div');
        testPanel.id = 'chatbot-test-panel';
        testPanel.style.cssText = `
            position: fixed;
            bottom: 90px;
            right: 90px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 9998;
            display: none;
            font-size: 12px;
        `;
        
        testPanel.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 8px;">🧪 Testing Controls</div>
            <button id="test-formatting" style="width: 100%; margin-bottom: 8px; padding: 4px 8px; font-size: 11px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Test Formatting</button>
            <label style="display: flex; align-items: center; margin-bottom: 5px;">
                <input type="radio" name="chatbot_mode" value="rag" ${localStorage.getItem('chatbot_mode') !== 'normal' ? 'checked' : ''}>
                <span style="margin-left: 5px;">RAG Mode (AI)</span>
            </label>
            <label style="display: flex; align-items: center; margin-bottom: 5px;">
                <input type="radio" name="chatbot_mode" value="normal" ${localStorage.getItem('chatbot_mode') === 'normal' ? 'checked' : ''}>
                <span style="margin-left: 5px;">Normal Mode</span>
            </label>
            <label style="display: flex; align-items: center; margin-top: 10px;">
                <input type="checkbox" id="chatbot_debug" ${localStorage.getItem('chatbot_debug') === 'true' ? 'checked' : ''}>
                <span style="margin-left: 5px;">Show Debug Info</span>
            </label>
        `;
        
        document.body.appendChild(testPanel);
        
        // Test formatting button
        const testBtn = testPanel.querySelector('#test-formatting');
        testBtn.addEventListener('click', function() {
            const testMessage = "Hi there! 👋 I'm your placement assistant.\n\nI can help you with:\n• Viewing available assessments\n• Checking your results\n• Taking tests\n• Portal navigation\n\nYou have **1 assessment** ready to take. Would you like to see it?\n\n📝 **Aptitude Assessment**\n– Duration: 30 minutes\n– Pass percentage: 50%\n– Difficulty: Easy\n\nPlease let me know how I can assist you further!";
            addMessageDirect(testMessage, '', 'bot', {indicator: '🟢 Test Mode'});
        });
        
        // Mode switcher event listeners
        const modeRadios = testPanel.querySelectorAll('input[name="chatbot_mode"]');
        modeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                localStorage.setItem('chatbot_mode', this.value);
                console.log('Chatbot mode switched to:', this.value);
            });
        });
        
        // Debug checkbox
        const debugCheckbox = testPanel.querySelector('#chatbot_debug');
        debugCheckbox.addEventListener('change', function() {
            localStorage.setItem('chatbot_debug', this.checked ? 'true' : 'false');
            console.log('Debug mode:', this.checked);
        });
        
        // Toggle test panel with Ctrl+Shift+T
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'T') {
                e.preventDefault();
                testPanel.style.display = testPanel.style.display === 'none' ? 'block' : 'none';
            }
        });
        
        // Add info to chatbot footer
        const chatbotFooter = document.querySelector('.chatbot-footer');
        if (chatbotFooter) {
            chatbotFooter.innerHTML += '<br><small>Press Ctrl+Shift+T for testing controls</small>';
        }
    }
});

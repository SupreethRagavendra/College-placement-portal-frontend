// Enhanced Intelligent Chatbot with Real-Time 3-Mode Support
// Matches site theme: Purple gradient (#667eea to #764ba2)

document.addEventListener('DOMContentLoaded', function() {
    // ==================== Elements ====================
    const toggleBtn = document.getElementById('chatbot-toggle');
    const chatWindow = document.getElementById('chatbot-window');
    const minimizeBtn = document.getElementById('chatbot-minimize');
    const messagesArea = document.getElementById('chatbot-messages');
    const inputField = document.getElementById('chatbot-input');
    const chatForm = document.getElementById('chatbot-form');
    const typingIndicator = document.getElementById('typing-indicator');
    const chatHeader = document.getElementById('chatbot-header');
    const statusDot = document.getElementById('status-dot');
    const assistantStatus = document.getElementById('assistant-status');
    const modeEmoji = document.getElementById('mode-emoji');
    const modeText = document.getElementById('mode-text');
    const clearChatBtn = document.getElementById('clear-chat');
    
    // ==================== State ====================
    let isOpen = false;
    let currentMode = 'checking'; // checking, rag_active, database_only, offline
    let messageCount = 0;
    
    // ==================== Initialize ====================
    console.log('🤖 Chatbot initialized - Mode checking every 2 seconds');
    checkModeStatus(); // Initial check
    setInterval(checkModeStatus, 2000); // Check every 2 seconds for real-time updates
    
    // ==================== Event Listeners ====================
    
    // Toggle Chat Window
    toggleBtn.addEventListener('click', function() {
        isOpen = !isOpen;
        chatWindow.classList.toggle('active', isOpen);
        toggleBtn.classList.toggle('active', isOpen);
        if (isOpen) {
            inputField.focus();
            checkModeStatus(); // Recheck when opening
        }
    });
    
    // Minimize
    minimizeBtn.addEventListener('click', function() {
        isOpen = false;
        chatWindow.classList.remove('active');
        toggleBtn.classList.remove('active');
    });
    
    // Clear Chat
    clearChatBtn.addEventListener('click', function() {
        if (confirm('Clear all messages?')) {
            const welcomeMsg = messagesArea.querySelector('.welcome-message');
            messagesArea.innerHTML = '';
            if (welcomeMsg) messagesArea.appendChild(welcomeMsg.cloneNode(true));
            messageCount = 0;
        }
    });
    
    // Send Message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = inputField.value.trim();
        if (message) {
            sendMessage(message);
            inputField.value = '';
            inputField.style.height = 'auto';
        }
    });
    
    // Auto-resize textarea
    inputField.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        
        // Update character count if element exists
        const charCountEl = document.getElementById('char-count');
        if (charCountEl) {
            const count = this.value.length;
            charCountEl.textContent = `${count}/1000`;
            charCountEl.style.color = count > 900 ? '#ef4444' : '#9ca3af';
        }
    });
    
    // Enter to send (Shift+Enter for new line)
    inputField.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });
    
    // Keyboard shortcut: Ctrl+/ to open chatbot
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === '/') {
            e.preventDefault();
            toggleBtn.click();
        }
    });
    
    // ==================== Mode Status Check ====================
    async function checkModeStatus() {
        try {
            const response = await fetch('/student/rag-health', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                cache: 'no-cache' // Disable caching for health checks
            });
            
            if (response.ok) {
                const data = await response.json();
                console.log('📡 Health check response:', data);
                
                // Check mode from response
                if (data.rag_service === true && data.status === 'healthy') {
                    updateModeUI('rag_active', '🟢', 'RAG Active', '#10b981');
                } else if (data.status === 'limited' || data.mode === 'limited' || data.fallback_available === true) {
                    updateModeUI('database_only', '🟡', 'Limited Mode', '#f59e0b');
                } else {
                    updateModeUI('database_only', '🟡', 'Limited Mode', '#f59e0b');
                }
            } else {
                console.warn('❌ Health check failed:', response.status);
                updateModeUI('database_only', '🟡', 'Limited Mode', '#f59e0b');
            }
        } catch (error) {
            console.error('❌ Mode check error:', error);
            updateModeUI('database_only', '🟡', 'Limited Mode', '#f59e0b');
        }
    }
    
    // ==================== Update Mode UI ====================
    function updateModeUI(mode, emoji, text, color) {
        currentMode = mode;
        console.log(`🔄 Mode updated: ${emoji} ${text}`, { mode, color });
        
        // Update mode badge
        if (modeEmoji) modeEmoji.textContent = emoji;
        if (modeText) modeText.textContent = text;
        
        // Update status dot
        if (statusDot) {
            statusDot.style.backgroundColor = color;
            statusDot.style.boxShadow = `0 0 8px ${color}`;
        }
        
        // Update assistant status text
        if (assistantStatus) {
            assistantStatus.textContent = text;
        }
        
        // Update header gradient
        if (chatHeader) {
            chatHeader.classList.remove('mode-rag-active', 'mode-database-only', 'mode-offline');
            chatHeader.classList.add(`mode-${mode.replace('_', '-')}`);
            
            // Apply gradient based on mode
            let gradient;
            switch(mode) {
                case 'rag_active':
                    gradient = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                    break;
                case 'database_only':
                    gradient = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
                    break;
                case 'offline':
                    gradient = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                    break;
                default:
                    gradient = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            }
            chatHeader.style.background = gradient;
        }
        
        // Update toggle button indicator
        if (toggleBtn) {
            toggleBtn.classList.remove('mode-rag-active', 'mode-database-only', 'mode-offline', 'mode-indicator');
            toggleBtn.classList.add('mode-indicator', `mode-${mode.replace('_', '-')}`);
        }
    }
    
    // ==================== Send Message ====================
    async function sendMessage(message) {
        // Check mode status before sending for immediate update
        await checkModeStatus();
        
        // Add user message
        addMessage(message, 'user');
        
        // Show typing indicator
        showTypingIndicator();
        
        try {
            const csrfToken = getCsrfToken();
            const response = await fetch('/student/rag-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message }),
                signal: AbortSignal.timeout(30000) // 30 second timeout
            });
            
            hideTypingIndicator();
            
            if (response.ok) {
                const data = await response.json();
                console.log('💬 Chat response received:', { mode: data.mode, mode_name: data.mode_name });
                
                // Update mode based on response
                if (data.mode && data.mode_color) {
                    const modeMap = {
                        'rag_active': { emoji: '🟢', text: 'RAG Active' },
                        'database_only': { emoji: '🟡', text: 'Limited Mode' },
                        'limited': { emoji: '🟡', text: 'Limited Mode' },
                        'offline': { emoji: '🔴', text: 'Offline' }
                    };
                    const modeInfo = modeMap[data.mode] || { emoji: '🟡', text: 'Limited Mode' };
                    updateModeUI(data.mode, modeInfo.emoji, modeInfo.text, data.mode_color);
                } else {
                    console.warn('⚠️ No mode info in response, keeping current mode');
                }
                
                // Add bot response
                const botMessage = data.message || data.response || 'No response';
                addMessage(botMessage, 'bot', {
                    mode: data.mode,
                    mode_name: data.mode_name
                });
                
                // Add action buttons if provided
                if (data.actions && data.actions.length > 0) {
                    setTimeout(() => addActionButtons(data.actions), 500);
                }
                
                // Add follow-up questions if provided
                if (data.follow_up_questions && data.follow_up_questions.length > 0) {
                    setTimeout(() => addFollowUpQuestions(data.follow_up_questions), 800);
                }
            } else {
                // Error response
                const errorData = await response.json().catch(() => ({}));
                const errorMessage = errorData.message || 'Sorry, I encountered an error. Please try again.';
                addMessage(errorMessage, 'bot');
                updateModeUI('offline', '🔴', 'Error', '#ef4444');
            }
        } catch (error) {
            console.error('Send message error:', error);
            hideTypingIndicator();
            
            // Offline fallback
            const offlineResponse = getOfflineResponse(message);
            addMessage(offlineResponse, 'bot');
            updateModeUI('offline', '🔴', 'Offline', '#ef4444');
        }
    }
    
    // ==================== Add Message ====================
    function addMessage(text, sender, metadata = {}) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        messageDiv.style.animation = 'fadeInUp 0.3s ease';
        
        const time = new Date().toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit' 
        });
        
        // Add mode badge to bot messages
        let modeBadge = '';
        if (sender === 'bot' && metadata.mode_name) {
            const badgeColor = metadata.mode === 'rag_active' ? '#10b981' : 
                              metadata.mode === 'database_only' ? '#f59e0b' : '#ef4444';
            modeBadge = `<span style="font-size:10px; color:${badgeColor}; margin-left:5px;">${metadata.mode_name}</span>`;
        }
        
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
                        ${escapeHtml(text)}
                    </div>
                    <span class="message-time">${time}</span>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <div class="avatar-ai-small">🤖</div>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        ${formatMarkdown(text)}
                    </div>
                    <span class="message-time">${time}${modeBadge}</span>
                </div>
            `;
        }
        
        messagesArea.appendChild(messageDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
        messageCount++;
    }
    
    // ==================== Add Action Buttons ====================
    function addActionButtons(actions) {
        const actionsDiv = document.createElement('div');
        actionsDiv.style.cssText = 'display:flex; gap:8px; flex-wrap:wrap; padding:0 52px; margin-bottom:15px;';
        
        actions.forEach(action => {
            const btn = document.createElement('button');
            btn.textContent = action.label;
            btn.style.cssText = `
                padding: 8px 16px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 20px;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 2px 8px rgba(102,126,234,0.3);
            `;
            btn.addEventListener('click', () => {
                if (action.url) window.location.href = action.url;
            });
            btn.addEventListener('mouseenter', () => {
                btn.style.transform = 'translateY(-2px)';
                btn.style.boxShadow = '0 4px 12px rgba(102,126,234,0.4)';
            });
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translateY(0)';
                btn.style.boxShadow = '0 2px 8px rgba(102,126,234,0.3)';
            });
            actionsDiv.appendChild(btn);
        });
        
        messagesArea.appendChild(actionsDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
    
    // ==================== Add Follow-Up Questions ====================
    function addFollowUpQuestions(questions) {
        const container = document.createElement('div');
        container.style.cssText = 'padding:0 52px; margin-bottom:15px;';
        
        const label = document.createElement('div');
        label.textContent = 'Quick questions:';
        label.style.cssText = 'font-size:11px; color:#94a3b8; margin-bottom:8px; font-weight:600;';
        container.appendChild(label);
        
        questions.forEach(question => {
            const chip = document.createElement('button');
            chip.textContent = question;
            chip.style.cssText = `
                display: block;
                width: 100%;
                padding: 10px 14px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                font-size: 13px;
                text-align: left;
                cursor: pointer;
                margin-bottom: 6px;
                transition: all 0.2s ease;
            `;
            chip.addEventListener('click', () => {
                inputField.value = question;
                chatForm.dispatchEvent(new Event('submit'));
            });
            chip.addEventListener('mouseenter', () => {
                chip.style.background = '#f9fafb';
                chip.style.borderColor = '#667eea';
                chip.style.transform = 'translateX(4px)';
            });
            chip.addEventListener('mouseleave', () => {
                chip.style.background = 'white';
                chip.style.borderColor = '#e5e7eb';
                chip.style.transform = 'translateX(0)';
            });
            container.appendChild(chip);
        });
        
        messagesArea.appendChild(container);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
    
    // ==================== Typing Indicator ====================
    function showTypingIndicator() {
        if (typingIndicator) {
            typingIndicator.style.display = 'flex';
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }
    }
    
    function hideTypingIndicator() {
        if (typingIndicator) {
            typingIndicator.style.display = 'none';
        }
    }
    
    // ==================== Format Markdown ====================
    function formatMarkdown(text) {
        if (!text) return '';
        
        // Preprocess inline lists
        text = text.replace(/:\s*•\s*/g, ':\n• ');
        text = text.replace(/\s+•\s+/g, '\n• ');
        text = text.replace(/:\s*[-–]\s*/g, ':\n– ');
        
        // Convert **bold**
        text = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        
        // Split into lines
        const lines = text.split('\n');
        let formatted = [];
        let inList = false;
        
        for (let line of lines) {
            const trimmed = line.trim();
            
            if (!trimmed) {
                if (inList) {
                    formatted.push('</ul>');
                    inList = false;
                }
                formatted.push('<div style="height:12px;"></div>');
                continue;
            }
            
            // Bullet points
            if (trimmed.startsWith('•')) {
                if (!inList) {
                    formatted.push('<ul style="margin:12px 0; padding-left:0; list-style:none;">');
                    inList = true;
                }
                const content = trimmed.substring(1).trim();
                formatted.push(`<li style="margin:8px 0; padding-left:20px; position:relative; line-height:1.6;"><span style="color:#667eea; font-weight:bold; position:absolute; left:0;">•</span>${content}</li>`);
            }
            // Dash details
            else if (trimmed.startsWith('–') || trimmed.startsWith('- ')) {
                if (!inList) {
                    formatted.push('<ul style="margin:12px 0; padding-left:0; list-style:none;">');
                    inList = true;
                }
                const content = trimmed.startsWith('–') ? trimmed.substring(1).trim() : trimmed.substring(2).trim();
                formatted.push(`<li style="margin:6px 0; padding-left:20px; position:relative; color:#64748b; font-size:14px;"><span style="color:#667eea; position:absolute; left:0;">–</span> ${content}</li>`);
            }
            // Numbered lists
            else if (trimmed.match(/^\d+\./)) {
                if (inList) {
                    formatted.push('</ul>');
                    inList = false;
                }
                const content = trimmed.replace(/^\d+\.\s*/, '');
                formatted.push(`<p style="margin:8px 0; line-height:1.6;">${trimmed}</p>`);
            }
            // Emoji prefixed (assessment names)
            else if (trimmed.match(/^(📝|✅|⚠️|❌|🎯)\s+/)) {
                if (inList) {
                    formatted.push('</ul>');
                    inList = false;
                }
                formatted.push(`<div style="margin:16px 0; padding:12px 16px; background:#ede9fe; border-left:4px solid #667eea; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05);"><strong style="color:#667eea; font-size:16px;">${trimmed}</strong></div>`);
            }
            // Regular paragraph
            else {
                if (inList) {
                    formatted.push('</ul>');
                    inList = false;
                }
                let style = 'margin:12px 0; line-height:1.6; font-size:15px;';
                if (trimmed.endsWith('?')) {
                    style += ' font-weight:600; color:#1e293b;';
                } else if (trimmed.toLowerCase().includes('click') || trimmed.toLowerCase().includes('ready')) {
                    style += ' color:#667eea; font-weight:500;';
                }
                formatted.push(`<p style="${style}">${trimmed}</p>`);
            }
        }
        
        if (inList) formatted.push('</ul>');
        
        return formatted.join('');
    }
    
    // ==================== Offline Response ====================
    function getOfflineResponse(message) {
        const lower = message.toLowerCase();
        
        if (lower.includes('hi') || lower.includes('hello') || lower.includes('hey')) {
            return '**Hello! 👋**\n\nI\'m in offline mode. Use the sidebar to navigate:\n\n• **Assessments** - View available tests\n• **History** - Check your results\n• **Profile** - Manage account\n\nThe AI will be back soon!';
        }
        
        if (lower.includes('assessment') || lower.includes('test')) {
            return '**📝 Assessments**\n\nI\'m offline and can\'t access live data.\n\n• Click **Assessments** in the sidebar\n• View all available tests\n• Start when ready\n\nThe AI assistant will return soon.';
        }
        
        if (lower.includes('result') || lower.includes('score')) {
            return '**📊 Results**\n\nI\'m offline.\n\n• Go to **History** from sidebar\n• View all completed assessments\n• Check scores and analysis\n\nThe AI will be back online soon.';
        }
        
        return '**⚠️ Offline Mode**\n\nI\'m currently offline.\n\n• Use the sidebar to navigate\n• All features are accessible\n• Data available through portal pages\n\nThe AI service will return shortly.';
    }
    
    // ==================== Utilities ====================
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
    
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
    
    // ==================== Auto-open for first-time users ====================
    const hasSeenChatbot = localStorage.getItem('hasSeenChatbot');
    if (!hasSeenChatbot) {
        setTimeout(() => {
            const notification = toggleBtn.querySelector('.chatbot-notification');
            if (notification) notification.classList.add('show');
        }, 2000);
    }
});


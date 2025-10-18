// Intelligent Chatbot JavaScript with Advanced Features - Part 1

class IntelligentChatbot {
    constructor() {
        // State Management
        this.isOpen = false;
        this.sidebarOpen = true;
        this.currentConversationId = null;
        this.sessionId = this.generateSessionId();
        this.messageCount = 0;
        this.typingTimeout = null;
        this.suggestions = [];
        this.lastMessageId = null;
        this.conversationHistory = [];
        
        // Performance Data Cache
        this.performanceData = null;
        this.lastPerformanceUpdate = null;
        
        // Initialize
        this.init();
    }
    
    init() {
        this.cacheElements();
        this.bindEvents();
        this.loadConversationHistory();
        this.initializeTextarea();
        this.setupKeyboardShortcuts();
        this.checkForNotifications();
    }
    
    cacheElements() {
        // Main elements
        this.toggleBtn = document.getElementById('chatbot-toggle');
        this.chatWindow = document.getElementById('chatbot-window');
        this.minimizeBtn = document.getElementById('chatbot-minimize');
        this.messagesArea = document.getElementById('chatbot-messages');
        this.inputField = document.getElementById('chatbot-input');
        this.chatForm = document.getElementById('chatbot-form');
        this.typingIndicator = document.getElementById('typing-indicator');
        this.typingText = document.getElementById('typing-text');
        
        // Sidebar elements
        this.sidebar = document.querySelector('.chatbot-sidebar');
        this.conversationList = document.getElementById('conversation-list');
        this.newConversationBtn = document.getElementById('new-conversation');
        this.toggleSidebarBtn = document.getElementById('toggle-sidebar');
        this.toggleSidebarMobile = document.getElementById('toggle-sidebar-mobile');
        
        // Enhanced elements
        this.insightsBar = document.getElementById('insights-bar');
        this.suggestionPills = document.getElementById('suggestion-pills');
        this.charCount = document.getElementById('char-count');
        this.sendButton = document.getElementById('send-button');
        this.clearChatBtn = document.getElementById('clear-chat');
        this.attachBtn = document.getElementById('attach-btn');
        this.confidenceIndicator = document.getElementById('confidence-indicator');
        this.assistantStatus = document.getElementById('assistant-status');
        
        // Feedback modal
        this.feedbackModal = document.getElementById('feedback-modal');
    }
    
    bindEvents() {
        // Toggle chat window
        this.toggleBtn.addEventListener('click', () => this.toggleChat());
        this.minimizeBtn.addEventListener('click', () => this.closeChat());
        
        // Form submission
        this.chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });
        
        // Textarea enhancements
        this.inputField.addEventListener('input', () => this.handleInputChange());
        this.inputField.addEventListener('keydown', (e) => this.handleKeyDown(e));
        
        // Sidebar controls
        this.newConversationBtn.addEventListener('click', () => this.startNewConversation());
        this.toggleSidebarBtn.addEventListener('click', () => this.toggleSidebar());
        this.toggleSidebarMobile?.addEventListener('click', () => this.toggleSidebar());
        
        // Clear chat
        this.clearChatBtn.addEventListener('click', () => this.clearCurrentChat());
        
        // Attach button (placeholder)
        this.attachBtn?.addEventListener('click', () => this.handleAttachment());
        
        // Feedback buttons
        document.querySelectorAll('.feedback-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.submitFeedback(e));
        });
        
        // Reaction buttons
        document.querySelectorAll('.reaction-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.addReaction(e));
        });
    }
    
    toggleChat() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.chatWindow.classList.add('active');
            this.toggleBtn.classList.add('active');
            this.inputField.focus();
            this.loadPerformanceInsights();
            
            // Remove notification
            const notification = this.toggleBtn.querySelector('.chatbot-notification');
            if (notification) {
                notification.classList.remove('show');
            }
        } else {
            this.chatWindow.classList.remove('active');
            this.toggleBtn.classList.remove('active');
        }
    }
    
    closeChat() {
        this.isOpen = false;
        this.chatWindow.classList.remove('active');
        this.toggleBtn.classList.remove('active');
    }
    
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        if (this.sidebarOpen) {
            this.sidebar.classList.remove('hidden');
            this.toggleSidebarBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="11 17 7 12 11 7"></polyline>
                    <polyline points="17 17 13 12 17 7"></polyline>
                </svg>
                <span>Hide History</span>
            `;
        } else {
            this.sidebar.classList.add('hidden');
            this.toggleSidebarBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="7 7 11 12 7 17"></polyline>
                    <polyline points="13 7 17 12 13 17"></polyline>
                </svg>
                <span>Show History</span>
            `;
        }
    }
    
    async sendMessage() {
        const message = this.inputField.value.trim();
        if (!message) return;
        
        // Add user message to chat
        this.addMessage(message, 'user');
        
        // Clear input and reset
        this.inputField.value = '';
        this.handleInputChange();
        this.inputField.focus();
        
        // Clear suggestions
        this.clearSuggestions();
        
        // Show typing indicator with context
        this.showTypingIndicator(this.getTypingContext(message));
        
        try {
            // Call intelligent API
            const response = await this.callIntelligentAPI(message);
            
            // Hide typing indicator
            this.hideTypingIndicator();
            
            if (response.success) {
                // Add bot message with rich formatting
                const messageId = this.addMessage(response.response, 'bot', {
                    intent: response.intent,
                    confidence: response.confidence,
                    model: response.model_used
                });
                
                // Store last message ID for feedback
                this.lastMessageId = messageId;
                
                // Update conversation ID if new
                if (response.conversation_id && !this.currentConversationId) {
                    this.currentConversationId = response.conversation_id;
                }
                
                // Show suggestions
                if (response.suggestions && response.suggestions.length > 0) {
                    this.showSuggestions(response.suggestions);
                }
                
                // Update confidence indicator
                this.updateConfidence(response.confidence);
                
                // Update assistant status based on intent
                this.updateAssistantStatus(response.intent);
                
                // Update conversation history
                this.updateConversationList();
            } else {
                this.addMessage('I encountered an error. Please try again.', 'bot');
            }
        } catch (error) {
            console.error('Chat error:', error);
            this.hideTypingIndicator();
            this.addMessage('I\'m having trouble connecting. Please check your connection and try again.', 'bot');
        }
    }
    
    async callIntelligentAPI(message) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        try {
            // MODE 1: Try OpenRouter RAG endpoint (Full AI Power - Green)
            const response = await fetch('/student/rag-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message
                })
            });
            
            if (response.ok) {
                const data = await response.json();
                
                // Update UI based on mode
                this.updateModeIndicator(data.mode, data.mode_name, data.mode_color);
                
                // Transform response to match expected format
                return {
                    success: data.success !== false,
                    response: data.message || data.response,
                    intent: data.query_type || 'general',
                    confidence: data.mode === 'rag_active' ? 0.9 : 0.7,
                    model_used: data.model_used || data.mode_name,
                    suggestions: data.follow_up_questions || [],
                    actions: data.actions || [],
                    mode: data.mode,
                    mode_info: {
                        name: data.mode_name,
                        description: data.mode_description,
                        color: data.mode_color
                    }
                };
            }
            
            // If server returned error, try MODE 3 (Offline)
            throw new Error(`Server error: ${response.status}`);
            
        } catch (error) {
            // MODE 3: Offline mode (Frontend Fallback - Red)
            console.error('API error, switching to offline mode:', error);
            return this.getOfflineResponse(message);
        }
    }
    
    addMessage(text, sender, metadata = {}) {
        const messageId = `msg-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        messageDiv.id = messageId;
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
                        ${this.escapeHtml(text)}
                    </div>
                    <span class="message-time">${time}</span>
                </div>
            `;
        } else {
            // Format bot message with rich content
            const formattedText = this.formatRichContent(text);
            
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <div class="avatar-ai-small">AI</div>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        ${formattedText}
                    </div>
                    <div class="message-meta">
                        <span class="message-time">${time}</span>
                    </div>
                </div>
            `;
        }
        
        this.messagesArea.appendChild(messageDiv);
        this.messagesArea.scrollTop = this.messagesArea.scrollHeight;
        this.messageCount++;
        
        // Remove welcome message after first interaction
        if (this.messageCount === 1 && sender === 'user') {
            const welcomeMessage = document.querySelector('.welcome-message');
            if (welcomeMessage) {
                welcomeMessage.style.display = 'none';
            }
        }
        
        return messageId;
    }
    
    formatRichContent(text) {
        let formatted = text;
        
        // Bold
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Lists
        formatted = formatted.replace(/^\* (.+)$/gm, '<li>$1</li>');
        formatted = formatted.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
        
        // Line breaks
        formatted = formatted.replace(/\n/g, '<br>');
        
        return formatted;
    }
    
    showTypingIndicator(context = 'Thinking') {
        this.typingText.textContent = context;
        this.typingIndicator.style.display = 'flex';
        this.messagesArea.scrollTop = this.messagesArea.scrollHeight;
        this.assistantStatus.textContent = 'Typing...';
    }
    
    hideTypingIndicator() {
        this.typingIndicator.style.display = 'none';
        this.assistantStatus.textContent = 'Ready to help';
    }
    
    getTypingContext(message) {
        const lower = message.toLowerCase();
        
        if (lower.includes('weak') || lower.includes('improve')) {
            return 'Analyzing your weak areas';
        }
        if (lower.includes('score') || lower.includes('result')) {
            return 'Checking your results';
        }
        if (lower.includes('assessment') || lower.includes('test')) {
            return 'Finding assessments';
        }
        if (lower.includes('progress') || lower.includes('trend')) {
            return 'Analyzing your progress';
        }
        
        return 'Processing your request';
    }
    
    showSuggestions(suggestions) {
        this.suggestionPills.innerHTML = '';
        this.suggestions = suggestions;
        
        suggestions.forEach(suggestion => {
            const pill = document.createElement('button');
            pill.className = 'suggestion-pill';
            pill.textContent = suggestion;
            pill.addEventListener('click', () => {
                this.inputField.value = suggestion;
                this.sendMessage();
            });
            
            this.suggestionPills.appendChild(pill);
        });
    }
    
    clearSuggestions() {
        this.suggestionPills.innerHTML = '';
        this.suggestions = [];
    }
    
    updateConfidence(confidence) {
        const percentage = Math.round((confidence || 0.5) * 100);
        let label = 'Low Confidence';
        let color = '#ef4444';
        
        if (percentage >= 80) {
            label = 'High Confidence';
            color = '#10b981';
        } else if (percentage >= 60) {
            label = 'Medium Confidence';
            color = '#f59e0b';
        }
        
        this.confidenceIndicator.textContent = label;
        this.confidenceIndicator.style.color = color;
    }
    
    updateAssistantStatus(intent) {
        const statusMap = {
            'weakness_inquiry': 'Analyzing weaknesses',
            'strength_inquiry': 'Reviewing strengths',
            'strategy_request': 'Preparing strategies',
            'progress_check': 'Checking progress',
            'assessment_query': 'Finding assessments'
        };
        
        const status = statusMap[intent] || 'Ready to help';
        this.assistantStatus.textContent = status;
        
        setTimeout(() => {
            this.assistantStatus.textContent = 'Ready to help';
        }, 2000);
    }
    
    async loadPerformanceInsights() {
        // Placeholder - will be implemented with actual API
        // Loading performance insights...
    }
    
    async loadConversationHistory() {
        try {
            const response = await fetch('/student/conversation-history');
            if (response.ok) {
                const data = await response.json();
                this.conversationHistory = data.conversations || [];
                this.renderConversationList();
            }
        } catch (error) {
            console.error('Failed to load conversation history:', error);
        }
    }
    
    renderConversationList() {
        // Simplified implementation for now
        // Rendering conversation list
    }
    
    updateConversationList() {
        clearTimeout(this.updateTimeout);
        this.updateTimeout = setTimeout(() => {
            this.loadConversationHistory();
        }, 2000);
    }
    
    startNewConversation() {
        this.clearCurrentChat();
        this.currentConversationId = null;
        this.sessionId = this.generateSessionId();
        this.messageCount = 0;
        
        const welcomeMessage = document.querySelector('.welcome-message');
        if (welcomeMessage) {
            welcomeMessage.style.display = 'flex';
        }
        
        this.clearSuggestions();
        this.updateConversationList();
        this.inputField.focus();
    }
    
    clearCurrentChat() {
        const messages = this.messagesArea.querySelectorAll('.message:not(.welcome-message)');
        messages.forEach(msg => msg.remove());
        
        const welcomeMessage = document.querySelector('.welcome-message');
        if (welcomeMessage) {
            welcomeMessage.style.display = 'flex';
        }
        
        this.messageCount = 0;
    }
    
    async submitFeedback(e) {
        const helpful = e.currentTarget.dataset.helpful === 'true';
        // Feedback submitted
        this.feedbackModal.style.display = 'none';
    }
    
    async addReaction(e) {
        const reaction = e.currentTarget.dataset.reaction;
        // Reaction added
        this.feedbackModal.style.display = 'none';
    }
    
    handleAttachment() {
        alert('Attachment feature coming soon!');
    }
    
    handleInputChange() {
        // Auto-resize textarea
        this.inputField.style.height = 'auto';
        this.inputField.style.height = Math.min(this.inputField.scrollHeight, 100) + 'px';
        
        // Update character count
        const length = this.inputField.value.length;
        this.charCount.textContent = `${length}/1000`;
        
        // Enable/disable send button
        this.sendButton.disabled = length === 0 || length > 1000;
        
        // Change color when approaching limit
        if (length > 900) {
            this.charCount.style.color = '#ef4444';
        } else if (length > 800) {
            this.charCount.style.color = '#f59e0b';
        } else {
            this.charCount.style.color = '#9ca3af';
        }
    }
    
    handleKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.chatForm.dispatchEvent(new Event('submit'));
        }
    }
    
    initializeTextarea() {
        this.handleInputChange();
    }
    
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl+/ to open chatbot
            if (e.ctrlKey && e.key === '/') {
                e.preventDefault();
                this.toggleChat();
            }
            
            // Escape to close
            if (e.key === 'Escape' && this.isOpen) {
                this.closeChat();
            }
        });
    }
    
    checkForNotifications() {
        // Check for new messages or updates
        setTimeout(() => {
            const notification = this.toggleBtn.querySelector('.chatbot-notification');
            if (notification && !this.isOpen) {
                notification.classList.add('show');
            }
        }, 5000);
    }
    
    generateSessionId() {
        return 'session-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    }
    
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    /**
     * Update mode indicator in UI
     */
    updateModeIndicator(mode, modeName, modeColor) {
        // Update status indicator
        const statusDot = document.querySelector('.status-dot');
        const assistantStatus = document.getElementById('assistant-status');
        const chatbotHeader = document.querySelector('.chatbot-header');
        const toggleBtn = this.toggleBtn;
        
        if (statusDot && assistantStatus) {
            statusDot.style.background = modeColor;
            assistantStatus.textContent = modeName || 'Active';
            assistantStatus.style.color = 'white';
        }
        
        // Update header background based on mode
        if (chatbotHeader) {
            // Remove all mode classes
            chatbotHeader.classList.remove('mode-rag-active', 'mode-database-only', 'mode-offline');
            
            // Add current mode class
            if (mode) {
                chatbotHeader.classList.add(`mode-${mode.replace('_', '-')}`);
            }
        }
        
        // Update toggle button indicator
        if (toggleBtn) {
            toggleBtn.classList.remove('mode-rag-active', 'mode-database-only', 'mode-offline', 'mode-indicator');
            if (mode) {
                toggleBtn.classList.add('mode-indicator', `mode-${mode.replace('_', '-')}`);
            }
        }
        
        // Add mode badge to header if not exists
        let modeBadge = document.getElementById('mode-badge');
        if (!modeBadge) {
            modeBadge = document.createElement('div');
            modeBadge.id = 'mode-badge';
            modeBadge.style.cssText = `
                position: absolute;
                top: 10px;
                right: 100px;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                color: white;
                transition: all 0.3s ease;
                z-index: 10;
            `;
            if (chatbotHeader) {
                chatbotHeader.appendChild(modeBadge);
            }
        }
        
        modeBadge.textContent = modeName;
        modeBadge.style.backgroundColor = modeColor;
        modeBadge.style.boxShadow = `0 2px 8px ${modeColor}40`;
    }
    
    /**
     * MODE 3: Offline fallback responses
     */
    getOfflineResponse(message) {
        const lowerMessage = message.toLowerCase();
        let response = '';
        let suggestions = [];
        
        // Basic pattern matching for offline mode
        if (lowerMessage.includes('hello') || lowerMessage.includes('hi')) {
            response = "👋 Hello! I'm currently in offline mode. Some features may be limited, but I can still help with basic navigation.";
            suggestions = ['View Assessments', 'Check History'];
        } else if (lowerMessage.includes('assessment') || lowerMessage.includes('test')) {
            response = "📝 To view assessments, please navigate to the Assessments page from the menu. I'm currently in offline mode and can't fetch real-time data.";
            suggestions = ['Go to Assessments'];
        } else if (lowerMessage.includes('result') || lowerMessage.includes('score')) {
            response = "📊 To view your results, please check the History page. I'm currently in offline mode.";
            suggestions = ['Go to History'];
        } else {
            response = "⚠️ I'm currently in offline mode. Please check your connection or try again later.\n\nYou can still navigate the portal using the menu.";
            suggestions = ['Refresh Page', 'View Dashboard'];
        }
        
        // Update UI to offline mode
        this.updateModeIndicator('offline', '🔴 Mode 3: OFFLINE MODE', '#ef4444');
        
        return {
            success: true,
            response: response,
            intent: 'offline',
            confidence: 0.5,
            model_used: '🔴 Mode 3: OFFLINE MODE',
            suggestions: suggestions,
            actions: [],
            mode: 'offline',
            mode_info: {
                name: '🔴 Mode 3: OFFLINE MODE',
                description: 'Frontend Fallback',
                color: '#ef4444'
            }
        };
    }
    
    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Just now';
        if (diff < 3600000) return Math.floor(diff / 60000) + ' min ago';
        if (diff < 86400000) return Math.floor(diff / 3600000) + ' hours ago';
        
        return date.toLocaleDateString();
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.intelligentChatbot = new IntelligentChatbot();
});

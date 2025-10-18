// Simplified Intelligent Chatbot JavaScript

class SimpleIntelligentChatbot {
    constructor() {
        // State
        this.isOpen = false;
        this.sessionId = this.generateSessionId();
        this.messageCount = 0;
        this.currentConversationId = null;
        
        // Initialize
        this.init();
    }
    
    init() {
        this.cacheElements();
        this.bindEvents();
        this.initializeTextarea();
        this.setupKeyboardShortcuts();
        this.loadPerformanceInsights();
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
        
        // Enhanced elements
        this.insightsBar = document.getElementById('insights-bar');
        this.suggestionPills = document.getElementById('suggestion-pills');
        this.sendButton = document.getElementById('send-button');
        this.confidenceIndicator = document.getElementById('confidence-indicator');
        this.assistantStatus = document.getElementById('assistant-status');
        
        // Performance elements
        this.weakAreaElement = document.getElementById('weak-area');
        this.performanceTrendElement = document.getElementById('performance-trend');
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
        
        // Textarea
        this.inputField.addEventListener('input', () => this.handleInputChange());
        this.inputField.addEventListener('keydown', (e) => this.handleKeyDown(e));
        
        // Suggestion pills
        document.querySelectorAll('.suggestion-pill').forEach(pill => {
            pill.addEventListener('click', (e) => {
                const message = e.currentTarget.dataset.message;
                this.inputField.value = message;
                this.sendMessage();
            });
        });
    }
    
    toggleChat() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.chatWindow.classList.add('active');
            this.toggleBtn.classList.add('active');
            this.inputField.focus();
            
            // Load insights when opening
            if (this.messageCount === 0) {
                this.loadPerformanceInsights();
            }
            
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
    
    async sendMessage() {
        const message = this.inputField.value.trim();
        if (!message) return;
        
        // Add user message
        this.addMessage(message, 'user');
        
        // Clear input
        this.inputField.value = '';
        this.handleInputChange();
        this.inputField.focus();
        
        // Show typing indicator with context
        this.showTypingIndicator(this.getTypingContext(message));
        
        try {
            // Call intelligent API
            const response = await this.callIntelligentAPI(message);
            
            // Hide typing indicator
            this.hideTypingIndicator();
            
            if (response.success) {
                // Add bot message
                this.addMessage(response.response, 'bot', {
                    confidence: response.confidence
                });
                
                // Update conversation ID if provided
                if (response.conversation_id && !this.currentConversationId) {
                    this.currentConversationId = response.conversation_id;
                }
                
                // Update suggestions if provided
                if (response.suggestions && response.suggestions.length > 0) {
                    this.updateSuggestions(response.suggestions);
                }
                
                // Update confidence
                this.updateConfidence(response.confidence);
                
                // Update status
                this.updateAssistantStatus(response.intent);
                
                // Update insights if this is a performance-related query
                if (this.isPerformanceQuery(message)) {
                    setTimeout(() => this.loadPerformanceInsights(), 1000);
                }
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
        
        const response = await fetch('/student/intelligent-chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                session_id: this.sessionId,
                conversation_id: this.currentConversationId
            })
        });
        
        if (response.ok) {
            return await response.json();
        } else {
            // Try the original chatbot endpoint as fallback
            const fallbackResponse = await fetch('/student/chatbot-ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    mode: 'rag'
                })
            });
            
            if (fallbackResponse.ok) {
                return await fallbackResponse.json();
            }
            
            throw new Error(`API error: ${response.status}`);
        }
    }
    
    addMessage(text, sender, metadata = {}) {
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            // Format bot message
            const formattedText = this.formatRichContent(text);
            
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <div class="avatar-ai-small">AI</div>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        ${formattedText}
                    </div>
                    <span class="message-time">${time}</span>
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
    }
    
    formatRichContent(text) {
        let formatted = text;
        
        // Convert newlines to <br>
        formatted = formatted.replace(/\n/g, '<br>');
        
        // Bold text
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Lists
        formatted = formatted.replace(/^\* (.+)$/gm, '<li>$1</li>');
        formatted = formatted.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
        
        // Inline code
        formatted = formatted.replace(/`([^`]+)`/g, '<code>$1</code>');
        
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
        this.assistantStatus.textContent = 'Ready to help with placement prep';
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
        if (lower.includes('tip') || lower.includes('advice') || lower.includes('study')) {
            return 'Preparing personalized tips';
        }
        if (lower.includes('focus') || lower.includes('topic')) {
            return 'Identifying focus areas';
        }
        
        return 'Processing your request';
    }
    
    updateSuggestions(suggestions) {
        // Clear existing pills except default ones
        const container = this.suggestionPills;
        
        // Keep only first 4 default pills
        const defaultPills = container.querySelectorAll('.suggestion-pill');
        defaultPills.forEach((pill, index) => {
            if (index >= 4) {
                pill.remove();
            }
        });
        
        // Add new dynamic suggestions (limit to 2)
        suggestions.slice(0, 2).forEach(suggestion => {
            const pill = document.createElement('button');
            pill.className = 'suggestion-pill';
            pill.dataset.message = suggestion;
            pill.innerHTML = `<span class="pill-icon">💭</span> ${suggestion}`;
            pill.addEventListener('click', () => {
                this.inputField.value = suggestion;
                this.sendMessage();
            });
            container.appendChild(pill);
        });
    }
    
    updateConfidence(confidence) {
        const percentage = Math.round((confidence || 0.5) * 100);
        let label = 'AI Confidence: ';
        
        if (percentage >= 80) {
            label += 'High';
            this.confidenceIndicator.style.color = '#10b981';
        } else if (percentage >= 60) {
            label += 'Medium';
            this.confidenceIndicator.style.color = '#f59e0b';
        } else {
            label += 'Low';
            this.confidenceIndicator.style.color = '#ef4444';
        }
        
        this.confidenceIndicator.textContent = label;
    }
    
    updateAssistantStatus(intent) {
        const statusMap = {
            'weakness_inquiry': 'Analyzed your weak areas',
            'strength_inquiry': 'Reviewed your strengths',
            'strategy_request': 'Prepared study strategies',
            'progress_check': 'Checked your progress',
            'assessment_query': 'Found assessments',
            'results_query': 'Reviewed your results'
        };
        
        const status = statusMap[intent] || 'Ready to help with placement prep';
        this.assistantStatus.textContent = status;
        
        // Reset after 3 seconds
        setTimeout(() => {
            this.assistantStatus.textContent = 'Ready to help with placement prep';
        }, 3000);
    }
    
    async loadPerformanceInsights() {
        try {
            const response = await fetch('/student/performance-insights');
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.insights) {
                    this.displayPerformanceInsights(data.insights);
                }
            }
        } catch (error) {
            console.error('Failed to load performance insights:', error);
            // Set default values
            this.weakAreaElement.textContent = 'Take assessments to see';
            this.performanceTrendElement.textContent = 'Not enough data';
        }
    }
    
    displayPerformanceInsights(insights) {
        // Update weak area
        if (insights.weak_areas && insights.weak_areas.length > 0) {
            const weakest = insights.weak_areas[0];
            this.weakAreaElement.textContent = `${weakest.category} (${weakest.accuracy}%)`;
        } else {
            this.weakAreaElement.textContent = 'No weak areas! 🎉';
        }
        
        // Update trend
        const trendMap = {
            'improving': '📈 Improving',
            'declining': '📉 Declining',
            'consistent': '➡️ Consistent',
            'unknown': '🔄 Building data'
        };
        
        this.performanceTrendElement.textContent = trendMap[insights.trend] || 'Analyzing...';
        
        // Update trend color
        if (insights.trend === 'improving') {
            this.performanceTrendElement.style.color = '#059669';
        } else if (insights.trend === 'declining') {
            this.performanceTrendElement.style.color = '#dc2626';
        } else {
            this.performanceTrendElement.style.color = '#6b7280';
        }
    }
    
    isPerformanceQuery(message) {
        const performanceKeywords = ['weak', 'strong', 'performance', 'trend', 'progress', 'improve', 'score', 'result'];
        const lower = message.toLowerCase();
        return performanceKeywords.some(keyword => lower.includes(keyword));
    }
    
    handleInputChange() {
        // Auto-resize textarea
        this.inputField.style.height = 'auto';
        this.inputField.style.height = Math.min(this.inputField.scrollHeight, 80) + 'px';
        
        // Enable/disable send button
        const hasText = this.inputField.value.trim().length > 0;
        this.sendButton.disabled = !hasText;
    }
    
    handleKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.sendMessage();
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
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.simpleIntelligentChatbot = new SimpleIntelligentChatbot();
});

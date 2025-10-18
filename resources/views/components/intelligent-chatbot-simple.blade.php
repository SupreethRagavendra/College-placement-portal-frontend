<!-- Simplified Intelligent Chatbot Widget -->
<div id="intelligent-chatbot-container">
    <!-- Floating Chat Button -->
    <button id="chatbot-toggle" class="chatbot-toggle">
        <svg class="chatbot-icon-open" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2a10 10 0 1 0 10 10H12z"></path>
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"></path>
        </svg>
        <svg class="chatbot-icon-close" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
        <span class="chatbot-notification"></span>
    </button>

    <!-- Simplified Chat Window -->
    <div id="chatbot-window" class="intelligent-chatbot-window-simple">
        <!-- Chat Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <div class="avatar-ai">AI</div>
                </div>
                <div>
                    <h3 class="chatbot-title">Intelligent Assistant</h3>
                    <p class="chatbot-status">
                        <span class="status-dot"></span>
                        <span id="assistant-status">Ready to help with placement prep</span>
                    </p>
                </div>
            </div>
            
            <button id="chatbot-minimize" class="chatbot-minimize">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>
        </div>

        <!-- Performance Insights Bar -->
        <div id="insights-bar" class="insights-bar-simple">
            <div class="insight-badge weak">
                <span class="insight-icon">🎯</span>
                <div>
                    <span class="insight-label">Focus Area</span>
                    <span class="insight-value" id="weak-area">Loading...</span>
                </div>
            </div>
            <div class="insight-badge trend">
                <span class="insight-icon">📈</span>
                <div>
                    <span class="insight-label">Your Trend</span>
                    <span class="insight-value" id="performance-trend">Analyzing...</span>
                </div>
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div id="chatbot-messages" class="chatbot-messages">
            <!-- Welcome Message -->
            <div class="message bot-message welcome-message">
                <div class="message-avatar">
                    <div class="avatar-ai-small">AI</div>
                </div>
                <div class="message-content">
                    <div class="message-bubble rich-content">
                        <h4>👋 Hello {{ Auth::user()->name ?? 'there' }}!</h4>
                        <p>I'm your <strong>intelligent placement assistant</strong> with advanced capabilities:</p>
                        
                        <div class="capabilities-grid">
                            <div class="capability">
                                <span class="cap-icon">📊</span>
                                <span>Performance Analysis</span>
                            </div>
                            <div class="capability">
                                <span class="cap-icon">🎯</span>
                                <span>Weakness Detection</span>
                            </div>
                            <div class="capability">
                                <span class="cap-icon">💡</span>
                                <span>Personalized Tips</span>
                            </div>
                            <div class="capability">
                                <span class="cap-icon">📈</span>
                                <span>Progress Tracking</span>
                            </div>
                        </div>
                        
                        <p class="help-text">Ask me about your weak areas, performance trends, or get personalized study tips!</p>
                    </div>
                    <span class="message-time">Just now</span>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="typing-indicator-enhanced" style="display: none;">
            <div class="typing-avatar">
                <div class="avatar-ai-small">AI</div>
            </div>
            <div class="typing-content">
                <span id="typing-text">Analyzing your performance</span>
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        </div>

        <!-- Smart Suggestions -->
        <div id="suggestion-pills" class="suggestion-pills">
            <button class="suggestion-pill" data-message="What are my weak areas?">
                <span class="pill-icon">🎯</span> Weak Areas
            </button>
            <button class="suggestion-pill" data-message="Show my performance trend">
                <span class="pill-icon">📈</span> Performance
            </button>
            <button class="suggestion-pill" data-message="Give me study tips">
                <span class="pill-icon">💡</span> Study Tips
            </button>
            <button class="suggestion-pill" data-message="Which topics to focus on?">
                <span class="pill-icon">📚</span> Focus Topics
            </button>
        </div>

        <!-- Chat Input Area -->
        <div class="chatbot-input-area">
            <form id="chatbot-form">
                <div class="input-group-enhanced">
                    <textarea 
                        id="chatbot-input" 
                        class="chatbot-input-enhanced" 
                        placeholder="Ask about weak areas, performance, or get personalized tips..."
                        rows="1"></textarea>
                    
                    <button type="submit" class="chatbot-send" id="send-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </div>
            </form>
            
            <div class="chatbot-footer">
                <span id="confidence-indicator">AI Confidence: High</span>
                <span class="separator">•</span>
                <span>Powered by Advanced AI</span>
            </div>
        </div>
    </div>
</div>

<!-- Include Simplified CSS and JS -->
<link rel="stylesheet" href="{{ asset('css/intelligent-chatbot-simple.css') }}">
<script src="{{ asset('js/intelligent-chatbot-simple.js') }}"></script>

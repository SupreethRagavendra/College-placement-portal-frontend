<!-- Intelligent Chatbot Widget with Conversation History -->
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

    <!-- Enhanced Chat Window with Sidebar -->
    <div id="chatbot-window" class="intelligent-chatbot-window">
        <!-- Conversation History Sidebar -->
        <div class="chatbot-sidebar">
            <div class="sidebar-header">
                <h3>Conversations</h3>
                <button id="new-conversation" class="new-conversation-btn" title="New Conversation">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
            </div>
            
            <div class="conversation-list" id="conversation-list">
                <!-- Conversations will be loaded here -->
                <div class="conversation-item active">
                    <div class="conversation-title">Current Session</div>
                    <div class="conversation-preview">Ask me anything...</div>
                    <div class="conversation-time">Now</div>
                </div>
            </div>
            
            <div class="sidebar-footer">
                <button id="toggle-sidebar" class="toggle-sidebar-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="11 17 7 12 11 7"></polyline>
                        <polyline points="17 17 13 12 17 7"></polyline>
                    </svg>
                    <span>Hide History</span>
                </button>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chatbot-main">
            <!-- Chat Header with Mode Badge -->
            <div class="chatbot-header" id="chatbot-header">
                <button id="toggle-sidebar-mobile" class="sidebar-toggle-mobile">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                
                <div class="chatbot-header-info">
                    <div class="chatbot-avatar">
                        <div class="avatar-ai">🤖</div>
                    </div>
                    <div>
                        <h3 class="chatbot-title">Campus AI Assistant</h3>
                        <p class="chatbot-status">
                            <span class="status-dot status-online" id="status-dot"></span>
                            <span id="assistant-status">Online</span>
                        </p>
                    </div>
                </div>
                
                <div class="header-actions">
                    <!-- Mode Badge -->
                    <div id="mode-badge" style="
                        background: rgba(46, 213, 115, 0.2);
                        padding: 6px 12px;
                        border-radius: 20px;
                        font-size: 11px;
                        font-weight: 600;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                        margin-right: 10px;
                        backdrop-filter: blur(10px);
                        box-shadow: 0 2px 8px rgba(46, 213, 115, 0.3);
                        border: 1px solid rgba(46, 213, 115, 0.4);
                    ">
                        <span id="mode-emoji" style="animation: pulse 2s infinite;">🟢</span>
                        <span id="mode-text" style="color: #2ed573;">Online Mode</span>
                    </div>
                    
                    <button id="clear-chat" class="header-action-btn" title="Clear Chat">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                    <button id="chatbot-minimize" class="chatbot-minimize">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Performance Insights Bar (Initially Hidden) -->
            <div id="insights-bar" class="insights-bar" style="display: none;">
                <div class="insight-item">
                    <span class="insight-label">Weak Area:</span>
                    <span class="insight-value" id="weak-area">-</span>
                </div>
                <div class="insight-item">
                    <span class="insight-label">Trend:</span>
                    <span class="insight-value trend" id="performance-trend">-</span>
                </div>
                <div class="insight-item">
                    <span class="insight-label">Next Focus:</span>
                    <span class="insight-value" id="next-focus">-</span>
                </div>
            </div>

            <!-- Chat Messages Area -->
            <div id="chatbot-messages" class="chatbot-messages">
                <!-- Welcome Message with Rich Content -->
                <div class="message bot-message welcome-message">
                    <div class="message-avatar">
                        <div class="avatar-ai-small">AI</div>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble rich-content">
                            <h4>👋 Hello {{ Auth::user()->name ?? 'there' }}!</h4>
                            <p>I'm your intelligent placement assistant with advanced capabilities:</p>
                            
                            <div class="feature-cards">
                                <div class="feature-card">
                                    <span class="feature-icon">📊</span>
                                    <span class="feature-text">Performance Analysis</span>
                                </div>
                                <div class="feature-card">
                                    <span class="feature-icon">🎯</span>
                                    <span class="feature-text">Personalized Tips</span>
                                </div>
                                <div class="feature-card">
                                    <span class="feature-icon">📈</span>
                                    <span class="feature-text">Progress Tracking</span>
                                </div>
                                <div class="feature-card">
                                    <span class="feature-icon">💡</span>
                                    <span class="feature-text">Smart Recommendations</span>
                                </div>
                            </div>
                            
                            <p style="margin-top: 15px;">What would you like to explore today?</p>
                        </div>
                        <span class="message-time">Just now</span>
                    </div>
                </div>
            </div>

            <!-- Typing Indicator with Realistic Animation -->
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

            <!-- Suggestion Pills (Dynamic) -->
            <div id="suggestion-pills" class="suggestion-pills">
                <!-- Will be populated dynamically -->
            </div>

            <!-- Enhanced Chat Input -->
            <div class="chatbot-input-area">
                <form id="chatbot-form">
                    <div class="input-group-enhanced">
                        <button type="button" id="attach-btn" class="attach-btn" title="Attach">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                            </svg>
                        </button>
                        
                        <textarea 
                            id="chatbot-input" 
                            class="chatbot-input-enhanced" 
                            placeholder="Ask about your performance, weak areas, or get study tips..."
                            rows="1"></textarea>
                        
                        <div class="input-actions">
                            <span id="char-count" class="char-count">0/1000</span>
                            <button type="submit" class="chatbot-send" id="send-button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="chatbot-footer">
                    <span>KIT Placement Portal Assistant</span>
                    <span class="separator">•</span>
                    <span id="confidence-indicator">Always Available</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Feedback Modal -->
    <div id="feedback-modal" class="feedback-modal" style="display: none;">
        <div class="feedback-content">
            <h4>Was this helpful?</h4>
            <div class="feedback-buttons">
                <button class="feedback-btn helpful" data-helpful="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                    </svg>
                    Yes
                </button>
                <button class="feedback-btn not-helpful" data-helpful="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path>
                    </svg>
                    No
                </button>
            </div>
            <div class="reaction-emojis">
                <button class="reaction-btn" data-reaction="👍">👍</button>
                <button class="reaction-btn" data-reaction="👎">👎</button>
                <button class="reaction-btn" data-reaction="❤️">❤️</button>
                <button class="reaction-btn" data-reaction="🤔">🤔</button>
                <button class="reaction-btn" data-reaction="💡">💡</button>
            </div>
        </div>
    </div>
</div>

<!-- Include Enhanced CSS -->
<link rel="stylesheet" href="{{ asset('css/intelligent-chatbot.css') }}?v=2.0">
<!-- Include Enhanced JavaScript (Real-time 3-Mode Support - 2 sec polling) -->
<script src="{{ asset('js/intelligent-chatbot-enhanced.js') }}?v=2.0"></script>

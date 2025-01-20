@extends('layouts.app')

@section('title', 'Chat')

@section('content')
	
	<!-- **************** MAIN CONTENT START **************** -->
	<main>
		<!-- Container START -->
		<div class="container" style="min-height: calc(88vh);">
			<div class="row mt-3">
				<!-- Main content START -->
				<div class="col-12 col-xl-8 col-lg-8 mx-auto">
					
					<h5>{{__('default.Chat with AI')}}</h5>
					
					
					<div class="chat-window" id="chatWindow"
					     style="border: 1px solid #ccc; height: 400px; overflow-y: scroll; padding: 10px;">
						<!-- Chat messages will be appended here -->
					</div>
					<div class="mb-3">
						<textarea class="form-control" id="userPrompt" rows="3"></textarea>
					</div>
					<button type="button" class="btn btn-primary" id="sendPromptBtn">{{ __('default.Send Prompt') }}</button>
					
					
					<div class="mt-5 mb-2">
						
						<span for="llmSelect" class="form-label">{{__('default.AI Engines:')}}
							@if (Auth::user() && Auth::user()->isAdmin())
								<label class="badge bg-danger">Admin</label>
							@endif
						
						</span>
						<select id="llmSelect" class="form-select mx-auto">
							<option value="">{{__('default.Select an AI Engine')}}</option>
							@if (Auth::user() && Auth::user()->isAdmin())
								<option value="anthropic-sonet">anthropic :: claude-3.5-sonnet (direct)</option>
								<option value="anthropic-haiku">anthropic :: haiku (direct)</option>
								<option value="open-ai-gpt-4o">openai :: gpt-4o (direct)</option>
								<option value="open-ai-gpt-4o-mini">openai :: gpt-4o-mini (direct)</option>
							@endif
							@if (Auth::user() && !empty(Auth::user()->anthropic_key))
								<option value="anthropic-sonet">anthropic :: claude-3.5-sonnet (direct)</option>
								<option value="anthropic-haiku">anthropic :: haiku (direct)</option>
							@endif
							@if (Auth::user() && !empty(Auth::user()->openai_api_key))
								<option value="open-ai-gpt-4o">openai :: gpt-4o (direct)</option>
								<option value="open-ai-gpt-4o-mini">openai :: gpt-4o-mini (direct)</option>
							@endif
						</select>
					</div>
					
					<div class="mb-5" id="modelInfo">
						<div class="mt-1 small" style="border: 1px solid #ccc; border-radius: 5px; padding: 5px;">
							<div id="modelDescription"></div>
							<div id="modelPricing"></div>
						</div>
					</div>
				
				</div> <!-- Row END -->
				<div class="col-12 col-xl-4 col-lg-4 mx-auto">
					
					<h5>{{__('default.Chat History')}}</h5>
					
					<div id="chatSessions" class="list-group">
						<!-- Chat sessions will be loaded here -->
					</div>
				
				</div>
			</div>
			<!-- Container END -->
	</main>
	
	@include('layouts.footer')

@endsection

@push('scripts')
	<!-- Inline JavaScript code -->
	<script>
		let savedLlm = localStorage.getItem('chat-llm') || 'anthropic/claude-3-haiku:beta';
		let sessionId = null;
		
		function getLLMsData() {
			return new Promise((resolve, reject) => {
				$.ajax({
					url: '/check-llms-json',
					type: 'GET',
					success: function (data) {
						resolve(data);
					},
					error: function (xhr, status, error) {
						reject(error);
					}
				});
			});
		}
		
		function linkify(text) {
			const urlRegex = /(https?:\/\/[^\s]+)/g;
			return text.replace(urlRegex, function (url) {
				return '<a href="' + url + '" target="_blank" rel="noopener noreferrer">' + url + '</a>';
			});
		}
		
		function loadChatSessions() {
			$.ajax({
				url: '/chat/sessions',
				type: 'GET',
				success: function (response) {
					const sessionsDiv = $('#chatSessions');
					sessionsDiv.empty();
					response.forEach(session => {
						const firstMessage = session.messages[0]?.message || 'New conversation';
						const date = new Date(session.created_at).toLocaleDateString();
						sessionsDiv.append(`
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/chat/${session.session_id}" class="chat-session text-decoration-none flex-grow-1" data-session-id="${session.session_id}">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">${firstMessage.substring(0, 30)}...</h6>
                                    <small>${date}</small>
                                </div>
                                <small>Model: ${session.messages[0]?.llm || 'N/A'}</small>
                            </a>
                            <button class="btn btn-sm btn-danger ms-2 delete-session"
                                    data-session-id="${session.session_id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `);
					});
					
					// Add click handler for delete buttons
					$('.delete-session').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();
						const sessionId = $(this).data('session-id');
						if (confirm('Are you sure you want to delete this chat session?')) {
							deleteSession(sessionId);
						}
					});
				}
			});
		}
		
		function deleteSession(sessionId) {
			$.ajax({
				url: `/chat/${sessionId}`,
				type: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					if (response.success) {
						// Reload the chat sessions
						loadChatSessions();
						
						// If we're currently viewing the deleted session, redirect to /chat
						if (sessionId === '{{ $current_session_id }}') {
							window.location.href = '/chat';
						}
					} else {
						alert('Error deleting session: ' + response.message);
					}
				},
				error: function() {
					alert('Error deleting session');
				}
			});
		}
		
		function loadChatMessages(sessionId) {
			$('#chatWindow').empty();
			sessionId = sessionId;
			
			$.ajax({
				url: `/chat/messages/${sessionId}`,
				type: 'GET',
				success: function (response) {
					response.forEach(message => {
						const tokens = message.role === 'assistant' ?
							`(Tokens: ${message.prompt_tokens}/${message.completion_tokens})` : '';
						
						$('#chatWindow').append(
							`<div><strong>${message.role}:</strong> ${message.message}
                         <small class="text-muted">${tokens}</small></div>`
						);
					});
					
					$('#chatWindow').scrollTop($('#chatWindow')[0].scrollHeight);
				}
			});
		}
		
		$(document).ready(function () {
			const currentSessionId = '{{ $current_session_id }}';
			
			if (currentSessionId) {
				// Load the existing session
				sessionId = currentSessionId;
				loadChatMessages(sessionId);
			} else {
				// Create new session
				$.ajax({
					url: '{{ route('chat.create-session') }}',
					type: 'POST',
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (data) {
						sessionId = data.session_id;
					}
				});
			}
			
			loadChatSessions();
			
			
			getLLMsData().then(function (llmsData) {
				const llmSelect = $('#llmSelect');
				
				llmsData.forEach(function (model) {
					
					// Calculate and display pricing per million tokens
					let promptPricePerMillion = ((model.pricing.prompt || 0) * 1000000).toFixed(2);
					let completionPricePerMillion = ((model.pricing.completion || 0) * 1000000).toFixed(2);
					
					llmSelect.append($('<option>', {
						value: model.id,
						text: model.name + ' - $' + promptPricePerMillion + ' / $' + completionPricePerMillion,
						'data-description': model.description,
						'data-prompt-price': model.pricing.prompt || 0,
						'data-completion-price': model.pricing.completion || 0,
					}));
				});
				
				// Set the saved LLM if it exists
				if (savedLlm) {
					llmSelect.val(savedLlm);
				}
				
				llmSelect.on('click', function () {
					$('#modelInfo').removeClass('d-none');
				});
				
				// Show description on change
				llmSelect.change(function () {
					const selectedOption = $(this).find('option:selected');
					const description = selectedOption.data('description');
					const promptPrice = selectedOption.data('prompt-price');
					const completionPrice = selectedOption.data('completion-price');
					$('#modelDescription').html(linkify(description || ''));
					
					// Calculate and display pricing per million tokens
					const promptPricePerMillion = (promptPrice * 1000000).toFixed(2);
					const completionPricePerMillion = (completionPrice * 1000000).toFixed(2);
					
					$('#modelPricing').html(`
                <strong>Pricing (per million tokens):</strong> Prompt: $${promptPricePerMillion} - Completion: $${completionPricePerMillion}
            `);
				});
				
				// Trigger change to show initial description
				llmSelect.trigger('change');
			}).catch(function (error) {
				console.error('Error loading LLMs data:', error);
			});
			
			$("#llmSelect").on('change', function () {
				localStorage.setItem('chat-llm', $(this).val());
				savedLlm = $(this).val();
			});
			
			// change $llmSelect to savedLlm
			console.log('set llmSelect to ' + savedLlm);
			var dropdown = document.getElementById('llmSelect');
			var options = dropdown.getElementsByTagName('option');
			
			for (var i = 0; i < options.length; i++) {
				if (options[i].value === savedLlm) {
					dropdown.selectedIndex = i;
				}
			}
			
			
			$('#sendPromptBtn').on('click', function () {
				const userPrompt = $('#userPrompt').val();
				const llm = $('#llmSelect').val();
				
				$('#userPrompt').val('');
				$('#chatWindow').append('<div><strong>User:</strong> ' + userPrompt + '</div>');
				$('#chatWindow').scrollTop($('#chatWindow')[0].scrollHeight);
				
				$('#sendPromptBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
				
				$.ajax({
					url: '{{ route('send-llm-prompt') }}',
					method: 'POST',
					data: {user_prompt: userPrompt, session_id: sessionId, llm: llm},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					dataType: 'json',
					success: function (response) {
						if (response.success) {
							$('#chatWindow').append(`<div><strong>Assistant:</strong>${response.result.content}(Tokens: ${response.result.prompt_tokens}/${response.result.completion_tokens})</div>`);
						} else {
							$('#chatWindow').append('<div><strong>Error:</strong> ' + JSON.stringify(response) + '</div>');
						}
						$('#chatWindow').scrollTop($('#chatWindow')[0].scrollHeight);
						$('#sendPromptBtn').prop('disabled', false).text('Send Prompt');
					}
				});
			});
			
		});
	</script>

@endpush


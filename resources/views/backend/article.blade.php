@extends('layouts.app')
@section('title', isset($article) ? __('default.Edit Article') : __('default.Create Article'))
@section('content')
	<!-- Main content START -->
	<main>
		<div class="container mb-5" style="min-height: calc(88vh);">
			<div class="row mt-3">
				<!-- Main content START -->
				<div class="col-12 col-xl-8 col-lg-8 mx-auto">
					
					@if ($errors->any())
						<div class="alert alert-danger">
							<ul class="mb-0">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					
					<!-- Display success message -->
					@if(session('success'))
						<div class="alert alert-success">
							{{ session('success') }}
						</div>
					@endif
					
					<!-- Display error message -->
					@if(session('error'))
						<div class="alert alert-danger">
							{{ session('error') }}
						</div>
					@endif
					
					<h5>{{ isset($article) ? __('default.Edit Article') : __('default.Create Article') }}</h5>
					
					<form id="articleForm" method="POST"
					      action="{{ isset($article) ? route('articles.update', \App\Helpers\IdHasher::encode($article->id)) : route('articles.store') }}">
						@csrf
						@if(isset($article))
							@method('PUT')
						@endif
						<input type="file" id="image-upload-input" style="display: none" accept="image/*">
						<input type="hidden" id="featured_image" name="featured_image"
						       value="{{ isset($article) ? $article->featured_image : '' }}">
						
						
						<!-- Title -->
						<div class="mb-3">
							<label for="title" class="form-label">{{ __('default.Title') }}</label>
							<input type="text" class="form-control" id="title" name="title"
							       value="{!! isset($article) ? $article->title : old('title')  !!}" required>
						</div>
						
						<!-- subtitle -->
						<div class="mb-3">
							<label for="subtitle" class="form-label">{{ __('default.Subtitle') }}</label>
							<input type="text" class="form-control" id="subtitle" name="subtitle"
							       value="{!! isset($article) ? $article->subtitle : old('subtitle') !!}">
						</div>
						
						<!-- article Content -->
						<div class="mb-3">
							<label for="main_text" class="form-label">{{ __('default.Content') }}</label>
							<div id="main_text"></div>
							<textarea class="form-control" id="article_alan" name="main_text"
							          rows="3">{!! isset($article) ? $article->main_text : old('main_text')  !!} </textarea>
						</div>
						
						<!-- Featured Image -->
						<div class="mb-3">
							<div id="selectedImagePreview" class="mt-2">
								@if(isset($article) && $article->featured_image)
									<img src="{{ $article->featured_image }}" alt="Featured Image" class="img-fluid"
									     style="max-width: 600px;">
								@endif
							</div>
						</div>
						
						<!-- Images tab START -->
						<div class="tab-pane" id="nav-setting-tab-4">
							<div class="card">
								<div class="card-header border-0 pb-3">
									<h5 class="card-title">{{ __('default.Featured Image') }}</h5>
									<div class="">
										<p class="mb-0">{{__('default.Upload and manage your images')}}</p>
										<div class=""> <!-- Added container for buttons -->
											<div type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
											     data-bs-target="#imageModal">
												{{ __('default.Select Featured Image') }}
											</div>
											<div class="btn btn-sm btn-primary" id="uploadImageBtn">
												{{__('default.Upload Image')}}
											</div>
											<div class="btn btn-sm btn-success" data-bs-toggle="collapse"
											     data-bs-target="#imageGenSection">
												{{__('default.Generate with AI')}}
											</div>
										</div>
									</div>
								</div>
								
								<!-- Image Generation Section -->
								<div class="collapse" id="imageGenSection">
									<div class="card-body border-bottom">
										<div class="mb-3">
											{{__('default.User Prompt')}}:
											<textarea class="form-control" id="userPrompt" rows="2"></textarea>
										</div>
										
										<button type="button" class="btn btn-primary" id="generateImageBtn">
											{{__('default.Generate Image')}}
										</button>
									</div>
								</div>
								
								<!-- Generated Image Preview -->
								<div id="generatedImageArea" class="card-body border-bottom d-none">
									<h6>{{__('default.Generated Image Preview')}}</h6>
									<div class="card">
										<img id="generatedImage" src="" class="card-img-top" alt="Generated Image">
										<div class="card-body">
											<p class="card-text" id="image_prompt"></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Images tab END -->
						
						<!-- Category -->
						<div class="mb-3 mt-3">
							<label for="category_id" class="form-label">{{ __('default.Categories') }}</label>
							<div id="generateCategory" class="ai-generate-button">Yapay Zeka ile Oluştur</div>
							<select class="form-select" id="category_id" name="category_id">
								<option value="">{{ __('default.Select Category') }}</option>
								@foreach($categories as $mainCategory)
									<option disabled class="fw-bold bg-light">{{ $mainCategory->category_name }}</option>
									@foreach($mainCategory->subCategories as $subCategory)
										<option value="{{ $subCategory->id }}"
										        {{ isset($article) && $article->category_id == $subCategory->id ? 'selected' : '' }}
										        data-main-category="{{ $mainCategory->category_name }}"
										        style="padding-left: 20px;">
											{{ $subCategory->category_name }}
										</option>
									@endforeach
								@endforeach
							</select>
						</div>
						
						<!-- Short Description -->
						<div class="mb-3">
							<label for="subheading" class="form-label">{{ __('default.Short Description') }}</label>
							<div id="generateDescription" class="ai-generate-button">Yapay Zeka ile Oluştur</div>
							<textarea class="form-control" id="subheading" name="subheading"
							          rows="3">{!! isset($article) ? $article->subheading : old('subheading')  !!}</textarea>
						</div>
						
						<!-- Keywords -->
						<div class="mb-3">
							<label for="keywords" class="form-label">{{ __('default.Keywords') }}</label>
							<div id="generateKeywords" class="ai-generate-button">Yapay Zeka ile Oluştur</div>
							<input type="text" class="form-control" id="keywords" name="keywords"
							       value="{{ isset($article) ? $article->keywords->pluck('keyword')->implode(', ') : old('keywords') }}"
							       placeholder="{{ __('default.Enter keywords separated by commas or spaces') }}">
							<small class="text-muted">{{ __('default.Maximum 16 characters per keyword') }}</small>
						</div>
						
						<!-- Publication Status -->
						<div class="mb-3">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1"
									{{ isset($article) && $article->is_published ? 'checked' : '' }}>
								<label class="form-check-label" for="is_published">
									{{ __('default.Publish Article') }}
								</label>
							</div>
						</div>
						
						<!-- Posted At -->
						<div class="mb-3">
							<label for="created_at" class="form-label">{{ __('default.Publication Date') }}:</label>
							{{ isset($article) ? $article->created_at->format('Y-m-d\TH:i') : '' }}
						</div>
						
						<button type="submit" class="btn btn-primary">
							{{ isset($article) ? __('default.Update Article') : __('default.Create Article') }}
						</button>
					</form>
				</div>
			</div>
		</div>
	</main>
	
	<!-- Image Selection Modal -->
	<div class="modal fade" id="imageModal" tabindex="-1">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ __('default.Select Featured Image') }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="row g-3" id="modalImageGrid">
						<!-- Images will be loaded here -->
					</div>
					<!-- Pagination container -->
					<div id="modalPaginationContainer" class="mt-4">
						<!-- Pagination will be added here -->
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Image Upload Modal -->
	<div class="modal fade" id="uploadImageModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{__('default.Upload Image')}}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<form id="uploadImageForm">
					@csrf
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">{{__('default.Image')}}</label>
							<input type="file" class="form-control" name="image" accept="image/*" required>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('default.Close')}}</button>
						<button type="submit" class="btn btn-primary">{{__('default.Upload')}}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	@include('layouts.footer')
@endsection

@push('styles')
	<link rel="stylesheet" type="text/css" href="/css/tagify.css">
@endpush

@push('scripts')
	<script src="/js/easymde.min.js"></script>
	<script src="/js/tagify.js"></script>
@endpush

@push('styles')
	<style>
      .ai-generate-button {
          border: 1px solid #ccc;
          font-size: 14px;
          border-radius: 3px;
          padding-left: 5px;
          padding-right: 5px;
          margin-left: 5px;
          display: inline-block;
		      cursor: pointer;
      }
      
      .ai-generate-button:hover {
		      text-decoration: underline;
			}

      #notification-container {
          position: fixed;
          top: 20px;
          right: 20px;
          z-index: 9999;
      }

      .notification-toast {
          min-width: 200px;
          max-width: 500px;
          margin-bottom: 10px;
      }

      #modalImageGrid .card {
          height: 100%;
      }

      #modalImageGrid .card-img-top {
          object-fit: cover;
          height: 200px;
      }


      #category_id option[disabled] {
          font-weight: bold;
          background-color: #f8f9fa;
          color: #212529;
      }

      #category_id option:not([disabled]) {
          padding-left: 20px;
          color: #495057;
      }

      /* Optional: Style for the selected option */
      #category_id option:checked {
          background-color: #007bff;
          color: white;
      }
	
	</style>
@endpush

@push('scripts')
	<script>
		function showNotification(message, type = 'error') {
			// Create the notification element
			const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show notification-toast" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
			
			// Add the notification to the page
			if (!$('#notification-container').length) {
				$('body').append('<div id="notification-container"></div>');
			}
			$('#notification-container').append(notification);
			
			// Remove the notification after 5 seconds
			setTimeout(() => {
				notification.alert('close');
			}, 5000);
		}
		
		
		// Add this to your existing $(document).ready() function
		function loadModalImages(page = 1) {
			$.get('/upload-images', {page: page}, function (response) {
				const grid = $('#modalImageGrid');
				grid.empty();
				
				response.images.data.forEach(image => {
					grid.append(createModalImageCard(image));
				});
				
				// Delete Image
				$('.delete-upload-image').on('click', function () {
					if (confirm('{{__('default.Are you sure you want to delete this image?')}}')) {
						const id = $(this).data('id');
						
						$.ajax({
							url: `/upload-images/${id}`,
							type: 'DELETE',
							data: {"_token": "{{ csrf_token() }}"},
							success: function () {
								showNotification('{{__('default.Image deleted successfully')}}', 'success');
							},
							error: function () {
								showNotification('{{__('default.Error deleting image')}}');
							}
						});
					}
				});
				
				updateModalPagination(response.pagination);
			});
		}
		
		function createModalImageCard(image) {
			let image_url = '';
			let image_original_url = '';
			let image_alt = '';
			
			if (image.image_type === 'upload') {
				image_url = '/storage/upload-images/small/' + image.image_small_filename;
				image_original_url = '/storage/upload-images/original/' + image.image_original_filename;
				image_alt = image.image_alt;
			} else {
				image_url = '/storage/ai-images/small/' + image.image_small_filename;
				image_original_url = '/storage/ai-images/original/' + image.image_original_filename;
				image_alt = image.user_prompt;
			}
			
			return `
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card h-100">
                <img src="${image_url}"
                     class="card-img-top cursor-pointer"
                     alt="${image_alt}"
                     data-original-url="${image_original_url}"
                     data-alt="${image_alt}"
                     style="cursor: pointer;">
                <div class="card-body">
                    <h6 class="card-title">${image_alt}</h6>
                    <p class="card-text small text-muted">
                        ${new Date(image.created_at).toLocaleDateString()}
                        <span class="badge bg-${image.image_type === '{{__('default.generated')}}' ? 'success' : 'primary'}">${image.image_type}</span>
                    </p>
                    <div>
											  <button class="btn btn-sm btn-primary select-modal-image"
                            data-image-id="${image.id}"
                            data-image-url="${image_url}">
                        {{__('default.Select')}}
                        </button>
												${image.image_type === 'upload' ? `
														<button class="btn btn-sm btn-danger delete-upload-image"
																		data-id="${image.id}">
																{{__('default.Delete')}}
                        ` : `
														<button class="btn btn-sm btn-danger delete-generated-image"
                                data-id="${image.id}">
                            {{__('default.Delete')}}
                        </button>`}
                    </div>
                </div>
            </div>
        </div>
    `;
		}
		
		function updateModalPagination(pagination) {
			const container = $('#modalPaginationContainer');
			container.empty();
			
			const paginationHtml = `
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link modal-page-link" href="#" data-page="${pagination.current_page - 1}">Geri</a>
                </li>
                ${generateModalPaginationItems(pagination)}
                <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                    <a class="page-link modal-page-link" href="#" data-page="${pagination.current_page + 1}">İleri</a>
                </li>
            </ul>
        </nav>
    `;
			
			container.html(paginationHtml);
		}
		
		function generateModalPaginationItems(pagination) {
			let items = '';
			for (let i = 1; i <= pagination.last_page; i++) {
				items += `
            <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                <a class="page-link modal-page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `;
			}
			return items;
		}
		
		//-------------------------------------------------------------------------
		
		
		function linkify(text) {
			const urlRegex = /(https?:\/\/[^\s]+)/g;
			return text.replace(urlRegex, function (url) {
				return '<a href="' + url + '" target="_blank" rel="noopener noreferrer">' + url + '</a>';
			});
		}
		
		//-------------------------------------------------------------------------
		
		$(document).ready(function () {
			// Initialize EasyMDE
			const easyMDE = new EasyMDE({
				element: document.getElementById('article_alan'),
				spellChecker: false,
				forceSync: true,
				lineWrapping: true,
				status: ["lines", "words"],
				toolbar: ["bold", "italic", "heading", "quote", "horizontal-rule", "|", "unordered-list", "ordered-list", "|", "preview", {
					name: "upload-image",
					action: function customFunction(editor) {
						// Trigger file input click
						$('#image-upload-input').click();
					},
					className: "fa fa-upload",
					title: "Upload Image",
				}, "guide"],
				toolbarButtonClassPrefix: "mde",
				
			});
			
			easyMDE.codemirror.setSize(null, '40vh');
			
			$('#image-upload-input').on('change', function () {
				const file = this.files[0];
				if (file) {
					const formData = new FormData();
					formData.append('image', file);
					formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
					
					$.ajax({
						url: '{{route('upload-article-images.store')}}',
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						success: function (response) {
							// Get the image URL from the response
							const imageUrl = response.url;
							
							// Insert the image markdown at cursor position
							const imageMarkdown = `![${file.name}](${imageUrl})`;
							const cm = easyMDE.codemirror;
							const pos = cm.getCursor();
							cm.replaceRange(imageMarkdown, pos);
						},
						error: function (xhr, status, error) {
							console.error('Upload failed:', error);
							alert('Image upload failed. Please try again.');
						}
					});
				}
			});
			
			$('#articleForm').on('submit', function (e) {
				if (easyMDE) {
					// Update the textarea with the current editor content
					$('#article_alan').val(easyMDE.value());
				}
			});
			
			//Images
			
			// Load images when modal is shown
			$('#imageModal').on('show.bs.modal', function () {
				loadModalImages();
			});
			
			// Handle pagination clicks
			$(document).on('click', '.modal-page-link', function (e) {
				e.preventDefault();
				const page = $(this).data('page');
				loadModalImages(page);
			});
			
			// Handle image selection
			$(document).on('click', '.select-modal-image', function () {
				const imageId = $(this).data('image-id');
				const imageUrl = $(this).data('image-url');
				
				// Update the hidden input and previewed
				$('#featured_image').val(imageUrl);
				$('#selectedImagePreview').html(`
            <img src="${imageUrl}" alt="Selected Image" class="img-fluid" style="max-width: 600px;">
        `);
				
				// Close the modal
				$('#imageModal').modal('hide');
			});
			
			// Handle image generation
			$('#generateImageBtn').on('click', function () {
				const userPrompt = $('#userPrompt').val();
				
				$(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> {{__('default.Generating...')}}');
				
				$.ajax({
					url: '{{ route('send-image-gen-prompt') }}',
					method: 'POST',
					data: {
						user_prompt: userPrompt,
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					dataType: 'json',
					success: function (result) {
						if (result.success) {
							$('#generatedImageArea').removeClass('d-none');
							$('#generatedImage').attr('src', '/storage/ai-images/large/' + result.image_large_filename);
							$('#image_prompt').text(result.image_prompt);
							$('#tokensDisplay').text(`Tokens Used: ${result.prompt_tokens}/${result.completion_tokens}`);
						}
						$('#generateImageBtn').prop('disabled', false).text('{{__('default.Generate Image')}}');
						$('#imageModal').modal('show');
						
					},
					error: function () {
						showNotification('Error generating image');
						$('#generateImageBtn').prop('disabled', false).text('{{__('default.Generate Image')}}');
					}
				});
			});
			
			// Delete generated image
			$(document).on('click', '.delete-generated-image', function () {
				if (confirm('{{__('default.Are you sure you want to delete this generated image?')}}')) {
					const imageId = $(this).data('id');
					$.ajax({
						url: `/image-gen/${imageId}`,
						type: 'DELETE',
						data: {"_token": "{{ csrf_token() }}"},
						success: function () {
							showNotification('{{__('default.Generated image deleted successfully')}}', 'success');
						},
						error: function () {
							showNotification('{{__('default.Error deleting generated image')}}');
						}
					});
				}
			});
			
			
			// Upload Image
			$('#uploadImageBtn').on('click', function (e) {
				e.preventDefault();
				$('#uploadImageModal').modal('show');
			});
			
			$('#uploadImageForm').submit(function (e) {
				e.preventDefault();
				const formData = new FormData(this);
				
				$.ajax({
					url: '{{ route('upload-images.index') }}',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function () {
						$('#uploadImageModal').modal('hide');
						showNotification('{{__('default.Image uploaded successfully')}}', 'success');
						$('#imageModal').modal('show');
					},
					error: function () {
						showNotification('{{__('default.Error uploading image')}}');
					}
				});
			});
			
			
			//Category
			const kategoriSelect = $('#category_id');
			
			// Store the original text for each option
			kategoriSelect.find('option').each(function () {
				if (!$(this).prop('disabled')) {
					$(this).data('original-text', $(this).text());
					if (typeof $(this).data('main-category') !== 'undefined') {
						$(this).data('short-text', $(this).data('main-category') + ' - ' + $(this).text().trim());
					} else {
						$(this).data('short-text', $(this).text().trim());
					}
				}
			});
			
			// Handle dropdown open/close
			kategoriSelect.on('mousedown', function () {
				const isOpen = $(this).hasClass('show');
				if (!isOpen) {
					// When opening dropdown, show indented format
					$(this).find('option').each(function () {
						if (!$(this).prop('disabled') && $(this).data('original-text')) {
							$(this).text($(this).data('original-text'));
						}
					});
				}
			});
			
			// When dropdown closes, show combined format
			kategoriSelect.on('change blur', function () {
				$(this).find('option').each(function () {
					if (!$(this).prop('disabled') && $(this).data('short-text')) {
						$(this).text($(this).data('short-text'));
					}
				});
			});
			
			// Initialize with combined format
			kategoriSelect.trigger('blur');
			
			
			//Keywords
			
			var keywordsInput = document.querySelector('input[name=keywords]');
			var tagify = new Tagify(keywordsInput, {
				maxTags: 10,
				maxLength: 16,
				delimiters: ", ",
				trim: true,
				dropdown: {
					enabled: 1,
					maxItems: 10,
					classname: "tags-look",
					closeOnSelect: false,
					position: "text"
				},
				callbacks: {
					add: validateTag,
					input: onInput
				}
			});
			
			function validateTag(e) {
				var tag = e.detail.data;
				if (tag.value.length > 16) {
					tag.value = tag.value.substr(0, 16);
				}
			}
			
			function onInput(e) {
				var value = e.detail.value;
				if (value.length >= 2) {
					fetch(`/eserlerim/keywords/search?q=${value}`, {
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
							'Accept': 'application/json'
						}
					})
						.then(response => response.json())
						.then(data => {
							tagify.whitelist = data;
							tagify.dropdown.show();
						})
						.catch(error => console.error('Error:', error));
				}
			}
			
			//AI Auto Generation
			// AI Generation handlers
			$('#generateCategory').on('click', function () {
				const mainText = easyMDE.value();
				if (!mainText) {
					showNotification('{{__('default.Please enter some content first.')}}', 'warning');
					return;
				}
				
				$(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
				
				$.ajax({
					url: '{{ route("articles.generate-category") }}',
					method: 'POST',
					data: {
						main_text: mainText,
						_token: '{{ csrf_token() }}'
					},
					success: function (response) {
						if (response.category_id) {
							$('#category_id').val(response.category_id);
						}
						showNotification('{{__('default.Category generated successfully.') }}', 'success');
					},
					error: function () {
						showNotification('{{__('default.Error generating category:') }}', 'error');
					},
					complete: function () {
						$('#generateCategory').prop('disabled', false).html('Yapay Zeka ile Oluştur');
					}
				});
			});
			
			$('#generateDescription').on('click', function () {
				const mainText = easyMDE.value();
				if (!mainText) {
					showNotification('{{__('default.Please enter some content first.')}}', 'warning');
					return;
				}
				
				$(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
				
				$.ajax({
					url: '{{ route("articles.generate-description") }}',
					method: 'POST',
					data: {
						main_text: mainText,
						_token: '{{ csrf_token() }}'
					},
					success: function (response) {
						$('#subheading').val(response.description);
						showNotification('{{__('default.Description generated successfully.') }}', 'success');
					},
					error: function () {
						showNotification('{{__('default.Error generating description:') }}', 'error');
					},
					complete: function () {
						$('#generateDescription').prop('disabled', false).html('Yapay Zeka ile Oluştur');
					}
				});
			});
			
			$('#generateKeywords').on('click', function () {
				const mainText = easyMDE.value();
				if (!mainText) {
					showNotification('{{__('default.Please enter some content first.')}}', 'warning');
					return;
				}
				
				$(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
				
				$.ajax({
					url: '{{ route("articles.generate-keywords") }}',
					method: 'POST',
					data: {
						main_text: mainText,
						_token: '{{ csrf_token() }}'
					},
					success: function (response) {
						tagify.removeAllTags();
						tagify.addTags(response.keywords);
						showNotification('{{__('default.Keywords generated successfully.') }}', 'success');
					},
					error: function () {
						showNotification('{{__('default.Error generating keywords:') }}', 'error');
					},
					complete: function () {
						$('#generateKeywords').prop('disabled', false).html('Yapay Zeka ile Oluştur');
					}
				});
			});
		});
	</script>
@endpush

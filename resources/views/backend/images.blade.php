@extends('layouts.settings')

@section('settings-content')
	
	<!-- Images tab START -->
	<div class="tab-pane" id="nav-setting-tab-4">
		<div class="card">
			<div class="card-header border-0 pb-0">
				<h5 class="card-title">{{__('default.Manage Images')}}</h5>
				<div class="d-flex justify-content-between align-items-center">
					<p class="mb-0">{{__('default.Upload and manage your images')}}</p>
					<div class="d-flex gap-2"> <!-- Added container for buttons -->
						<button class="btn btn-sm btn-primary" id="uploadImageBtn">
							{{__('default.Upload Image')}}
						</button>
						<button class="btn btn-sm btn-success" data-bs-toggle="collapse"
						        data-bs-target="#imageGenSection">
							{{__('default.Generate with AI')}}
						</button>
					</div>
				</div>
			</div>
			
			<!-- Image Generation Section -->
			<div class="collapse" id="imageGenSection">
				<div class="card-body border-bottom">
					<div class="mb-3">
						{{__('default.Prompt Enhancer')}}:
						<textarea class="form-control" id="promptEnhancer" rows="4">##UserPrompt##
Write a prompt to create an image using the above text.: Write in English even if the above text is written in another language. With the above information, compose a image. Write it as a single paragraph. The instructions should focus on the text elements of the image.</textarea>
					</div>
					
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
			
			<div class="card-body">
				<div class="row g-3" id="imageGrid">
					<!-- Images will be loaded here -->
				</div>
			</div>
		</div>
	</div>
	<!-- Images tab END -->
	
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
						<div class="mb-3">
							<label class="form-label">{{__('default.Alt Text')}}</label>
							<input type="text" class="form-control" name="alt">
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
	
	<!-- Edit Image Modal -->
	<div class="modal fade" id="editImageModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{__('default.Edit Image')}}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<form id="editImageForm">
					@csrf
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">{{__('default.Alt Text')}}</label>
							<input type="text" class="form-control" name="alt" required>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('default.Close')}}</button>
						<button type="submit" class="btn btn-primary">{{__('default.Save')}}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- Full Size Image Modal -->
	<div class="modal fade" id="imagePreviewModal" tabindex="-1">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Image Preview</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body text-center">
					<img src="" id="previewImage" class="img-fluid" alt="">
					<p class="mt-2" id="previewImageDescription"></p>
				</div>
			</div>
		</div>
	</div>
	
	<style>
      .preview-image {
          cursor: pointer;
          transition: opacity 0.3s;
      }

      .preview-image:hover {
          opacity: 0.8;
      }
	</style>

@endsection

@push('scripts')
	<script>
		function loadImages(page = 1) {
			$.get('/upload-images', {page: page}, function (response) {
				const grid = $('#imageGrid');
				grid.empty();
				response.images.data.forEach(image => {
					grid.append(createImageCard(image));
				});
				
				// Add pagination
				updatePagination(response.pagination);
				
				// Add click handlers for image preview
				$('.preview-image').on('click', function () {
					const imageUrl = $(this).data('original-url');
					const imageAlt = $(this).data('alt');
					$('#previewImage').attr('src', imageUrl);
					$('#previewImage').attr('alt', imageAlt);
					$('#previewImageDescription').text(imageAlt);
					$('#imagePreviewModal').modal('show');
				});
				
				$('.edit-image').on('click', function () {
					const id = $(this).data('id');
					const alt = $(this).data('alt');
					
					$('#editImageForm').data('id', id);
					$('#editImageForm').find('[name="alt"]').val(alt);
					$('#editImageModal').modal('show');
					response.images.forEach(image => {
						grid.append(createImageCard(image));
					});
				});
				
				// Delete Image
				$('.delete-upload-image').on('click', function () {
					if (confirm('Are you sure you want to delete this image?')) {
						const id = $(this).data('id');
						
						$.ajax({
							url: `/upload-images/${id}`,
							type: 'DELETE',
							data: {"_token": "{{ csrf_token() }}"},
							success: function () {
								loadImages();
								showNotification('Image deleted successfully', 'success');
							},
							error: function () {
								showNotification('Error deleting image');
							}
						});
					}
				});
			});
		}
		
		function createImageCard(image) {
			let image_url = '';
			let image_original_url = '';
			let image_alt = '';
			
			if (image.image_type==='upload') {
				image_url = '/storage/upload-images/small/' + image.image_small_filename;
				image_original_url = '/storage/upload-images/original/' + image.image_filename;
				image_alt = image.image_alt;
			} else
			{
				image_url = '/storage/ai-images/small/' + image.image_small_filename;
				image_original_url = '/storage/ai-images/original/' + image.image_filename;
				image_alt = image.user_prompt;
			}
			
			return `
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card h-100">
                <img src="${image_url}"
                     class="card-img-top preview-image cursor-pointer"
                     alt="${image_alt}"
                     data-original-url="${image_original_url}"
                     data-alt="${image_alt}"
                     style="cursor: pointer;">
                <div class="card-body">
                    <h6 class="card-title">${image_alt}</h6>
                    <p class="card-text small text-muted">
                        ${new Date(image.created_at).toLocaleDateString()}
                        <span class="badge bg-${image.image_type === 'generated' ? 'success' : 'primary'}">${image.image_type}</span>
                    </p>
                    <div>
												${image.image_type === 'upload' ? `
                            <button class="btn btn-sm btn-primary edit-image mr-2"
                                    data-id="${image.id}"
                                    data-alt="${image_alt}">
                                Edit
                            </button>
														<button class="btn btn-sm btn-danger delete-upload-image"
																		data-id="${image.id}">
																Delete
                        ` : `
                            <button class="btn btn-sm btn-primary edit-generated-image mr-2"
                                    data-user-prompt="${image.user_prompt}"
                                    data-llm-prompt="${image.llm_prompt}">
                                Edit
                            </button>
														<button class="btn btn-sm btn-danger delete-generated-image"
                                data-id="${image.id}">
                            Delete
                        </button>`}
                    </div>
                </div>
            </div>
        </div>
    `;
		}
		
		function updatePagination(pagination) {
			// Remove existing pagination
			$('.pagination-container').remove();
			
			const paginationHtml = `
        <nav aria-label="Page navigation" class="mt-4 pagination-container">
            <ul class="pagination justify-content-center">
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                </li>
                ${generatePaginationItems(pagination)}
                <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                </li>
            </ul>
        </nav>
    `;
			
			$('#imageGrid').after(paginationHtml);
			
			// Add click handlers for pagination
			$('.pagination .page-link').on('click', function (e) {
				e.preventDefault();
				const page = $(this).data('page');
				if (page > 0 && page <= pagination.last_page) {
					loadImages(page);
				}
			});
		}
		
		function generatePaginationItems(pagination) {
			let items = '';
			for (let i = 1; i <= pagination.last_page; i++) {
				items += `
            <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `;
			}
			return items;
		}
		
		function linkify(text) {
			const urlRegex = /(https?:\/\/[^\s]+)/g;
			return text.replace(urlRegex, function (url) {
				return '<a href="' + url + '" target="_blank" rel="noopener noreferrer">' + url + '</a>';
			});
		}

		$(document).ready(function () {
			loadImages();
			
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
							loadImages();
						}
						$('#generateImageBtn').prop('disabled', false).text('{{__('default.Generate Image')}}');
					},
					error: function () {
						showNotification('Error generating image');
						$('#generateImageBtn').prop('disabled', false).text('{{__('default.Generate Image')}}');
					}
				});
			});
			
			// Delete generated image
			$(document).on('click', '.delete-generated-image', function () {
				if (confirm('Are you sure you want to delete this generated image?')) {
					const sessionId = $(this).data('id');
					$.ajax({
						url: `/image-gen/${sessionId}`,
						type: 'DELETE',
						data: {"_token": "{{ csrf_token() }}"},
						success: function () {
							loadImages();
							showNotification('Generated image deleted successfully', 'success');
						},
						error: function () {
							showNotification('Error deleting generated image');
						}
					});
				}
			});
			
			$(document).on('click', '.edit-generated-image', function () {
				const userPrompt = $(this).data('user-prompt');
				
				// Show the image generation section
				$('#imageGenSection').collapse('show');
				
				// Scroll to the form
				$('html, body').animate({
					scrollTop: $('#imageGenSection').offset().top - 100
				}, 500);
				
				// Set the values in the form
				// Decode HTML entities before setting
				const decodedUserPrompt = $('<div/>').html(userPrompt).text();
				
				$('#userPrompt').val(decodedUserPrompt);
			});
			
			
			// Upload Image
			$('#uploadImageBtn').on('click', function () {
				$('#uploadImageModal').modal('show');
			});
			
			$('#uploadImageForm').submit(function (e) {
				e.preventDefault();
				const formData = new FormData(this);
				
				$.ajax({
					url: '/upload-images',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function () {
						$('#uploadImageModal').modal('hide');
						loadImages();
						showNotification('Image uploaded successfully', 'success');
					},
					error: function () {
						showNotification('Error uploading image');
					}
				});
			});
			
			
			$('#editImageForm').submit(function (e) {
				e.preventDefault();
				const id = $(this).data('id');
				
				$.ajax({
					url: `/upload-images/${id}`,
					type: 'PUT',
					data: $(this).serialize(),
					success: function () {
						$('#editImageModal').modal('hide');
						loadImages();
						showNotification('Image updated successfully', 'success');
					},
					error: function () {
						showNotification('Error updating image');
					}
				});
			});
			
		});
	</script>
@endpush

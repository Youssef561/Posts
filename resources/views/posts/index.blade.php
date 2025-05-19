@extends('layout.app')

@section('title', 'Posts')

@section('content')

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold">📋 Posts Management</h2>
            <button class="btn btn-success shadow" data-bs-toggle="modal" data-bs-target="#createPostModal">➕ Create Post</button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 text-center">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Posted By</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="posts-table-body">
                        <tr>
                            <td colspan="6" class="text-muted py-4">Loading posts...</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- Create Modal --}}
    <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="create-post-form" class="modal-content shadow-sm border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-1"></i>Create Post</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Title</label>
                        <input type="text" class="form-control" name="title" id="title" >
                        <div id="title-error" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label fw-semibold">Content</label>
                        <textarea class="form-control" name="content" id="content" rows="4" ></textarea>
                        <div id="content-error" class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="edit-post-form" class="modal-content shadow-sm border-0">
                <input type="hidden" id="edit-post-id" name="post_id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i>Edit Post</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-title" class="form-label fw-semibold">Title</label>
                        <input type="text" class="form-control" name="title" id="edit-title">
                        <div id="edit-title-error" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-content" class="form-label fw-semibold">Content</label>
                        <textarea class="form-control" name="content" id="edit-content" rows="4" ></textarea>
                        <div id="edit-content-error" class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification Area -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div id="toast-container" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Success message here
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        let currentUser = null;

        document.addEventListener('DOMContentLoaded', function () {
            fetchPosts();

            // Create post form submission
            document.getElementById('create-post-form').addEventListener('submit', function (e) {
                e.preventDefault();
                clearCreateErrors();

                let formData = new FormData(this);
                let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                fetch("{{ route('posts.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.reset();
                            bootstrap.Modal.getInstance(document.getElementById('createPostModal')).hide();
                            fetchPosts();
                            showToast("Post created successfully!","success");
                        } else if (data.errors) {
                            showCreateErrors(data.errors);
                            showToast("⚠️ Please fix validation errors.", "warning");
                        }
                    })
                    .catch(error => {
                        console.error('Create Error:', error);
                    });
            });

            // Edit post form submission
            document.getElementById('edit-post-form').addEventListener('submit', function(e) {
                e.preventDefault();
                clearEditErrors();

                let id = document.getElementById('edit-post-id').value;
                let formData = new FormData(this);
                let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                fetch(`/posts/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: new URLSearchParams({
                        '_method': 'PUT',
                        'title': formData.get('title'),
                        'content': formData.get('content')
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            bootstrap.Modal.getInstance(document.getElementById('editPostModal')).hide();
                            fetchPosts();
                            showToast("Post updated successfully!");
                        } else if (data.errors) {
                            showEditErrors(data.errors);
                            showToast("⚠️ Please fix validation errors.", "warning");
                        }else {
                            showToast("❌ Failed to update the post.", "danger");
                        }
                    })
                    .catch(error => {
                        console.error('Edit Error:', error);
                    });
            });
        });

        function fetchPosts() {
            fetch("{{ route('posts.list') }}")
                .then(response => response.json())
                .then(data => {
                    currentUser = data.current_user;
                    let tbody = document.getElementById('posts-table-body');
                    tbody.innerHTML = '';

                    if (data.posts.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-muted">No posts available.</td></tr>`;
                        return;
                    }

                    data.posts.forEach((post, index) => {
                        let canEditDelete = currentUser.is_admin || currentUser.id === post.user_id;
                        tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${escapeHtml(post.title)}</td>
                            <td>${escapeHtml(post.content)}</td>
                            <td>${escapeHtml(post.user.name)}</td>
                            <td>${new Date(post.created_at).toLocaleDateString()}</td>
                            <td>
                                ${canEditDelete ? `
                                    <button class="btn btn-primary btn-sm" onclick="openEditModal(${post.id}, '${escapeJs(post.title)}', \`${escapeJs(post.content)}\`)">✏ Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deletePost(${post.id})">🗑 Delete</button>
                                ` : ''}
                            </td>
                        </tr>
                    `;
                    });
                })
                .catch(error => {
                    console.error("Error fetching posts:", error);
                    document.getElementById('posts-table-body').innerHTML = `<tr><td colspan="5" class="text-danger">Failed to load posts.</td></tr>`;
                });
        }

        function openEditModal(id, title, content) {
            document.getElementById('edit-post-id').value = id;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-content').value = content;
            clearEditErrors();
            new bootstrap.Modal(document.getElementById('editPostModal')).show();
        }

        function deletePost(id) {
            if (!confirm('Are you sure you want to delete this post?')) return;

            fetch(`/posts/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({ '_method': 'DELETE' })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        fetchPosts();
                        showToast("🗑️ Post deleted successfully!,danger");
                    } else {
                        showToast("Failed to delete post.", "danger");
                    }
                })
                .catch(error => {
                    console.error('Delete Error:', error);
                });
        }

        // Escape helpers to prevent XSS or JS errors
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function escapeJs(text) {
            return text
                .replace(/\\/g, "\\\\")
                .replace(/`/g, "\\`")
                .replace(/\$/g, "\\$")
                .replace(/'/g, "\\'")
                .replace(/"/g, '\\"')
                .replace(/\r?\n/g, "\\n");
        }

        // Show validation errors for create form
        function showCreateErrors(errors) {
            if(errors.title) {
                document.getElementById('title-error').textContent = errors.title[0];
                document.querySelector('#create-post-form [name="title"]').classList.add('is-invalid');
            }
            if(errors.content) {
                document.getElementById('content-error').textContent = errors.content[0];
                document.querySelector('#create-post-form [name="content"]').classList.add('is-invalid');
            }
        }

        function clearCreateErrors() {
            document.getElementById('title-error').textContent = '';
            document.getElementById('content-error').textContent = '';
            document.querySelector('#create-post-form [name="title"]').classList.remove('is-invalid');
            document.querySelector('#create-post-form [name="content"]').classList.remove('is-invalid');
        }

        // Show validation errors for edit form
        function showEditErrors(errors) {
            if(errors.title) {
                document.getElementById('edit-title-error').textContent = errors.title[0];
                document.querySelector('#edit-post-form [name="title"]').classList.add('is-invalid');
            }
            if(errors.content) {
                document.getElementById('edit-content-error').textContent = errors.content[0];
                document.querySelector('#edit-post-form [name="content"]').classList.add('is-invalid');
            }
        }

        function clearEditErrors() {
            document.getElementById('edit-title-error').textContent = '';
            document.getElementById('edit-content-error').textContent = '';
            document.querySelector('#edit-post-form [name="title"]').classList.remove('is-invalid');
            document.querySelector('#edit-post-form [name="content"]').classList.remove('is-invalid');
        }


        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            toastContainer.classList.remove('bg-success', 'bg-danger');
            toastContainer.classList.add(`bg-${type}`);
            toastContainer.querySelector('.toast-body').textContent = message;

            const toast = new bootstrap.Toast(toastContainer);
            toast.show();
        }


    </script>
@endpush

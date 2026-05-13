document.addEventListener('DOMContentLoaded', () => {
    /* ==========================================================================
       Dark Mode / Light Mode Toggle
       ========================================================================== */
    const themeToggleBtn = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;

    // Check for saved user preference in localStorage
    const savedTheme = localStorage.getItem('portfolio-theme');
    if (savedTheme) {
        htmlElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
    } else {
        // If no preference, check system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            htmlElement.setAttribute('data-theme', 'dark');
            updateThemeIcon('dark');
        }
    }

    // Toggle theme on button click
    themeToggleBtn.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        htmlElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('portfolio-theme', newTheme); // Save preference
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        themeToggleBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
    }

    /* ==========================================================================
       Client-Side Form Validation (Contact Form)
       ========================================================================== */
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // Prevent default submission to validate first
            e.preventDefault();
            
            let isValid = true;
            
            // Get form fields
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const messageInput = document.getElementById('message');
            
            // Validate Name
            if (nameInput.value.trim() === '') {
                showError('nameGroup');
                isValid = false;
            } else {
                removeError('nameGroup');
            }

            // Validate Email with simple Regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                showError('emailGroup');
                isValid = false;
            } else {
                removeError('emailGroup');
            }

            // Validate Message (min 10 characters)
            if (messageInput.value.trim().length < 10) {
                showError('messageGroup');
                isValid = false;
            } else {
                removeError('messageGroup');
            }

            // If everything is valid, submit via AJAX
            if (isValid) {
                const formData = new FormData(contactForm);
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                submitBtn.textContent = 'Gönderiliyor...';
                submitBtn.disabled = true;

                fetch('process_contact.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        contactForm.reset();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
            }
        });
    }

    function showError(groupId) {
        const group = document.getElementById(groupId);
        if (group) group.classList.add('error');
    }

    function removeError(groupId) {
        const group = document.getElementById(groupId);
        if (group) group.classList.remove('error');
    }

    /* ==========================================================================
       Fetch Projects via AJAX (Phase 4)
       ========================================================================== */
    const projectsContainer = document.getElementById('projectsContainer');
    
    if (projectsContainer) {
        fetch('fetch_projects.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    projectsContainer.innerHTML = ''; // Clear loading text
                    
                    data.data.forEach(project => {
                        // Generate Tags HTML
                        let tagsHtml = '';
                        if (project.tags) {
                            const tagsArray = project.tags.split(',');
                            tagsArray.forEach(tag => {
                                tagsHtml += `<span class="tag">${tag.trim()}</span>`;
                            });
                        }
                        
                        // Default image if empty
                        const imageUrl = project.image_url || 'https://via.placeholder.com/600x400?text=Project+Image';
                        
                        const projectHtml = `
                            <div class="project-card">
                                <div class="project-image" style="background-image: url('${imageUrl}')"></div>
                                <div class="project-info">
                                    <h3>${project.title}</h3>
                                    <p>${project.description}</p>
                                    <div class="project-tags">
                                        ${tagsHtml}
                                    </div>
                                </div>
                            </div>
                        `;
                        projectsContainer.insertAdjacentHTML('beforeend', projectHtml);
                    });
                } else {
                    projectsContainer.innerHTML = '<p style="text-align: center; width: 100%;">Henüz proje bulunamadı. Yönetici Panelinden giriş yapıp ekleyebilirsiniz!</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching projects:', error);
                projectsContainer.innerHTML = '<p style="color: #ef4444; text-align: center; width: 100%;">Projeler yüklenirken bir hata oluştu. Veritabanı bağlantınızı kontrol edin.</p>';
            });
    }
});

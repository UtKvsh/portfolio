// Tema Yönetimi
const themeToggleBtn = document.getElementById('themeToggle');
const htmlElement = document.documentElement;

const savedTheme = localStorage.getItem('theme');
if (savedTheme) {
    htmlElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
}

themeToggleBtn.addEventListener('click', () => {
    const currentTheme = htmlElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';

    htmlElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
});

function updateThemeIcon(theme) {
    themeToggleBtn.textContent = theme === 'light' ? '🌙' : '☀️';
}

// İletişim Formu Doğrulama ve AJAX Gönderimi
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Sayfa yenilenmesini engelle

        let isValid = true;

        // Form Alanları
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const messageInput = document.getElementById('message');

        // İsim Doğrulama
        if (nameInput.value.trim() === '') {
            showError('nameGroup');
            isValid = false;
        } else {
            hideError('nameGroup');
        }

        // E-posta Doğrulama
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
            showError('emailGroup');
            isValid = false;
        } else {
            hideError('emailGroup');
        }

        // Mesaj Doğrulama (Min 10 karakter)
        if (messageInput.value.trim().length < 10) {
            showError('messageGroup');
            isValid = false;
        } else {
            hideError('messageGroup');
        }

        // AJAX Gönderimi
        if (isValid) {
            const formData = new FormData(contactForm);
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'Gönderiliyor...';
            submitBtn.disabled = true;

            fetch('process_contact.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        contactForm.reset();
                    } else {
                        alert(data.message || 'Bir hata oluştu.');
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
    document.getElementById(groupId).classList.add('error');
}

function hideError(groupId) {
    document.getElementById(groupId).classList.remove('error');
}

// Projeleri AJAX ile Çekme
document.addEventListener('DOMContentLoaded', () => {
    const projectsContainer = document.getElementById('projectsContainer');

    if (projectsContainer) {
        fetch('fetch_projects.php')
            .then(response => response.json())
            .then(data => {
                projectsContainer.innerHTML = ''; // Yükleniyor yazısını temizle

                if (data.length > 0) {
                    data.forEach(project => {
                        // Resim yoksa varsayılan resim koy
                        const imageUrl = project.image_url ? project.image_url : 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=600&q=80';

                        // Etiketleri (Tags) rozetlere çevir
                        let tagsHtml = '';
                        if (project.tags) {
                            const tagsArray = project.tags.split(',');
                            tagsHtml = tagsArray.map(tag => `<span class="tag">${tag.trim()}</span>`).join('');
                        }

                        const projectHtml = `
                            <div class="project-card">
                                <img src="${imageUrl}" alt="${project.title}" class="project-image">
                                <div class="project-content">
                                    <h3 class="project-title">${project.title}</h3>
                                    <p class="project-desc">${project.description}</p>
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

/* ==========================================================================
   Typewriter (Daktilo) Efekti
   ========================================================================== */
const texts = [
    "Yazılım Mühendisliği Öğrencisi.",
    "Yazılım Mühendisi.",
    "Full-Stack Geliştirici.",
    "Web Tasarımcısı.",
    "Problem Çözücü."
];
let count = 0;
let index = 0;
let currentText = "";
let letter = "";
let isDeleting = false;

function type() {
    const typewriterElement = document.getElementById("typewriter");
    if (!typewriterElement) return;

    if (count === texts.length) {
        count = 0;
    }
    currentText = texts[count];

    if (isDeleting) {
        letter = currentText.slice(0, --index);
    } else {
        letter = currentText.slice(0, ++index);
    }

    typewriterElement.textContent = letter;

    let typeSpeed = isDeleting ? 50 : 100;

    if (!isDeleting && letter.length === currentText.length) {
        typeSpeed = 2000; // Kelime bitince 2 saniye bekle
        isDeleting = true;
    } else if (isDeleting && letter.length === 0) {
        isDeleting = false;
        count++;
        typeSpeed = 500; // Silinince yeni kelimeye geçmeden bekle
    }

    setTimeout(type, typeSpeed);
}

document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("typewriter")) {
        setTimeout(type, 1000);
    }
});

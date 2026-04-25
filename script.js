// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease-out reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Escape HTML
function escapeHtml(str) {
    if(!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}

// ========== SEEKER FUNCTIONS ==========

function loadAIMatches() {
    const container = document.getElementById('aiRecommendations');
    if(!container) return;
    
    container.innerHTML = '<div class="col-span-2 text-center py-8"><div class="loading-spinner mx-auto"></div><p class="mt-3">AI is finding best matches...</p></div>';
    
    fetch('api_ai_match.php')
        .then(r => r.json())
        .then(jobs => {
            if(jobs.length === 0) {
                container.innerHTML = '<div class="col-span-2 text-center py-8 text-white/80"><i class="fas fa-robot text-4xl mb-2 block"></i>Complete your profile for AI recommendations!</div>';
                return;
            }
            
            container.innerHTML = jobs.map(job => `
                <div class="bg-white text-gray-800 rounded-xl p-4 card-hover">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-lg">${escapeHtml(job.title)}</h3>
                        <span class="text-2xl font-bold text-purple-600">${job.score}%</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">${escapeHtml(job.company)}</p>
                    <div class="flex justify-between text-sm mb-3">
                        <span>📍 District ${job.district}</span>
                        <span>💰 ${job.salary_min} - ${job.salary_max} ₸</span>
                    </div>
                    <button onclick="applyToJob(${job.id})" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-paper-plane mr-1"></i>Apply Now
                    </button>
                </div>
            `).join('');
        })
        .catch(err => {
            container.innerHTML = '<div class="col-span-2 text-center text-white/80">Error loading recommendations</div>';
        });
}

function loadJobs() {
    const title = document.getElementById('searchTitle')?.value || '';
    const district = document.getElementById('filterDistrict')?.value || '';
    const type = document.getElementById('filterType')?.value || '';
    
    fetch(`api_get_jobs.php?title=${encodeURIComponent(title)}&district=${district}&type=${type}`)
        .then(r => r.json())
        .then(jobs => {
            const container = document.getElementById('jobsList');
            if(!container) return;
            
            if(jobs.length === 0) {
                container.innerHTML = '<div class="bg-white rounded-xl p-12 text-center text-gray-500"><i class="fas fa-search text-5xl mb-3 block"></i>No jobs found. Try different filters!</div>';
                return;
            }
            
            container.innerHTML = jobs.map(job => `
                <div class="bg-white rounded-xl shadow-md p-6 card-hover animate-fade-in-up">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="font-bold text-xl text-gray-800">${escapeHtml(job.title)}</h3>
                            <p class="text-gray-600 mb-2"><i class="fas fa-building mr-1"></i>${escapeHtml(job.company_name)}</p>
                            <div class="flex flex-wrap gap-3 text-sm text-gray-500 mb-3">
                                <span><i class="fas fa-map-marker-alt mr-1"></i>District ${job.microdistrict}</span>
                                <span><i class="fas fa-tenge mr-1"></i>${job.salary_min} - ${job.salary_max} ₸</span>
                                <span><i class="fas fa-clock mr-1"></i>${job.type === 'full' ? 'Full Time' : job.type === 'part' ? 'Part Time' : 'Freelance'}</span>
                            </div>
                            <p class="text-gray-700">${escapeHtml(job.description.substring(0, 120))}...</p>
                            ${job.skills_required ? `<div class="mt-2 flex flex-wrap gap-1"><span class="text-xs text-gray-500">Skills:</span> ${job.skills_required.split(',').map(s => `<span class="skill-tag bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full ml-1">${s.trim()}</span>`).join('')}</div>` : ''}
                        </div>
                        <div class="flex flex-col justify-center gap-2">
                            <button onclick="applyToJob(${job.id})" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-xl hover:shadow-lg transition">
                                <i class="fas fa-paper-plane mr-1"></i>Apply
                            </button>
                            <button onclick="saveJob(${job.id})" class="border-2 border-gray-300 text-gray-600 px-6 py-2 rounded-xl hover:bg-gray-50 transition">
                                <i class="far fa-bookmark mr-1"></i>Save
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        });
}

function applyToJob(jobId) {
    fetch('api_apply.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'job_id=' + jobId
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            showToast(`✓ Applied! AI Match: ${data.ai_score}%`, 'success');
            loadAIMatches();
            loadJobs();
        } else {
            showToast(data.error || 'Application failed', 'error');
        }
    });
}

function saveJob(jobId) {
    showToast('Job saved to favorites!', 'success');
}

// ========== EMPLOYER FUNCTIONS ==========

function showCreateJobModal() {
    document.getElementById('jobModal').classList.add('flex');
    document.getElementById('jobModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('jobModal').classList.add('hidden');
    document.getElementById('jobModal').classList.remove('flex');
}

function loadMyJobs() {
    fetch('api_get_my_jobs.php')
        .then(r => r.json())
        .then(jobs => {
            const container = document.getElementById('myJobs');
            if(!container) return;
            
            document.getElementById('activeJobsCount').innerText = jobs.filter(j => j.is_active == 1).length;
            document.getElementById('totalAppsCount').innerText = jobs.reduce((sum, j) => sum + parseInt(j.app_count || 0), 0);
            
            if(jobs.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-8"><i class="fas fa-briefcase text-4xl mb-2 block"></i>No jobs posted yet. Click "Post New Job" to start!</div>';
                return;
            }
            
            container.innerHTML = jobs.map(job => `
                <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-blue-300 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-lg">${escapeHtml(job.title)}</h3>
                                <span class="text-xs px-2 py-1 rounded-full ${job.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'}">
                                    ${job.is_active ? 'Active' : 'Closed'}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-3 text-sm text-gray-500 mb-2">
                                <span><i class="fas fa-map-marker-alt"></i> District ${job.microdistrict}</span>
                                <span><i class="fas fa-tenge"></i> ${job.salary_min} - ${job.salary_max} ₸</span>
                                <span><i class="fas fa-users"></i> ${job.app_count || 0} applications</span>
                            </div>
                            <p class="text-gray-600 text-sm">${escapeHtml(job.description.substring(0, 100))}...</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button onclick="toggleJobStatus(${job.id}, ${job.is_active ? 0 : 1})" class="text-sm ${job.is_active ? 'text-orange-600' : 'text-green-600'}">
                                <i class="fas ${job.is_active ? 'fa-eye-slash' : 'fa-eye'} mr-1"></i>${job.is_active ? 'Close' : 'Open'}
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        });
}

function loadResponses() {
    fetch('api_get_responses.php')
        .then(r => r.json())
        .then(responses => {
            const container = document.getElementById('responsesList');
            if(!container) return;
            
            document.getElementById('hiredCount').innerText = responses.filter(r => r.status === 'accepted').length;
            
            if(responses.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-500 py-8"><i class="fas fa-inbox text-4xl mb-2 block"></i>No applications yet</div>';
                return;
            }
            
            container.innerHTML = responses.map(resp => `
                <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-green-300 transition">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold">${escapeHtml(resp.fullname)}</h3>
                                ${resp.ai_score ? `<span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full"><i class="fas fa-robot mr-1"></i>AI Match: ${resp.ai_score}%</span>` : ''}
                            </div>
                            <p class="text-sm text-gray-600"><i class="fas fa-phone mr-1"></i>${resp.phone}</p>
                            <p class="text-sm text-gray-600">Applied for: <span class="font-semibold">${escapeHtml(resp.job_title)}</span></p>
                            <p class="text-sm">Skills: ${escapeHtml(resp.skills)} | Experience: ${resp.experience} years</p>
                        </div>
                        <div class="flex flex-col gap-2 min-w-[150px]">
                            <select onchange="updateStatus(${resp.id}, this.value)" class="border-2 rounded-xl px-3 py-2 text-sm">
                                <option value="pending" ${resp.status === 'pending' ? 'selected' : ''}>📋 Pending</option>
                                <option value="accepted" ${resp.status === 'accepted' ? 'selected' : ''}>✅ Accept</option>
                                <option value="rejected" ${resp.status === 'rejected' ? 'selected' : ''}>❌ Reject</option>
                            </select>
                            <button onclick="contactSeeker('${resp.phone}')" class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-green-700 transition">
                                <i class="fas fa-phone-alt mr-1"></i>Contact
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        });
}

function createJob() {
    const formData = new FormData();
    formData.append('title', document.getElementById('jobTitle').value);
    formData.append('description', document.getElementById('jobDesc').value);
    formData.append('skills', document.getElementById('jobSkills').value);
    formData.append('salary_min', document.getElementById('jobSalaryMin').value);
    formData.append('salary_max', document.getElementById('jobSalaryMax').value);
    formData.append('district', document.getElementById('jobDistrict').value);
    formData.append('type', document.getElementById('jobType').value);
    
    fetch('api_create_job.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            showToast('✓ Job posted successfully!', 'success');
            closeModal();
            loadMyJobs();
            document.getElementById('createJobForm').reset();
        } else {
            showToast('Failed to post job', 'error');
        }
    });
}

function toggleJobStatus(jobId, isActive) {
    showToast('Feature: Toggle job status', 'info');
}

function updateStatus(appId, status) {
    fetch('api_update_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `app_id=${appId}&status=${status}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            showToast(`✓ Status updated to ${status}`, 'success');
            loadResponses();
        }
    });
}

function contactSeeker(phone) {
    showToast(`Calling ${phone}...`, 'info');
    window.location.href = 'tel:' + phone;
}

// Form submission handlers
if(document.getElementById('createJobForm')) {
    document.getElementById('createJobForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createJob();
    });
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('jobModal');
    if(event.target === modal) {
        closeModal();
    }
}
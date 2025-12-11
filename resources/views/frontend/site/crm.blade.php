@extends('frontend.layouts.main')

@section('content')
{{-- ✅ Ikonkalar ishlashi uchun shu linkni qo'shdim --}}
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<style>
  :root {
    --yuksalish-orange: #F58025;
    --yuksalish-dark: #212529;
    --yuksalish-light: #fffbf8;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
  }

  /* Wrapper */
  .page-wrapper {
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
  }

  /* Header */
  .header-section {
    text-align: center;
    margin-bottom: 50px;
  }

  .main-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--yuksalish-dark);
    margin-bottom: 10px;
  }

  .sub-title {
    font-size: 1.1rem;
    color: #6c757d;
  }

  .highlight {
    color: var(--yuksalish-orange);
  }

  /* Role Cards Grid */
  .roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
  }

  /* Card Design */
  .role-card {
    background: white;
    border-radius: 20px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: block;
  }

  .role-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(245, 128, 37, 0.15);
    border-color: var(--yuksalish-orange);
  }

  /* Card Icon */
  .icon-container {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: var(--yuksalish-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--yuksalish-orange);
    transition: all 0.3s;
  }

  .role-card:hover .icon-container {
    background: var(--yuksalish-orange);
    color: white;
    transform: scale(1.1) rotate(5deg);
  }

  .role-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--yuksalish-dark);
    margin-bottom: 10px;
  }

  .role-desc {
    font-size: 0.9rem;
    color: #888;
    line-height: 1.5;
    margin-bottom: 25px;
  }

  /* Button inside Card */
  .enter-btn {
    display: inline-block;
    padding: 10px 25px;
    background: transparent;
    color: var(--yuksalish-orange);
    border: 2px solid var(--yuksalish-orange);
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s;
  }

  .role-card:hover .enter-btn {
    background: var(--yuksalish-orange);
    color: white;
  }

  /* Statistics Section */
  .stats-section {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    text-align: center;
  }

  .stat-item {
    padding: 10px;
  }

  .stat-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--yuksalish-dark);
    display: block;
    margin-bottom: 5px;
  }

  .stat-label {
    font-size: 1rem;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  /* Mobile Optimization */
  @media (max-width: 768px) {
    .main-title { font-size: 1.8rem; }
    .roles-grid { grid-template-columns: 1fr; gap: 20px; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .stat-value { font-size: 2rem; }
  }
</style>

<div class="page-wrapper">
  
  <!-- Header -->
  <div class="header-section">
    <h1 class="main-title">Andijon <span class="highlight">Yuksalish</span> Maktabi</h1>
    <p class="sub-title">Ta'lim jarayonlarini boshqarish tizimi (CRM)</p>
  </div>

  <!-- Role Selection Grid -->
  <div class="roles-grid">
    
    <!-- Admin -->
    <a href="{{ route('dashboard') }}" class="role-card">
      <div class="icon-container">
        <i class="ri-admin-line"></i>
      </div>
      <h3 class="role-title">Super Admin</h3>
      <p class="role-desc">Tizimni to'liq boshqarish, o'qituvchilar va o'quvchilarni nazorat qilish paneli.</p>
      <span class="enter-btn">Kirish <i class="ri-arrow-right-line"></i></span>
    </a>

    <!-- Teacher -->
    <a href="{{ route('teacher') }}" class="role-card">
      <div class="icon-container">
        <i class="ri-presentation-line"></i>
      </div>
      <h3 class="role-title">O'qituvchi</h3>
      <p class="role-desc">Dars jadvallari, baholash jurnali va o'quv materiallarini boshqarish.</p>
      <span class="enter-btn">Kirish <i class="ri-arrow-right-line"></i></span>
    </a>

    <!-- Koordinator -->
    <a href="{{ route('koordinator') }}" class="role-card">
      <div class="icon-container">
        <i class="ri-user-star-line"></i>
      </div>
      <h3 class="role-title">Koordinator</h3>
      <p class="role-desc">Sinflar nazorati, davomat va tashkiliy ishlarni boshqarish.</p>
      <span class="enter-btn">Kirish <i class="ri-arrow-right-line"></i></span>
    </a>

    <!-- Student -->
    <!-- <a href="{{ route('student') }}" class="role-card">
      <div class="icon-container">
        <i class="ri-graduation-cap-line"></i>
      </div>
      <h3 class="role-title">O'quvchi</h3>
      <p class="role-desc">Shaxsiy kabinet, baholar, uy vazifalari va reytingni kuzatish.</p>
      <span class="enter-btn">Kirish <i class="ri-arrow-right-line"></i></span>
    </a> -->

  </div>

  <!-- Statistics Section -->
  <div class="stats-section">
    <div class="stats-grid">
      
      <div class="stat-item">
        <span class="stat-value" data-count="{{ \App\Models\Users::where('user_type', \App\Models\Users::TYPE_STUDENT)->count() }}">0</span>
        <span class="stat-label">O'quvchilar</span>
      </div>

      <div class="stat-item">
        <span class="stat-value" data-count="{{ \App\Models\Users::where('user_type', \App\Models\Users::TYPE_TEACHER)->count() }}">0</span>
        <span class="stat-label">O'qituvchilar</span>
      </div>

      <div class="stat-item">
        <span class="stat-value" data-count="{{ \App\Models\Classes::count() }}">0</span>
        <span class="stat-label">Sinflar</span>
      </div>

      <div class="stat-item">
        <span class="stat-value" data-count="{{ \App\Models\Subjects::count() }}">0</span>
        <span class="stat-label">Fanlar</span>
      </div>

    </div>
  </div>

</div>

<!-- Animated Counter Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const counters = document.querySelectorAll(".stat-value");
  
  counters.forEach(counter => {
    const target = +counter.getAttribute("data-count");
    const duration = 1500; // 1.5 sekund
    const increment = target / (duration / 16); // 60fps

    let current = 0;
    const updateCounter = () => {
      current += increment;
      if (current < target) {
        counter.innerText = Math.ceil(current);
        requestAnimationFrame(updateCounter);
      } else {
        counter.innerText = target;
      }
    };
    updateCounter();
  });
});
</script>
@endsection
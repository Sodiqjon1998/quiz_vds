@extends('frontend.layouts.main')

@section('content')
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  html, body {
    width: 100%;
    overflow-x: hidden;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #ffffff;
    min-height: 100vh;
  }

  .page-wrapper {
    width: 100%;
    padding: 20px 16px;
    max-width: 1400px;
    margin: 0 auto;
  }

  /* ==================== HEADER ==================== */
  .header-section {
    text-align: center;
    margin-bottom: 32px;
    animation: slideDown 0.6s ease;
  }

  .main-title {
    color: #1a1a1a;
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 8px;
    line-height: 1.3;
  }

  .sub-title {
    color: #666666;
    font-size: 1rem;
    font-weight: 400;
  }

  /* ==================== CARDS GRID ==================== */
  .roles-grid {
    display: grid;
    gap: 20px;
    margin-bottom: 32px;
  }

  /* Mobile: 1 column */
  @media (max-width: 639px) {
    .roles-grid {
      grid-template-columns: 1fr;
    }
  }

  /* Tablet: 2 columns */
  @media (min-width: 640px) and (max-width: 1023px) {
    .roles-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  /* Desktop: 4 columns */
  @media (min-width: 1024px) {
    .roles-grid {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  /* ==================== ROLE CARD ==================== */
  .role-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 28px 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08), 
                0 4px 16px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    animation: fadeIn 0.6s ease;
    animation-fill-mode: both;
    border: 1px solid rgba(0, 0, 0, 0.04);
  }

  .role-card:nth-child(1) { animation-delay: 0.1s; }
  .role-card:nth-child(2) { animation-delay: 0.2s; }
  .role-card:nth-child(3) { animation-delay: 0.3s; }
  .role-card:nth-child(4) { animation-delay: 0.4s; }



  /* Touch feedback */
  .role-card:active {
    transform: scale(0.98);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  }

  /* Hover for desktop */
  @media (hover: hover) and (pointer: fine) {
    .role-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), 
                  0 16px 48px rgba(0, 0, 0, 0.08);
    }
  }

  /* ==================== CARD ICON ==================== */
  .icon-container {
    width: 80px;
    height: 80px;
    margin: 0 auto 18px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    transition: transform 0.3s ease;
    position: relative;
    font-size: 2.5rem;
  }

  /* Emoji Icons */
  .icon-admin::before {
    content: '👨‍💼';
  }

  .icon-teacher::before {
    content: '👨‍🏫';
  }

  .icon-coordinator::before {
    content: '📋';
  }

  .icon-student::before {
    content: '🎓';
  }

  .role-card:active .icon-container {
    transform: scale(0.95);
  }

  @media (hover: hover) and (pointer: fine) {
    .role-card:hover .icon-container {
      transform: scale(1.1) rotate(5deg);
    }
  }

  /* ==================== CARD TEXT ==================== */
  .role-title {
    font-size: 1.4rem;
    color: #1a1a1a;
    margin-bottom: 10px;
    font-weight: 700;
    line-height: 1.2;
  }

  .role-desc {
    color: #666666;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 20px;
  }

  /* ==================== BUTTON ==================== */
  .enter-btn {
    display: inline-block;
    padding: 12px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 28px;
    font-weight: 600;
    font-size: 1rem;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    -webkit-tap-highlight-color: transparent;
  }

  .enter-btn:active {
    transform: scale(0.96);
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.2);
  }

  @media (hover: hover) and (pointer: fine) {
    .enter-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
  }

  /* ==================== STATS SECTION ==================== */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    animation: fadeIn 0.6s ease 0.5s;
    animation-fill-mode: both;
  }

  @media (min-width: 640px) {
    .stats-grid {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  /* ==================== STAT CARD ==================== */
  .stat-item {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px 16px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08), 
                0 4px 16px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.04);
  }

  .stat-item:active {
    transform: scale(0.97);
  }

  @media (hover: hover) and (pointer: fine) {
    .stat-item:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1), 
                  0 12px 32px rgba(0, 0, 0, 0.06);
    }
  }

  .stat-value {
    display: block;
    color: #667eea;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 6px;
    line-height: 1;
  }

  .stat-name {
    color: #666666;
    font-size: 0.95rem;
    font-weight: 500;
  }

  /* ==================== ANIMATIONS ==================== */
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* ==================== MOBILE OPTIMIZATIONS ==================== */
  
  /* Extra small phones (< 360px) */
  @media (max-width: 359px) {
    .page-wrapper {
      padding: 16px 12px;
    }
    
    .main-title {
      font-size: 1.5rem;
    }
    
    .sub-title {
      font-size: 0.9rem;
    }
    
    .role-card {
      padding: 24px 16px;
    }
    
    .icon-container {
      width: 70px;
      height: 70px;
    }
    
    .role-title {
      font-size: 1.25rem;
    }
    
    .role-desc {
      font-size: 0.9rem;
    }
    
    .enter-btn {
      padding: 10px 24px;
      font-size: 0.95rem;
    }
    
    .stat-value {
      font-size: 2rem;
    }
  }

  /* Small phones (360px - 399px) */
  @media (min-width: 360px) and (max-width: 399px) {
    .page-wrapper {
      padding: 18px 14px;
    }
  }

  /* Medium phones (400px - 639px) */
  @media (min-width: 400px) and (max-width: 639px) {
    .page-wrapper {
      padding: 20px 16px;
    }
  }

  /* Tablets (640px - 1023px) */
  @media (min-width: 640px) and (max-width: 1023px) {
    .page-wrapper {
      padding: 32px 24px;
    }
    
    .header-section {
      margin-bottom: 40px;
    }
    
    .main-title {
      font-size: 2rem;
    }
    
    .sub-title {
      font-size: 1.1rem;
    }
    
    .roles-grid {
      gap: 24px;
    }
  }

  /* Desktop (1024px+) */
  @media (min-width: 1024px) {
    .page-wrapper {
      padding: 40px 32px;
    }
    
    .header-section {
      margin-bottom: 48px;
    }
    
    .main-title {
      font-size: 2.5rem;
    }
    
    .sub-title {
      font-size: 1.2rem;
    }
    
    .roles-grid {
      gap: 28px;
    }
    
    .role-card {
      padding: 32px 24px;
    }
    
    .icon-container {
      width: 90px;
      height: 90px;
    }
    
    .role-title {
      font-size: 1.5rem;
    }
    
    .stat-value {
      font-size: 3rem;
    }
  }

  /* Landscape mode for phones */
  @media (max-height: 500px) and (orientation: landscape) {
    .page-wrapper {
      padding: 12px;
    }
    
    .header-section {
      margin-bottom: 16px;
    }
    
    .main-title {
      font-size: 1.5rem;
    }
    
    .sub-title {
      font-size: 0.9rem;
    }
    
    .roles-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
      margin-bottom: 16px;
    }
    
    .role-card {
      padding: 16px 12px;
    }
    
    .icon-container {
      width: 60px;
      height: 60px;
      margin-bottom: 12px;
    }
    
    .role-title {
      font-size: 1.1rem;
      margin-bottom: 6px;
    }
    
    .role-desc {
      font-size: 0.85rem;
      margin-bottom: 12px;
    }
    
    .enter-btn {
      padding: 8px 20px;
      font-size: 0.9rem;
    }
    
    .stats-grid {
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
    }
    
    .stat-item {
      padding: 16px 12px;
    }
    
    .stat-value {
      font-size: 1.8rem;
    }
    
    .stat-name {
      font-size: 0.85rem;
    }
  }
</style>

<div class="page-wrapper">
  <!-- Header -->
  <header class="header-section">
    <h1 class="main-title">🎓 Andijon Yuksalish Maktabi</h1>
    <p class="sub-title">Ta'lim Boshqaruv Tizimi (CRM)</p>
  </header>

  <!-- Role Cards -->
  <section class="roles-grid">
    @php
      $roles = [
        [
          'icon_class' => 'icon-admin',
          'title' => 'Super Admin',
          'description' => 'Tizimni to\'liq boshqarish, o\'qituvchilar, o\'quvchilar va kurslarni nazorat qilish paneli',
          'route' => 'dashboard'
        ],
        [
          'icon_class' => 'icon-teacher',
          'title' => 'O\'qituvchi',
          'description' => 'Darslarni boshqarish, o\'quvchilarni baholash va dars jadvalini ko\'rish',
          'route' => 'teacher'
        ],
        [
          'icon_class' => 'icon-coordinator',
          'title' => 'Koordinator',
          'description' => 'Darslarni boshqarish, o\'quvchilarni baholash va dars jadvalini ko\'rish',
          'route' => 'koordinator'
        ],
        [
          'icon_class' => 'icon-student',
          'title' => 'O\'quvchi',
          'description' => 'Darslar jadvali, baholar, vazifalar va o\'quv materiallari bilan tanishish',
          'route' => 'student'
        ]
      ];
    @endphp

    @foreach($roles as $role)
      <article class="role-card">
        <div class="icon-container {{ $role['icon_class'] }}"></div>
        <h2 class="role-title">{{ $role['title'] }}</h2>
        <p class="role-desc">{{ $role['description'] }}</p>
        <a href="{{ route($role['route']) }}" class="enter-btn">Kirish</a>
      </article>
    @endforeach
  </section>

  <!-- Statistics -->
  <section class="stats-grid">
    @php
      $stats = [
        [
          'count' => App\Models\Users::where('user_type', App\Models\Users::TYPE_STUDENT)
                                    ->where('status', App\Models\Users::STATUS_ACTIVE)
                                    ->count(),
          'label' => 'O\'quvchilar'
        ],
        [
          'count' => App\Models\Users::where('user_type', App\Models\Users::TYPE_TEACHER)
                                    ->where('status', App\Models\Users::STATUS_ACTIVE)
                                    ->count(),
          'label' => 'O\'qituvchilar'
        ],
        [
          'count' => App\Models\Subjects::where('status', App\Models\Subjects::STATUS_ACTIVE)
                                       ->count(),
          'label' => 'Fanlar'
        ],
        [
          'count' => App\Models\Classes::where('status', App\Models\Classes::STATUS_ACTIVE)
                                      ->count(),
          'label' => 'Sinflar'
        ]
      ];
    @endphp

    @foreach($stats as $stat)
      <div class="stat-item">
        <span class="stat-value" data-count="{{ $stat['count'] }}">0</span>
        <p class="stat-name">{{ $stat['label'] }}</p>
      </div>
    @endforeach
  </section>
</div>

<script>
(function() {
  'use strict';
  
  // Counter animation
  function animateNumber(element, target) {
    const duration = 1200;
    const steps = 60;
    const increment = target / steps;
    const stepTime = duration / steps;
    let current = 0;
    
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        element.textContent = target + '+';
        clearInterval(timer);
      } else {
        element.textContent = Math.floor(current);
      }
    }, stepTime);
  }
  
  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    // Animate stat counters
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(element => {
      const targetValue = parseInt(element.dataset.count, 10);
      animateNumber(element, targetValue);
    });
    
    // Button click animation
    const buttons = document.querySelectorAll('.enter-btn');
    buttons.forEach(button => {
      button.addEventListener('click', function(e) {
        this.style.transform = 'scale(0.94)';
        setTimeout(() => {
          this.style.transform = '';
        }, 150);
      });
    });
  });
  
  // Touch feedback enhancements
  if ('ontouchstart' in window) {
    const touchElements = document.querySelectorAll('.role-card, .stat-item');
    
    touchElements.forEach(element => {
      element.addEventListener('touchstart', function() {
        this.style.transition = 'transform 0.15s ease';
      }, { passive: true });
      
      element.addEventListener('touchend', function() {
        setTimeout(() => {
          this.style.transition = 'all 0.3s ease';
        }, 150);
      }, { passive: true });
    });
  }
})();
</script>
@endsection
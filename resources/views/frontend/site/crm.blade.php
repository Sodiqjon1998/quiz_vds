@extends('frontend.layouts.main')

@section('content')
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .container {
    width: 100%;
    max-width: 1200px;
  }

  .header {
    text-align: center;
    margin-bottom: 50px;
    animation: fadeInDown 0.8s ease;
  }

  .header h1 {
    color: white;
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
  }

  .header p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.2rem;
    font-weight: 300;
  }

  .cards-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
  }

  .card {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.8s ease;
    animation-fill-mode: both;
  }

  .card:nth-child(1) {
    animation-delay: 0.1s;
  }

  .card:nth-child(2) {
    animation-delay: 0.2s;
  }

  .card:nth-child(3) {
    animation-delay: 0.3s;
  }

  .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, #667eea, #764ba2);
  }

  .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  }

  .card-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 25px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    transition: all 0.3s ease;
  }

  .card:hover .card-icon {
    transform: scale(1.1) rotate(5deg);
  }

  .card h2 {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
  }

  .card p {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 25px;
  }

  .card-btn {
    display: inline-block;
    padding: 12px 35px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  }

  .card-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    color: white;
  }

  .stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 40px;
    animation: fadeInUp 0.8s ease 0.4s;
    animation-fill-mode: both;
  }

  .stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .stat-card h3 {
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .stat-card p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
  }

  /* Loading skeleton */
  .skeleton {
    background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
  }

  @keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 768px) {
    .header h1 {
      font-size: 2rem;
    }

    .header p {
      font-size: 1rem;
    }

    .cards-wrapper {
      grid-template-columns: 1fr;
    }

    .stat-card h3 {
      font-size: 2rem;
    }
  }
</style>

<div class="container">
  <!-- Header Section -->
  <div class="header">
    <h1>🎓 Andijon Yuksalish Maktabi</h1>
    <p>Ta'lim Boshqaruv Tizimi (CRM)</p>
  </div>

  <!-- Main Cards -->
  <div class="cards-wrapper">
    @php
      $roles = [
        [
          'icon' => '👨‍💼',
          'title' => 'Super Admin',
          'description' => 'Tizimni to\'liq boshqarish, o\'qituvchilar, o\'quvchilar va kurslarni nazorat qilish paneli',
          'route' => 'dashboard',
          'type' => App\Models\Users::TYPE_ADMIN
        ],
        [
          'icon' => '👨‍🏫',
          'title' => 'O\'qituvchi',
          'description' => 'Darslarni boshqarish, o\'quvchilarni baholash va dars jadvalini ko\'rish',
          'route' => 'teacher',
          'type' => App\Models\Users::TYPE_TEACHER
        ],
        [
          'icon' => '👨‍🎓',
          'title' => 'O\'quvchi',
          'description' => 'Darslar jadvali, baholar, vazifalar va o\'quv materiallari bilan tanishish',
          'route' => 'student',
          'type' => App\Models\Users::TYPE_STUDENT
        ]
      ];
    @endphp

    @foreach($roles as $role)
      <div class="card">
        <div class="card-icon">
          {{ $role['icon'] }}
        </div>
        <h2>{{ $role['title'] }}</h2>
        <p>{{ $role['description'] }}</p>
        <a href="{{ route($role['route']) }}" class="card-btn">Kirish</a>
      </div>
    @endforeach
  </div>

  <!-- Statistics Section -->
  <div class="stats-section">
    @php
      // Dinamik statistika
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
      <div class="stat-card">
        <h3 class="stat-number" data-target="{{ $stat['count'] }}">0</h3>
        <p>{{ $stat['label'] }}</p>
      </div>
    @endforeach
  </div>
</div>

<script>
  // Counter animation
  function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        element.textContent = target + '+';
        clearInterval(timer);
      } else {
        element.textContent = Math.floor(current);
      }
    }, 30);
  }

  // Animate all counters when page loads
  document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
      const target = parseInt(counter.getAttribute('data-target'));
      animateCounter(counter, target);
    });
  });

  // Smooth scroll animation
  document.querySelectorAll('.card-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      this.style.transform = 'scale(0.95)';
      setTimeout(() => {
        this.style.transform = 'scale(1)';
      }, 100);
    });
  });

  // Parallax effect on mouse move
  document.addEventListener('mousemove', function(e) {
    const cards = document.querySelectorAll('.card');
    const x = e.clientX / window.innerWidth;
    const y = e.clientY / window.innerHeight;

    cards.forEach((card, index) => {
      const speed = 5 + (index * 2);
      const moveX = (x - 0.5) * speed;
      const moveY = (y - 0.5) * speed;
      card.style.transform = `translateX(${moveX}px) translateY(${moveY}px)`;
    });
  });

  // Reset card position on hover
  document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-10px)';
    });
    
    card.addEventListener('mouseleave', function() {
      this.style.transform = '';
    });
  });
</script>
@endsection
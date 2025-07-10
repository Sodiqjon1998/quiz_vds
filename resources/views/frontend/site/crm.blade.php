@extends('frontend.layouts.main')


@section('content')
<style>
  body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #f0f0f0;
  }

  .list-container {
    position: relative;
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .list {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
  }

  a{
    text-decoration: none;
    color: black;
  }

  .list-item {
    padding: 15px 20px;
    cursor: pointer;
    transition: color 0.3s ease, background-color 0.3s ease;
    z-index: 1;
    position: relative;
  }

  .list-item.active {
    color: white;
    background-color: #7cb9e8;
    /* Lighter blue for active item */
  }

  .highlight {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background-color: #3490dc;
    transition: 0.3s ease;
    opacity: 0;
    pointer-events: none;
  }

  a{
      width: 100%;
      height: 100%;
  }
</style>
<div class="list-container">
  <ul class="list">
    <li class="list-item">
      <a href="{{ route('dashboard') }}">
        Super admin
      </a>
    </li>
    <li class="list-item">
    <a href="{{route('teacher')}}">
        Teacher
      </a>
    </li>
    <li class="list-item">
      <a href="{{route('student')}}">
        Student
      </a>
    </li>
  </ul>
  <div class="highlight"></div>
</div>
<script>
  const listItems = document.querySelectorAll('.list-item');
  const highlight = document.querySelector('.highlight');
  let activeItem = null;

  function updateHighlight(item, isActive = false) {
    const rect = item.getBoundingClientRect();
    const containerRect = item.parentElement.getBoundingClientRect();

    highlight.style.width = `${rect.width}px`;
    highlight.style.left = `${rect.left - containerRect.left}px`;
    highlight.style.opacity = isActive ? '0' : '1';
  }

  function handleItemClick(e) {
    const clickedItem = e.target;

    if (activeItem === clickedItem) {
      // If clicking the active item, deactivate it
      activeItem.classList.remove('active');
      activeItem = null;
      highlight.style.opacity = '0';
    } else {
      // Deactivate the previous active item if exists
      if (activeItem) {
        activeItem.classList.remove('active');
      }

      // Activate the clicked item
      clickedItem.classList.add('active');
      activeItem = clickedItem;
      updateHighlight(clickedItem, true);
    }
  }

  function handleItemHover(e) {
    if (e.target !== activeItem) {
      updateHighlight(e.target, false);
    }
  }

  function handleItemLeave() {
    if (activeItem) {
      updateHighlight(activeItem, true);
    } else {
      highlight.style.opacity = '0';
    }
  }

  listItems.forEach(item => {
    item.addEventListener('click', handleItemClick);
    item.addEventListener('mouseenter', handleItemHover);
  });

  document.querySelector('.list-container').addEventListener('mouseleave', handleItemLeave);
</script>
@endsection

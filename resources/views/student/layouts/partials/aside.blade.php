<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" style="border-right: 1px solid #d3cece">


    <div class="app-brand demo ">
        <a href="{{ route('student') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span style="color:var(--bs-primary);">
                    <img src="{{asset('images/staticImages/logo.png')}}" alt="" style="width:50px">
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-semibold ms-2">Student</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
                    fill-opacity="0.9"></path>
                <path
                    d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
                    fill-opacity="0.4"></path>
            </svg>
        </a>

        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
                fill-opacity="0.9"></path>
            <path
                d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
                fill-opacity="0.4"></path>
        </svg>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>


    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ Route::is('student') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-home-smile-line"></i>
                <div data-i18n="Dashboards">Dashboards</div>
                <div class="badge bg-danger rounded-pill ms-auto">5</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="app-ecommerce-dashboard.html" class="menu-link">
                        <div data-i18n="eCommerce">eCommerce</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="dashboards-crm.html" class="menu-link">
                        <div data-i18n="CRM">CRM</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="index.html" class="menu-link">
                        <div data-i18n="Analytics">Analytics</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-logistics-dashboard.html" class="menu-link">
                        <div data-i18n="Logistics">Logistics</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-academy-dashboard.html" class="menu-link">
                        <div data-i18n="Academy">Academy</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- e-commerce-app menu start -->

        <!-- e-commerce-app menu end -->
        <!-- Academy menu start -->


{{--        <li class="menu-item {{ Route::is('backend.user*') ? 'active open' : '' }}">--}}
{{--            <a href="javascript:void(0);" class="menu-link menu-toggle">--}}
{{--                <i class="ri-group-line"></i>--}}
{{--                <div data-i18n="Foydalanuvchilar">Foydalanuvchilar</div>--}}
{{--            </a>--}}
{{--            <ul class="menu-sub">--}}
{{--                <li class="menu-item {{ Route::is('backend.user.index') ? 'active' : '' }}">--}}
{{--                    <a href="{{ route('backend.user.index') }}" class="menu-link">--}}
{{--                        <div data-i18n="O'qituvchilar va Kordinatorlar">O'qituvchilar va Kordinatorlar</div>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                --}}{{-- <li class="menu-item {{ Route::is('backend.user.koordinator') ? 'active' : '' }}">--}}
{{--                    <a href="{{ route('backend.user.koordinator') }}" class="menu-link">--}}
{{--                        <div data-i18n="Kordinatorlar">Kordinatorlar</div>--}}
{{--                    </a>--}}
{{--                </li> --}}
{{--            </ul>--}}
{{--        </li>--}}

        <li class="menu-item {{ Route::is('student.classes.index') ? 'active' : '' }}">
            <a href="{{route('student.quiz.index')}}" class="menu-link">
                <i class="ri-graduation-cap-fill"></i> &nbsp;
                <div data-i18n="Imtixonlar">Imtixonlar</div>
            </a>
        </li>

{{--        <li class="menu-item {{ Route::is('backend.subjects.index') ? 'active' : '' }}">--}}
{{--            <a href="{{route('backend.subjects.index')}}" class="menu-link">--}}
{{--                <i class="ri-contacts-book-fill"></i> &nbsp;--}}
{{--                <div data-i18n="Fanlar">Fanlar</div>--}}
{{--            </a>--}}
{{--        </li>--}}

{{--        <li class="menu-item {{ Route::is('backend.student.index') ? 'active' : '' }}">--}}
{{--            <a href="{{route('backend.student.index')}}" class="menu-link">--}}
{{--                <i class="ri-school-fill"></i> &nbsp;--}}
{{--                <div data-i18n="O'quvchilar ">O'quvchilar </div>--}}
{{--            </a>--}}
{{--        </li>--}}

        <li class="menu-item">
            <a href="https://demos.pixinvent.com/materialize-html-admin-template/documentation/" target="_blank"
               class="menu-link">
                <i class="menu-icon tf-icons ri-article-line"></i>
                <div data-i18n="Documentation">Documentation</div>
            </a>
        </li>
    </ul>

</aside>

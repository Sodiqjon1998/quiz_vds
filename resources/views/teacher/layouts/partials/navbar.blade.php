@php use App\Models\Teacher\Teacher; @endphp
@php use App\Models\User; @endphp
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
     id="layout-navbar" style="border-bottom: 1px solid #d3cece">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0   d-xl-none ">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="ri-menu-fill ri-22px"></i>
        </a>
    </div>


    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">


        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <strong>O'qituvchi: </strong> &nbsp; {{User::getStudentFullNameById(Auth::user()->id)}}
        </div>
        <!-- /Search -->


        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <li class="nav-item">
                <strong>Fan nomi:</strong> {{Teacher::subject(Auth::user()->subject_id)->name ?? "-----"}}
            </li>
            <!-- Language -->

            <!--/ Language -->

            <!-- Style Switcher -->

            <!-- / Style Switcher-->

            <!-- Quick links  -->

            <!-- Quick links -->

            <!-- Notification -->

            <!--/ Notification -->

            <!-- Teacher -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{asset('assets/img/avatars/1.png')}}" alt="" class="rounded-circle">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('teacher.user.setting') }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar avatar-online">
                                        <img src="{{asset('assets/img/avatars/1.png')}}" alt="" class="rounded-circle">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-medium d-block small"></strong> {{Teacher::subject(Auth::user()->subject_id)->name ?? "-----"}}</span>
                                    <small class="text-muted">O'qituvchi</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    {{-- <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="pages-profile-user.html">
                            <i class="ri-user-3-line ri-22px me-3"></i><span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="pages-account-settings-account.html">
                            <i class="ri-settings-4-line ri-22px me-3"></i><span class="align-middle">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="pages-account-settings-billing.html">
                            <span class="d-flex align-items-center align-middle">
                                <i class="flex-shrink-0 ri-file-text-line ri-22px me-3"></i>
                                <span class="flex-grow-1 align-middle">Billing</span>
                                <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger">4</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="pages-pricing.html">
                            <i class="ri-money-dollar-circle-line ri-22px me-3"></i><span
                                class="align-middle">Pricing</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="pages-faq.html">
                            <i class="ri-question-line ri-22px me-3"></i><span class="align-middle">FAQ</span>
                        </a>
                    </li> --}}
                    <li>
                        <div class="d-grid px-4 pt-2 pb-1">
                            <a class="btn btn-sm btn-danger d-flex" href="{{ route('teacher.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                               target="_blank">
                                <small class="align-middle">Logout</small>
                                <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
                            </a>
                            <form id="frm-logout" action="{{ route('backend.logout') }}" method="POST"
                                  style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                </ul>
            </li>
            <!--/ Teacher -->


        </ul>
    </div>


    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper  d-none">
        <input type="text" class="form-control search-input container-xxl border-0" placeholder="Search..."
               aria-label="Search...">
        <i class="ri-close-fill search-toggler cursor-pointer"></i>
    </div>


</nav>

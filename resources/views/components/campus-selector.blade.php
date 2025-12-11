{{--
    Composant : Sélecteur de Campus pour Super Admin
    À inclure dans le header/navbar du layout admin

    Usage: @include('components.campus-selector')
--}}

@if(Auth::check() && Auth::user()->isSuperAdmin())
    @php
        $allCampuses = \App\Models\Campus::orderBy('nomCampus')->get();
        $selectedCampusId = Session::get('selected_campus_id');
        $currentCampusName = 'Tous les campus';

        if ($selectedCampusId && $selectedCampusId !== 'all') {
            $currentCampus = $allCampuses->firstWhere('id', $selectedCampusId);
            $currentCampusName = $currentCampus ? $currentCampus->nomCampus : 'Tous les campus';
        }
    @endphp

    <div class="campus-selector">
        <div class="campus-dropdown">
            <button class="campus-btn" onclick="toggleCampusDropdown()">
                <i class="fas fa-building"></i>
                <span>{{ $currentCampusName }}</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="campus-dropdown-menu" id="campusDropdown">
                <div class="dropdown-header">
                    <i class="fas fa-crown"></i> Mode Super Admin
                </div>
                <form action="{{ route('super-admin.switch-campus') }}" method="POST">
                    @csrf
                    <input type="hidden" name="campus_id" value="all">
                    <button type="submit" class="dropdown-item {{ $selectedCampusId === 'all' || !$selectedCampusId ? 'active' : '' }}">
                        <i class="fas fa-globe"></i> Tous les campus
                    </button>
                </form>
                <div class="dropdown-divider"></div>
                @foreach($allCampuses as $campus)
                    <form action="{{ route('super-admin.switch-campus') }}" method="POST">
                        @csrf
                        <input type="hidden" name="campus_id" value="{{ $campus->id }}">
                        <button type="submit" class="dropdown-item {{ $selectedCampusId == $campus->id ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i> {{ $campus->nomCampus }}
                        </button>
                    </form>
                @endforeach
                <div class="dropdown-divider"></div>
                <a href="{{ route('super-admin.dashboard') }}" class="dropdown-item dropdown-item-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard Super Admin
                </a>
            </div>
        </div>
    </div>

    <style>
        .campus-selector {
            position: relative;
            margin-right: 1rem;
        }

        .campus-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        }

        .campus-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .campus-btn i.fa-chevron-down {
            font-size: 0.7rem;
            transition: transform 0.2s;
        }

        .campus-dropdown {
            position: relative;
        }

        .campus-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            z-index: 1001;
            overflow: hidden;
        }

        .campus-dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.65rem 1rem;
            background: none;
            border: none;
            color: #374151;
            font-family: inherit;
            font-size: 0.85rem;
            text-align: left;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
            color: #667eea;
        }

        .dropdown-item.active {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            font-weight: 600;
        }

        .dropdown-item i {
            width: 16px;
            text-align: center;
            opacity: 0.7;
        }

        .dropdown-item-link {
            color: #667eea;
            font-weight: 500;
        }

        .dropdown-divider {
            height: 1px;
            background: #e2e8f0;
            margin: 0.25rem 0;
        }

        /* Animation pour le chevron */
        .campus-dropdown-menu.show + .campus-btn i.fa-chevron-down,
        .campus-btn:focus i.fa-chevron-down {
            transform: rotate(180deg);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .campus-btn span {
                max-width: 100px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .campus-dropdown-menu {
                right: -50px;
            }
        }
    </style>

    <script>
        function toggleCampusDropdown() {
            const dropdown = document.getElementById('campusDropdown');
            dropdown.classList.toggle('show');
        }

        // Fermer le dropdown si on clique ailleurs
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('campusDropdown');
            const btn = document.querySelector('.campus-btn');

            if (dropdown && !dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
@endif

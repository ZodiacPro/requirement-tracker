<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="#" class="simple-text logo-mini">{{ __('BD') }}</a>
            <a href="#" class="simple-text logo-normal">{{ __('Black Dashboard') }}</a>
        </div>
        <ul class="nav">
            {{-- dashboard --}}
            <li @if ($pageSlug == 'dashboard') class="active " @endif>
                <a href="{{ route('home') }}">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>{{ __('Dashboard') }}</p>
                </a>
            </li>
            {{-- Area --}}
            <li @if ($pageSlug == 'area') class="active " @endif>
                <a href="{{ route('area.list') }}">
                    <i class="tim-icons icon-compass-05"></i>
                    <p>{{ __('Area') }}</p>
                </a>
            </li>
            {{-- User management --}}
            <li>
                <a data-toggle="collapse" href="#user" aria-expanded="true">
                    <i class="tim-icons icon-single-02"></i>
                    <span class="nav-link-text" >{{ __('User Management') }}</span>
                    <b class="caret mt-1"></b>
                </a>

                <div class="collapse
                @if($pageSlug == 'profile' || $pageSlug == 'users')
                show
                @endif
                " id="user">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'profile') class="active " @endif>
                            <a href="{{ route('profile.edit')  }}">
                                <i class="tim-icons icon-single-02"></i>
                                <p>{{ __('User Profile') }}</p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'users') class="active " @endif>
                            <a href="{{ route('user.index')  }}">
                                <i class="tim-icons icon-bullet-list-67"></i>
                                <p>{{ __('User Management') }}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            {{-- Site management --}}
            <li>
                    <a data-toggle="collapse" href="#site" aria-expanded="true">
                        <i class="tim-icons icon-puzzle-10"></i>
                        <span class="nav-link-text" >{{ __('Site Management') }}</span>
                        <b class="caret mt-1"></b>
                    </a>
    
                    <div class="collapse
                    @if($pageSlug == 'site')
                    show
                    @endif
                    " id="site">
                        <ul class="nav pl-4">
                            <li @if ($pageSlug == 'site') class="active " @endif>
                                <a href="{{ route('site.list')  }}">
                                    <i class="tim-icons icon-molecule-40"></i>
                                    <p>{{ __('Site List') }}</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
        </ul>
    </div>
</div>

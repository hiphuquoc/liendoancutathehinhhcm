<div class="breadcrumb-container">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                <!-- Home Item -->
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="/" itemprop="item" aria-label="Trang chủ">
                        <i class="fa-solid fa-house"></i>
                        <span itemprop="name">Trang chủ</span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>

                <!-- Dynamic Items -->
                @if(!empty($breadcrumb))
                    @foreach($breadcrumb as $key => $item)
                        @php
                            $title = $item->title ?? $item->name ?? '';
                            $slug  = $item->slug_full ?? $item->slug ?? '#';
                            $isLast = $loop->last;
                            $position = $key + 2;
                        @endphp
                        
                        <li class="breadcrumb-item {{ $isLast ? 'active' : '' }}" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            @if(!$isLast)
                                <a href="/{{ $slug }}" title="{{ $title }}" itemprop="item">
                                    <span itemprop="name">{{ $title }}</span>
                                </a>
                            @else
                                <span itemprop="name" title="{{ $title }}">{{ $title }}</span>
                                <!-- Current item doesn't need link usually, but if needed use meta -->
                                <meta itemprop="item" content="{{ url()->current() }}" /> 
                            @endif
                            <meta itemprop="position" content="{{ $position }}" />
                        </li>
                    @endforeach
                @endif
            </ol>
        </nav>
    </div>
</div>
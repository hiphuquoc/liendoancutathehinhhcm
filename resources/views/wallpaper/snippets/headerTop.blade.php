  @php
      /* lấy danh sách chuyên mục level 2 */
      $categoriesLv2  = \App\Models\CategoryBlog::select('*')
                          ->join('seo', 'seo.id', '=', 'category_blog.seo_id')
                          ->whereHas('seos.infoSeo', function ($query) use ($language) {
                              $query->where('language', $language)
                                  ->where('level', 2);
                          })
                          ->with(['seos.infoSeo' => function($query) use ($language) {
                              $query->where('language', $language);
                          }, 'seo', 'seos', 'blogs'])
                          ->orderBy('seo.ordering', 'DESC')
                          ->orderBy('seo.id', 'DESC')
                          ->get();
  @endphp
  
  <!-- header -->
  <header>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <nav class="main-nav" role="navigation">

            <!-- Mobile menu toggle button (hamburger/x icon) -->
            <input id="main-menu-state" type="checkbox" />
            <label class="main-menu-btn" for="main-menu-state"> 
              <span class="main-menu-btn-icon"></span> 
              Icon menu mobile
            </label>
            <h2 class="nav-brand">
              <a href="/">
                <img src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/logo-liendoancuta-1.webp" alt="logo liên đoàn cử tạ thể hình TPHCM" class="img-fluid" />
              </a>
            </h2>
            <!-- Sample menu definition -->
            <ul id="main-menu" class="sm sm-clean">
              @php
                $dataMenu         = config('main_'.env('APP_NAME').'.menu_public');
                $slugCurrent      = \App\Helpers\Url::getSlugCurrent();
                $arraySlugCurrent = explode('/', $slugCurrent);
              @endphp
              @foreach($dataMenu as $itemMenu)
                @php
                  $slugMenu = $itemMenu['slug'];
                  /* so sánh để active */
                  $active   = in_array($slugMenu, $arraySlugCurrent) ? 'active' : '';
                @endphp
                <li>
                  <a href="/{{ $slugMenu }}" class="{{ $active }}">{{ $itemMenu['name'] }}</a>
                  <!-- xử lý thêm menu con của tin-tuc -->
                  @if($slugMenu=='tin-tuc'&&!empty($categoriesLv2)&&$categoriesLv2->isNotEmpty())
                    <ul>
                      @foreach($categoriesLv2 as $cLv2)
                        <li><a href="/{{ $cLv2->slug_full ?? '' }}">{{ $cLv2->title ?? '' }}</a></li>
                      @endforeach
                    </ul>
                  @endif
                </li>
              @endforeach
              {{-- <li class="last_menu"><a href="#">Đăng ký ngay</a></li> --}}
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </header>
  <!-- end header -->